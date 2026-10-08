<?php

namespace Tests\Unit;

use App\Services\Page\PageHtmlAssetScanner;
use PHPUnit\Framework\TestCase;

class PageHtmlAssetScannerTest extends TestCase
{
    public function test_it_finds_local_images_and_ignores_remote_images(): void
    {
        $html = <<<'HTML'
<section data-section-name="Hero Section">
    <h2>Ignored because explicit name wins</h2>
    <img src="hero.webp" alt="Hero Guard" data-image-label="Main Hero">
    <picture><source srcset="hero-mobile.jpg 1x, hero-mobile-2.jpg 2x"></picture>
    <div style="background-image:url('hero-bg.png')"></div>
    <img src="https://example.com/remote.jpg" alt="Remote">
</section>
<section><h3>Services</h3><img src="service.avif"></section>
HTML;

        $assets = (new PageHtmlAssetScanner())->scan($html, '.cta{background:url(cta.jpeg)}');
        $byReference = collect($assets)->keyBy('reference');

        $this->assertCount(6, $assets);
        $this->assertSame('Hero Section', $byReference['hero.webp']['section_name']);
        $this->assertSame('Main Hero', $byReference['hero.webp']['image_label']);
        $this->assertSame('Hero Guard', $byReference['hero.webp']['alt_text']);
        $this->assertSame('Services', $byReference['service.avif']['section_name']);
        $this->assertSame('Custom CSS', $byReference['cta.jpeg']['section_name']);
        $this->assertArrayNotHasKey('https://example.com/remote.jpg', $byReference->all());
    }

    public function test_duplicate_reference_only_requires_one_upload_slot(): void
    {
        $html = '<section><h2>A</h2><img src="shared.webp"></section><section><h2>B</h2><img src="shared.webp"></section>';
        $assets = (new PageHtmlAssetScanner())->scan($html);
        $this->assertCount(1, $assets);
        $this->assertSame('A', $assets[0]['section_name']);
    }

    public function test_div_based_html_uses_nearest_preceding_heading(): void
    {
        $html = '<div><h2>Introduction</h2><img src="intro.webp"></div><div><h3>Why Choose Us</h3><img src="why.jpg"></div>';
        $assets = collect((new PageHtmlAssetScanner())->scan($html))->keyBy('reference');
        $this->assertSame('Introduction', $assets['intro.webp']['section_name']);
        $this->assertSame('Why Choose Us', $assets['why.jpg']['section_name']);
    }

    public function test_nested_sections_use_innermost_section_context(): void
    {
        $html = '<section data-section-name="Outer"><h2>Outer</h2><section data-section-name="Inner"><h3>Inner</h3><img src="inside.webp"></section></section>';
        $assets = (new PageHtmlAssetScanner())->scan($html);
        $this->assertSame('Inner', $assets[0]['section_name']);
    }

    public function test_lazy_load_preload_and_multiline_css_urls_are_found(): void
    {
        $html = <<<'HTML'
<h2>Gallery</h2>
<img data-src="lazy.webp" data-srcset="lazy-2.jpg 2x">
<link rel="preload" as="image" href="preload.avif">
<style>.hero{background-image:url(
  "multiline.png"
)}</style>
HTML;
        $assets = collect((new PageHtmlAssetScanner())->scan($html))->keyBy('reference');
        $this->assertTrue($assets->has('lazy.webp'));
        $this->assertTrue($assets->has('lazy-2.jpg'));
        $this->assertTrue($assets->has('preload.avif'));
        $this->assertTrue($assets->has('multiline.png'));
    }

    public function test_query_variants_are_deduplicated_for_one_slot(): void
    {
        $html = '<h2>Hero</h2><img src="hero.webp?v=1"><img src="hero.webp?v=2">';
        $assets = (new PageHtmlAssetScanner())->scan($html);
        $this->assertCount(1, $assets);
        $this->assertSame('hero.webp', $assets[0]['expected_filename']);
    }
}
