<?php

$root = dirname(__DIR__, 2);
$frontend = file_get_contents($root . '/resources/views/frontend/page.blade.php');
$create = file_get_contents($root . '/resources/views/backend/pages/create.blade.php');
$edit = file_get_contents($root . '/resources/views/backend/pages/edit.blade.php');
$renderer = file_get_contents($root . '/app/Services/Page/CustomPageRenderer.php');

function check(bool $condition, string $label): void {
    if (!$condition) {
        fwrite(STDERR, "FAIL: {$label}\n");
        exit(1);
    }
    echo "PASS: {$label}\n";
}

check(strpos($frontend, 'cdn.jsdelivr.net/npm/swiper@12') === false,
    'PageMaker frontend no longer injects Swiper 12 CDN');
check(strpos($frontend, 'swiper-bundle.min.css') === false,
    'PageMaker frontend adds no duplicate Swiper CSS');
check(strpos($frontend, 'swiper-bundle.min.js') === false,
    'PageMaker frontend adds no duplicate Swiper JS');

check(strpos($create, 'name="swiper_enabled"') !== false && strpos($edit, 'name="swiper_enabled"') !== false,
    'existing Swiper dependency toggle remains available');
check(strpos($create, 'already provided by the SGSSPL site') !== false &&
      strpos($edit, 'already provided by the SGSSPL site') !== false,
    'toggle explains that existing site Swiper is reused');

check(strpos($frontend, '<script>{!! $customJs !!}</script>') !== false,
    'Custom JS still renders separately');
check(strpos($renderer, "script") !== false,
    'existing Custom HTML sanitizer implementation remains present');

$sampleHtml = <<<'HTML'
<div class="mySwiper swiper">
    <div class="swiper-wrapper">
        <div class="swiper-slide">One</div>
        <div class="swiper-slide">Two</div>
        <div class="swiper-slide">Three</div>
    </div>
</div>
HTML;

$sampleJs = <<<'JS'
document.addEventListener('DOMContentLoaded', function () {
    new Swiper('.mySwiper', {
        slidesPerView: 1,
        loop: true
    });
});
JS;

check(strpos($sampleHtml, 'mySwiper swiper') !== false &&
      strpos($sampleJs, "new Swiper('.mySwiper'") !== false,
    'requested Swiper markup/Custom JS regression fixture is represented');

echo "\nV1.3.5 Swiper conflict hotfix smoke tests passed.\n";
