<?php

namespace App\Services\Page;

class PageHtmlAssetScanner
{
    private array $extensions = ['jpg', 'jpeg', 'png', 'webp', 'avif'];

    public function scan(?string $html, ?string $css = null): array
    {
        $assets = [];
        $html = (string) $html;

        if (trim($html) !== '') {
            $sections = $this->sectionSpans($html);
            $headings = $this->headingPositions($html);

            foreach ($this->htmlReferences($html) as $item) {
                $section = $this->sectionForOffset($sections, $item['offset']);
                $sectionName = $section['name'] ?? $this->nearestHeading($headings, $item['offset']) ?? 'Page Content';
                $this->addReference(
                    $assets,
                    $item['reference'],
                    $sectionName,
                    $item['alt'] ?? null,
                    $item['label'] ?? null
                );
            }
        }

        foreach ($this->cssReferencesWithOffsets((string) $css) as $item) {
            $this->addReference($assets, $item['reference'], 'Custom CSS', null, null);
        }

        return array_values($assets);
    }

    /**
     * Return image references with source offsets so section/heading context can be resolved.
     */
    private function htmlReferences(string $html): array
    {
        $found = [];

        if (preg_match_all('/<(img|source)\b[^>]*>/is', $html, $tags, PREG_OFFSET_CAPTURE)) {
            foreach ($tags[0] as $tagMatch) {
                [$tag, $offset] = $tagMatch;
                $attributes = $this->attributes($tag);
                $alt = isset($attributes['alt']) ? trim($attributes['alt']) : null;
                $label = isset($attributes['data-image-label']) ? trim($attributes['data-image-label']) : null;

                foreach (['src', 'data-src', 'data-lazy-src'] as $attribute) {
                    if (!empty($attributes[$attribute])) {
                        $found[] = compact('offset') + [
                            'reference' => $attributes[$attribute],
                            'alt' => $alt,
                            'label' => $label,
                        ];
                    }
                }

                foreach (['srcset', 'data-srcset'] as $attribute) {
                    if (!empty($attributes[$attribute])) {
                        foreach ($this->srcsetReferences($attributes[$attribute]) as $reference) {
                            $found[] = compact('offset', 'reference') + ['alt' => $alt, 'label' => $label];
                        }
                    }
                }
            }
        }

        // Common preload markup: <link rel="preload" as="image" href="hero.webp">
        if (preg_match_all('/<link\b[^>]*>/is', $html, $links, PREG_OFFSET_CAPTURE)) {
            foreach ($links[0] as $linkMatch) {
                [$tag, $offset] = $linkMatch;
                $attributes = $this->attributes($tag);
                if (strtolower($attributes['rel'] ?? '') === 'preload'
                    && strtolower($attributes['as'] ?? '') === 'image'
                    && !empty($attributes['href'])) {
                    $found[] = ['offset' => $offset, 'reference' => $attributes['href'], 'alt' => null, 'label' => 'Preloaded image'];
                }
            }
        }

        // CSS url(...) in inline styles and <style> blocks. DOTALL handles formatted multiline url().
        foreach ($this->cssReferencesWithOffsets($html) as $item) {
            $found[] = ['offset' => $item['offset'], 'reference' => $item['reference'], 'alt' => null, 'label' => null];
        }

        usort($found, fn ($a, $b) => $a['offset'] <=> $b['offset']);
        return $found;
    }

    /**
     * Parse nested <section> spans using a stack instead of a regex pair matcher.
     */
    private function sectionSpans(string $html): array
    {
        $spans = [];
        $stack = [];

        if (!preg_match_all('/<\/?section\b[^>]*>/is', $html, $matches, PREG_OFFSET_CAPTURE)) {
            return [];
        }

        foreach ($matches[0] as $match) {
            [$tag, $offset] = $match;
            $isClosing = preg_match('/^<\s*\/section/i', $tag) === 1;

            if (!$isClosing) {
                $stack[] = [
                    'start' => $offset,
                    'content_start' => $offset + strlen($tag),
                    'attributes' => $this->attributes($tag),
                ];
                continue;
            }

            $open = array_pop($stack);
            if (!$open) continue;

            $content = substr($html, $open['content_start'], max(0, $offset - $open['content_start']));
            $spans[] = [
                'start' => $open['start'],
                'end' => $offset + strlen($tag),
                'name' => $this->sectionNameFromData($open['attributes'], $content),
            ];
        }

        // Gracefully treat unclosed sections as extending to the end of the document.
        foreach ($stack as $open) {
            $content = substr($html, $open['content_start']);
            $spans[] = [
                'start' => $open['start'],
                'end' => strlen($html),
                'name' => $this->sectionNameFromData($open['attributes'], $content),
            ];
        }

        return $spans;
    }

