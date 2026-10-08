<?php

require __DIR__ . '/../../vendor/autoload.php';

use App\Models\Page;
use App\Services\Page\CustomPageRenderer;

function pm135_check(bool $condition, string $label): void
{
    if (!$condition) {
        fwrite(STDERR, "FAIL: {$label}\n");
        exit(1);
    }
    echo "PASS: {$label}\n";
}

$root = dirname(__DIR__, 2);
$model = file_get_contents($root . '/app/Models/Page.php');
$controller = file_get_contents($root . '/app/Http/Controllers/PageController.php');
$create = file_get_contents($root . '/resources/views/backend/pages/create.blade.php');
$edit = file_get_contents($root . '/resources/views/backend/pages/edit.blade.php');
$front = file_get_contents($root . '/resources/views/frontend/page.blade.php');
$migration = file_get_contents($root . '/database/migrations/2026_08_28_204700_add_custom_js_to_pages_table.php');
$rendererSource = file_get_contents($root . '/app/Services/Page/CustomPageRenderer.php');

pm135_check(str_contains($migration, "longText('custom_js')"), 'migration adds custom_js');
pm135_check(str_contains($migration, "boolean('swiper_enabled')"), 'migration adds Swiper page toggle');
pm135_check(str_contains($model, "'custom_js'") && str_contains($model, "'swiper_enabled'"), 'Page model allows new fields');
pm135_check(substr_count($controller, "'custom_js' => 'nullable|string'") === 2, 'store and update validate custom_js');
pm135_check(substr_count($controller, "boolean('swiper_enabled')") === 2, 'store and update normalize Swiper checkbox');
pm135_check(str_contains($create, 'name="custom_js"') && str_contains($edit, 'name="custom_js"'), 'create/edit UI contains Custom JavaScript field');
pm135_check(str_contains($create, 'name="swiper_enabled"') && str_contains($edit, 'name="swiper_enabled"'), 'create/edit UI contains Swiper dependency toggle');
pm135_check(str_contains($edit, "old('custom_js', \$page->custom_js)"), 'edit UI reloads saved custom_js');
pm135_check(!str_contains($front, 'swiper@12/swiper-bundle.min.css') && !str_contains($front, 'swiper@12/swiper-bundle.min.js'), 'frontend reuses site Swiper without injecting duplicate CDN dependency');
pm135_check(str_contains($front, '<script>{!! $customJs !!}</script>'), 'frontend creates script wrapper for custom_js');
pm135_check(str_contains($rendererSource, "preg_replace('#</script#i'"), 'renderer protects generated script wrapper from literal closing tag');

$page = new class extends Page {
    public function __get($key)
    {
        if ($key === 'custom_js') {
            return "console.log('PageMaker Custom JS works');\nconst marker = '</script>';";
        }
        return parent::__get($key);
    }
};
$renderedJs = (new CustomPageRenderer())->renderJs($page);
pm135_check(str_contains($renderedJs, "console.log('PageMaker Custom JS works')"), 'Custom JS source is preserved for execution');
pm135_check(!str_contains(strtolower($renderedJs), '</script'), 'literal closing script sequence cannot break wrapper');

// Existing HTML sanitizer contract must remain in place; these exact removal rules are
// also exercised by the prior V1.2/V1.3 hardening smoke suites.
pm135_check(str_contains($rendererSource, "preg_replace('#<(script|iframe|object|embed|svg|math|template)"), 'existing Custom HTML script sanitizer remains present');
pm135_check(str_contains($rendererSource, "on[a-z][\\w:-]*"), 'existing inline event-handler protection remains present');

echo "\nPageMaker V1.3.5 Custom JS smoke tests passed.\n";
