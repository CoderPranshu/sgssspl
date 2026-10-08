<?php
require __DIR__ . '/../../vendor/autoload.php';
use App\Services\Page\PageHtmlAssetScanner;
function check(bool $condition, string $label): void { if (!$condition) { fwrite(STDERR, "FAIL: {$label}\n"); exit(1); } echo "PASS: {$label}\n"; }
$scanner = new PageHtmlAssetScanner();
set_error_handler(static function (int $severity, string $message, string $file, int $line): never { throw new ErrorException($message, 0, $severity, $file, $line); });
try {
$assets = $scanner->scan(<<<'HTML'
<section data-section-name=""><h2>Scanner Regression</h2><img src="hero.webp" alt=""><img data-src='lazy.webp' data-image-label=''><source srcset="small.webp 640w, large.webp 2x"><img src=plain.webp alt=Plain></section>
HTML);
} finally { restore_error_handler(); }
$refs = array_column($assets, 'reference');
check(in_array('hero.webp',$refs,true), 'empty double-quoted attribute does not crash scanner');
check(in_array('lazy.webp',$refs,true), 'empty single-quoted attribute does not crash scanner');
check(in_array('small.webp',$refs,true) && in_array('large.webp',$refs,true), 'srcset still scans after empty attributes');
check(in_array('plain.webp',$refs,true), 'unquoted attributes still scan');
check(count($assets) === 5, 'all expected image references are detected');
echo "\nPageMaker V1.3.1 scanner hotfix smoke tests passed.\n";