    private function sectionNameFromData(array $attrs, string $content): string
    {
        if (!empty($attrs['data-section-name'])) return trim($attrs['data-section-name']);

        foreach (['h2', 'h3', 'h1', 'h4'] as $tag) {
            if (preg_match('/<' . $tag . '\b[^>]*>(.*?)<\/' . $tag . '\s*>/is', $content, $match)) {
                $text = $this->cleanText($match[1]);
                if ($text !== '') return mb_substr($text, 0, 180);
            }
        }

        if (!empty($attrs['id'])) return trim($attrs['id']);
        if (!empty($attrs['class'])) return trim(preg_split('/\s+/', trim($attrs['class']))[0] ?? 'Unnamed Section');
        return 'Unnamed Section';
    }

    private function headingPositions(string $html): array
    {
        $headings = [];
        if (preg_match_all('/<(h[1-4])\b[^>]*>(.*?)<\/\1\s*>/is', $html, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
            foreach ($matches as $match) {
                $text = $this->cleanText($match[2][0] ?? '');
                if ($text !== '') {
                    $headings[] = [
                        'offset' => $match[0][1],
                        'level' => strtolower($match[1][0]),
                        'text' => mb_substr($text, 0, 180),
                    ];
                }
            }
        }
        return $headings;
    }

    private function nearestHeading(array $headings, int $offset): ?string
    {
        $best = null;
        foreach ($headings as $heading) {
            if ($heading['offset'] > $offset) break;
            $best = $heading['text'];
        }
        return $best;
    }

    private function sectionForOffset(array $sections, int $offset): ?array
    {
        $best = null;
        foreach ($sections as $section) {
            if ($offset >= $section['start'] && $offset <= $section['end']) {
                // Innermost section = greatest start offset among containing sections.
                if ($best === null || $section['start'] > $best['start']) {
                    $best = $section;
                }
            }
        }
        return $best;
    }

    private function addReference(array &$assets, string $reference, string $sectionName, ?string $alt, ?string $label): void
    {
        $reference = trim(html_entity_decode($reference, ENT_QUOTES | ENT_HTML5), " \t\n\r\0\x0B\"'");
        $reference = mb_substr($reference, 0, 500);
        if (!$this->isLocalImage($reference)) return;

        $cleanReference = preg_replace('/[?#].*$/', '', $reference);
        $filename = basename(str_replace('\\', '/', $cleanReference));
        if ($filename === '') return;

        $key = $this->canonicalKey($reference);
        if (isset($assets[$key])) return;

        $assets[$key] = [
            'asset_type' => 'image',
            'section_name' => mb_substr($sectionName ?: 'Unnamed Section', 0, 255),
            'image_label' => mb_substr((($label ?: $alt) ?: $filename), 0, 255),
            'reference' => $reference,
            'expected_filename' => mb_substr($filename, 0, 255),
            'alt_text' => $alt ?: null,
        ];
    }

    public function canonicalKey(string $reference): string
    {
        $reference = trim(html_entity_decode($reference, ENT_QUOTES | ENT_HTML5), " \t\n\r\0\x0B\"'");
        $path = preg_replace('/[?#].*$/', '', str_replace('\\', '/', $reference));
        $path = preg_replace('#(^|/)\./#', '$1', $path);
        $path = preg_replace('#/+#', '/', $path);
        return ltrim($path, '/');
    }

    private function cleanText(string $html): string
    {
        return trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5)));
    }

    private function attributes(string $tag): array
    {
        $attributes = [];
        if (preg_match_all('/([:\w-]+)\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s>]+))/s', $tag, $matches, PREG_SET_ORDER | PREG_UNMATCHED_AS_NULL)) {
            foreach ($matches as $match) {
                $name = strtolower($match[1]);
                // Optional capture groups may be absent from PREG_SET_ORDER results.
                // Empty quoted values such as alt="" must not read a missing group.
                $value = $match[2] ?? $match[3] ?? $match[4] ?? '';
                $attributes[$name] = html_entity_decode($value, ENT_QUOTES | ENT_HTML5);
            }
        }
        return $attributes;
    }

    private function srcsetReferences(string $srcset): array
    {
        $refs = [];
        foreach (explode(',', $srcset) as $candidate) {
            $candidate = trim($candidate);
            if ($candidate === '') continue;
            $parts = preg_split('/\s+/', $candidate);
            if (!empty($parts[0])) $refs[] = $parts[0];
        }
        return $refs;
    }

    private function cssReferencesWithOffsets(string $css): array
    {
        if (trim($css) === '') return [];
        $refs = [];
        if (preg_match_all('/url\(\s*(["\']?)(.*?)\1\s*\)/is', $css, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
            foreach ($matches as $match) {
                $refs[] = [
                    'reference' => $match[2][0] ?? '',
                    'offset' => $match[0][1] ?? 0,
                ];
            }
        }
        return $refs;
    }

    private function isLocalImage(string $reference): bool
    {
        if ($reference === '' || str_starts_with($reference, '#')) return false;
        if (preg_match('#^(https?:)?//#i', $reference)) return false;
        if (preg_match('#^(data:|blob:|mailto:|tel:|javascript:|vbscript:)#i', $reference)) return false;

        $path = parse_url($reference, PHP_URL_PATH) ?: $reference;
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        return in_array($extension, $this->extensions, true);
    }
}
