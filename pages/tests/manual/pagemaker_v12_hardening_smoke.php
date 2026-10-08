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
<meta http-equiv="refresh" content="0;url=https://evil.test">
<link rel="stylesheet" href="https://evil.test/x.css">
<base href="https://evil.test/">
<form action="https://evil.test"><div class="lead"><h2>Hello</h2><input name="x"><p style="color:red">World</p></div></form>
<a href="java&#x73;cript:alert(1)" onclick="alert(2)">Bad</a>
<a href="https://example.com" target="_blank">Good</a>
<img src="hero.webp" alt="Hero" onerror="alert(3)">
<section data-section-name="Intro"><style>.hero{color:red}</style><div class="hero">OK</div></section>
HTML;
$clean = $sanitize->invoke($renderer, $dirty);
check(!preg_match('/<(meta|link|base|form|input|iframe|object|embed|script|svg|math)\b/i', $clean), 'document-level and active tags are removed');
check(!preg_match('/\son[a-z]+\s*=/i', $clean), 'event attributes are removed');
check(!str_contains(strtolower(html_entity_decode($clean)), 'javascript:'), 'executable URI schemes are removed');
check(str_contains($clean, '<h2>Hello</h2>') && str_contains($clean, '<p style="color:red">World</p>'), 'normal SEO content inside stripped form survives');
check(str_contains($clean, 'href="https://example.com"') && str_contains($clean, 'rel="noopener noreferrer"'), 'safe external links survive with noopener');
check(str_contains($clean, '<img src="hero.webp" alt="Hero"'), 'safe images survive');
check(str_contains($clean, 'data-section-name="Intro"'), 'PageMaker structured metadata survives');

$namespace = $ref->getMethod('namespaceKeyframes');
$namespace->setAccessible(true);
$scopeBlock = $ref->getMethod('scopeBlock');
$scopeBlock->setAccessible(true);
$css = '@font-face{font-family:X;src:url(x.woff2)} @property --angle{syntax:"<angle>";initial-value:0deg;inherits:false} @keyframes fade{from{opacity:0}to{opacity:1}} .hero,.card:not(.a,.b){animation:fade 1s;color:red}';
$namespaced = $namespace->invoke($renderer, $css, '#seo-custom-page-7');
$scoped = $scopeBlock->invoke($renderer, $namespaced, '#seo-custom-page-7');
check(!str_contains(strtolower($scoped), '@font-face') && !str_contains(strtolower($scoped), '@property'), 'document-global CSS at-rules are removed');
check(str_contains($scoped, '@keyframes pm-seo-custom-page-7-fade'), 'keyframe names are namespaced per page');
check(str_contains($scoped, 'animation:pm-seo-custom-page-7-fade 1s'), 'animation references follow namespaced keyframes');
check(str_contains($scoped, '#seo-custom-page-7 .card:not(.a,.b)'), 'complex selectors remain intact and scoped');

$routes = file_get_contents(__DIR__ . '/../../routes/web.php');
check(str_contains($routes, "Route::middleware(['auth', 'pagemaker'])->group"), 'PageMaker routes require auth plus PageMaker authorization');
$bootstrap = file_get_contents(__DIR__ . '/../../bootstrap/app.php');
check(str_contains($bootstrap, "'pagemaker' => \\App\\Http\\Middleware\\EnsurePageMakerAccess::class"), 'PageMaker authorization middleware is registered');
$legacy = file_get_contents(__DIR__ . '/../../app/Http/Controllers/BlogController.php');
check(str_contains($legacy, "'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:10240'"), 'legacy PageMaker upload validates image MIME and size');
check(str_contains($legacy, 'bin2hex(random_bytes(16))'), 'legacy upload uses server-generated filenames');

echo "\nPageMaker V1.2 hardening smoke tests passed.\n";
