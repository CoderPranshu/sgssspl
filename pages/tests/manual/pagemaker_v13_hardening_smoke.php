<?php

require __DIR__ . '/../../vendor/autoload.php';

use App\Services\Page\CustomPageRenderer;

function check(bool $condition, string $label): void {
    if (!$condition) {
        fwrite(STDERR, "FAIL: {$label}\n");
        exit(1);
    }
    echo "PASS: {$label}\n";
}

$renderer = new CustomPageRenderer();
$ref = new ReflectionClass($renderer);
$sanitize = $ref->getMethod('sanitizeHtml');
$sanitize->setAccessible(true);

$dirty = <<<'HTML'
<picture>
  <source srcset="javascript:alert(1) 1x, hero-small.webp 640w, https://cdn.example.com/hero.webp 2x">
  <img src="hero.webp" srcset="java&#x73;cript:alert(2) 1x, hero.webp 1x, hero@2x.webp 2x" data-srcset="vbscript:msgbox(1) 1x, lazy.webp 1x" alt="Hero">
</picture>
HTML;
$clean = $sanitize->invoke($renderer, $dirty);
$decoded = strtolower(html_entity_decode($clean, ENT_QUOTES | ENT_HTML5));

check(!str_contains($decoded, 'javascript:'), 'javascript URLs are removed from srcset candidates');
check(!str_contains($decoded, 'vbscript:'), 'vbscript URLs are removed from data-srcset candidates');
check(str_contains($clean, 'hero-small.webp 640w'), 'safe width srcset descriptor survives');
check(str_contains($clean, 'https://cdn.example.com/hero.webp 2x'), 'safe HTTPS srcset candidate survives');
check(str_contains($clean, 'hero.webp 1x') && str_contains($clean, 'hero@2x.webp 2x'), 'safe density srcset candidates survive');
check(str_contains($clean, 'data-srcset="lazy.webp 1x"'), 'safe data-srcset candidate survives');

$pageController = file_get_contents(__DIR__ . '/../../app/Http/Controllers/PageController.php');
check(!str_contains($pageController, 'getClientOriginalExtension()'), 'PageController no longer trusts client-provided extensions');
check(substr_count($pageController, '->extension()') >= 4, 'page and breadcrumb uploads use server-detected extensions in create/update');
check(substr_count($pageController, 'bin2hex(random_bytes(16))') >= 4, 'page and breadcrumb uploads use random server filenames');

echo "\nPageMaker V1.3 hardening smoke tests passed.\n";
