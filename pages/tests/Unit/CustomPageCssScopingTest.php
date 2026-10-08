<?php

namespace Tests\Unit;

use App\Services\Page\CustomPageRenderer;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class CustomPageCssScopingTest extends TestCase
{
    private function privateMethod(string $name): \ReflectionMethod
    {
        $method = (new ReflectionClass(new CustomPageRenderer()))->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

    public function test_common_selectors_are_scoped_and_keyframes_are_preserved(): void
    {
        $renderer = new CustomPageRenderer();
        $method = $this->privateMethod('scopeBlock');
        $result = $method->invoke($renderer,
            '.container,.btn:hover{color:red}@media(max-width:768px){h2{font-size:20px}}@keyframes fade{from{opacity:0}to{opacity:1}}body .hero{margin:0}',
            '#seo-custom-page-7'
        );

        $this->assertStringContainsString('#seo-custom-page-7 .container', $result);
        $this->assertStringContainsString('#seo-custom-page-7 .btn:hover', $result);
        $this->assertStringContainsString('@media(max-width:768px){#seo-custom-page-7 h2', $result);
        $this->assertStringContainsString('@keyframes fade{from{opacity:0}to{opacity:1}}', $result);
        $this->assertStringContainsString('#seo-custom-page-7 .hero{margin:0}', $result);
    }

    public function test_import_and_charset_cannot_un_scope_the_next_selector(): void
    {
        $renderer = new CustomPageRenderer();
        $method = $this->privateMethod('scopeBlock');
        $result = $method->invoke($renderer,
            '@charset "UTF-8"; @import url("https://fonts.example/x.css"); .hero{color:red}',
            '#seo-custom-page-7'
        );

        $this->assertStringNotContainsString('@import', $result);
        $this->assertStringNotContainsString('@charset', $result);
        $this->assertStringContainsString('#seo-custom-page-7 .hero{color:red}', $result);
    }

    public function test_selector_commas_inside_not_and_attributes_are_not_split(): void
    {
        $renderer = new CustomPageRenderer();
        $method = $this->privateMethod('scopeBlock');
        $result = $method->invoke($renderer,
            '.card:not(.a,.b), [data-label="a,b"]{display:block}',
            '#seo-custom-page-7'
        );

        $this->assertStringContainsString('#seo-custom-page-7 .card:not(.a,.b)', $result);
        $this->assertStringContainsString('#seo-custom-page-7 [data-label="a,b"]', $result);
    }

    public function test_sanitizer_removes_active_content_and_script_uris(): void
    {
        $renderer = new CustomPageRenderer();
        $method = $this->privateMethod('sanitizeHtml');
        $html = '<svg/onload=alert(1)><img src=x/onerror=alert(2)><a href="java&#x73;cript:alert(3)">x</a><iframe src="https://evil.test"></iframe><p onclick="x()">ok</p>';
        $result = $method->invoke($renderer, $html);

        $this->assertStringNotContainsString('onload', strtolower($result));
        $this->assertStringNotContainsString('onerror', strtolower($result));
        $this->assertStringNotContainsString('onclick', strtolower($result));
        $this->assertStringNotContainsString('javascript:', strtolower(html_entity_decode($result)));
        $this->assertStringNotContainsString('<iframe', strtolower($result));
        $this->assertStringNotContainsString('<svg', strtolower($result));
    }
}
