<?php

require dirname(__DIR__, 2) . '/vendor/autoload.php';

use App\Services\Page\CustomPageRenderer;
use App\Services\Page\PageHtmlAssetScanner;

function ok(bool $condition, string $message): void {
    if (!$condition) {
        fwrite(STDERR, "FAIL: {$message}\n");
        exit(1);
    }
    echo "PASS: {$message}\n";
}

$scanner = new PageHtmlAssetScanner();
$html = <<<'HTML'
<div><h2>Intro</h2><img src="intro.webp" alt="Intro"></div>
<div><h3>Why Us</h3><img data-src="why.jpg"></div>
<section data-section-name="Outer"><section data-section-name="Inner"><img src="nested.avif"></section></section>
<link rel="preload" as="image" href="preload.png">
<img src="https://example.com/remote.jpg">
HTML;
$assets = collect($scanner->scan($html, ".x{background:url(\n'bg.jpeg'\n)}"))->keyBy('reference');
ok($assets->has('intro.webp') && $assets['intro.webp']['section_name'] === 'Intro', 'nearest heading groups div-based image');
ok($assets->has('why.jpg') && $assets['why.jpg']['section_name'] === 'Why Us', 'data-src is detected');
ok($assets->has('nested.avif') && $assets['nested.avif']['section_name'] === 'Inner', 'nested section uses innermost context');
ok($assets->has('preload.png'), 'image preload is detected');
ok($assets->has('bg.jpeg') && $assets['bg.jpeg']['section_name'] === 'Custom CSS', 'multiline CSS url is detected');
ok(!$assets->has('https://example.com/remote.jpg'), 'remote image is ignored');

$renderer = new CustomPageRenderer();
$reflection = new ReflectionClass($renderer);
$scope = $reflection->getMethod('scopeBlock');
$scope->setAccessible(true);
$scoped = $scope->invoke($renderer, '@charset "UTF-8"; @import url(x.css); .card:not(.a,.b),[data-label="a,b"]{color:red}@media(max-width:800px){h2{font-size:20px}}', '#seo-custom-page-7');
ok(!str_contains($scoped, '@import') && !str_contains($scoped, '@charset'), 'brace-less global at-rules are removed');
ok(str_contains($scoped, '#seo-custom-page-7 .card:not(.a,.b)'), 'complex :not selector remains intact and scoped');
ok(str_contains($scoped, '#seo-custom-page-7 [data-label="a,b"]'), 'comma inside attribute selector remains intact and scoped');
ok(str_contains($scoped, '@media(max-width:800px){#seo-custom-page-7 h2'), 'nested media selectors are scoped');

$sanitize = $reflection->getMethod('sanitizeHtml');
$sanitize->setAccessible(true);
$dirty = '<svg/onload=alert(1)><img src=x/onerror=alert(2)><a href="java&#x73;cript:alert(3)">x</a><iframe src="https://evil.test"></iframe><p onclick="x()">ok</p>';
$clean = $sanitize->invoke($renderer, $dirty);
ok(!preg_match('/on(load|error|click)/i', $clean), 'slash and whitespace event handlers are removed');
ok(!str_contains(strtolower(html_entity_decode($clean)), 'javascript:'), 'javascript URI is removed');
ok(!preg_match('/<(svg|iframe|object|embed)\b/i', $clean), 'active embedded elements are removed');

$replace = $reflection->getMethod('replaceAssetReferences');
$replace->setAccessible(true);
$mapped = $replace->invoke($renderer, '<img src="hero.webp?v=2"><img src="https://cdn.test/hero.webp"><source srcset="hero.webp?v=3 1x, other.jpg 2x"><div style="background:url(hero.webp?v=9)"></div>', ['hero.webp?v=1' => '/uploads/pages/1/hero.webp']);
ok(substr_count($mapped, '/uploads/pages/1/hero.webp') === 3, 'query variants resolve to the uploaded local asset');
ok(str_contains($mapped, 'https://cdn.test/hero.webp'), 'same-named remote URL is not rewritten');

echo "\nPageMaker V1.1 standalone smoke tests passed.\n";
