<?php

namespace App\Services\Page;

use App\Models\Page;

class CustomPageRenderer
{
    public function renderHtml(Page $page): string
    {
        $html = (string) $page->custom_html;
        $scope = '#seo-custom-page-' . $page->id;
        $assetMap = $this->assetMap($page);

        $html = $this->replaceAssetReferences($html, $assetMap);
        $html = $this->sanitizeHtml($html);

        // Scope CSS included directly in ChatGPT HTML as <style>...</style>.
        $html = preg_replace_callback('#<style\b[^>]*>(.*?)</style\s*>#is', function ($match) use ($scope, $assetMap) {
            $css = $this->replaceAssetReferences($match[1] ?? '', $assetMap);
            return '<style>' . $this->scopeBlock($this->namespaceKeyframes($css, $scope), $scope) . '</style>';
        }, $html);

        return $html;
    }

    public function scopedCss(Page $page): string
    {
        $scope = '#seo-custom-page-' . $page->id;
        $css = trim((string) $page->custom_css);
        $css = $this->replaceAssetReferences($css, $this->assetMap($page));

        // Strong same-DOM isolation boundary. Global author CSS is reset for descendants,
        // while normal UA defaults (block headings, lists, etc.) are retained via revert.
        $isolation = $scope . '{display:block;contain:layout style;isolation:isolate;}'
            . $scope . ',' . $scope . ' *{box-sizing:border-box;}'
            . $scope . ' *{all:revert;box-sizing:border-box;}'
            . $scope . ' img{' . 'max-width:100%;height:auto;' . '}';

        return $isolation . ($css !== '' ? $this->scopeBlock($this->namespaceKeyframes($css, $scope), $scope) : '');
    }

    /**
     * Return page-specific JavaScript exactly as authored, while preventing a literal
     * closing </script> sequence from terminating the wrapper created by the Blade view.
     * Custom JS is intentionally separate from the Custom HTML sanitizer.
     */
    public function renderJs(Page $page): string
    {
        $js = (string) $page->custom_js;
        return preg_replace('#</script#i', '<\/script', $js) ?? $js;
    }

    private function sanitizeHtml(string $html): string
    {
        // Remove active/document-level elements with their contents first. Custom PageMaker
        // HTML is section content, not a second document head or an application surface.
        $html = preg_replace('#<(script|iframe|object|embed|svg|math|template)\b[^>]*>.*?</\1\s*>#is', '', $html);
        $html = preg_replace('#<(script|iframe|object|embed|svg|math|form|template|meta|link|base|input|textarea|select|option)\b[^>]*/?\s*>#is', '', $html);

        // Remove parser-trick event attributes before tokenizing attributes (e.g. src=x/onerror=...).
        $html = preg_replace('/[\s\/]+on[a-z][\w:-]*\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);

        return $this->sanitizeAllowedTags($html);
    }

    private function sanitizeAllowedTags(string $html): string
    {
        $allowedTags = [
            'section', 'div', 'article', 'main', 'header', 'footer', 'nav', 'aside',
            'span', 'p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'a', 'img', 'picture', 'source',
            'figure', 'figcaption', 'ul', 'ol', 'li', 'dl', 'dt', 'dd', 'strong', 'b', 'em', 'i',
            'u', 's', 'small', 'mark', 'br', 'hr', 'blockquote', 'pre', 'code', 'table', 'thead',
            'tbody', 'tfoot', 'tr', 'th', 'td', 'colgroup', 'col', 'caption', 'details', 'summary',
            'time', 'address', 'style', 'button',
        ];

        $out = '';
        $len = strlen($html);
        $cursor = 0;

        while ($cursor < $len) {
            $lt = strpos($html, '<', $cursor);
            if ($lt === false) {
                $out .= substr($html, $cursor);
                break;
            }

            $out .= substr($html, $cursor, $lt - $cursor);

            // Preserve literal '<' that is not the start of a tag/comment.
            if (!preg_match('/^<\/?[A-Za-z!]/', substr($html, $lt, 3))) {
                $out .= '<';
                $cursor = $lt + 1;
                continue;
            }

            $gt = $this->findTagEnd($html, $lt + 1);
            if ($gt === null) {
                $out .= htmlspecialchars(substr($html, $lt), ENT_NOQUOTES | ENT_HTML5);
                break;
            }

            $tagText = substr($html, $lt, $gt - $lt + 1);
            $out .= $this->sanitizeTag($tagText, $allowedTags);
            $cursor = $gt + 1;
        }

        return $out;
    }

    private function findTagEnd(string $html, int $start): ?int
    {
        $quote = null;
        $len = strlen($html);
        for ($i = $start; $i < $len; $i++) {
            $c = $html[$i];
            if ($quote !== null) {
                if ($c === $quote && ($i === 0 || $html[$i - 1] !== '\\')) $quote = null;
                continue;
            }
            if ($c === '"' || $c === "'") { $quote = $c; continue; }
            if ($c === '>') return $i;
        }
        return null;
    }

    private function sanitizeTag(string $tagText, array $allowedTags): string
    {
        if (preg_match('/^<!--/s', $tagText) || preg_match('/^<!DOCTYPE/i', $tagText)) return '';

        $closing = preg_match('/^<\s*\//', $tagText) === 1;
        if (!preg_match('/^<\s*\/?\s*([A-Za-z][A-Za-z0-9:-]*)/i', $tagText, $m)) return '';
        $tag = strtolower($m[1]);
        if (!in_array($tag, $allowedTags, true)) return '';
        if ($closing) return '</' . $tag . '>';

        $selfClosing = preg_match('/\/\s*>$/', $tagText) === 1;
        $inside = preg_replace('/^<\s*[A-Za-z][A-Za-z0-9:-]*/i', '', $tagText, 1);
        $inside = preg_replace('/\/?>\s*$/', '', (string) $inside);

        $attributes = [];
        preg_match_all(
            '/([A-Za-z_:][A-Za-z0-9_:.\-]*)(?:\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s"\'=<>`]+)))?/s',
            (string) $inside,
            $matches,
            PREG_SET_ORDER
        );

        foreach ($matches as $attr) {
            $name = strtolower($attr[1]);
            $a2 = $attr[2] ?? '';
            $a3 = $attr[3] ?? '';
            $a4 = $attr[4] ?? '';
            $value = $a2 !== '' ? $a2 : ($a3 !== '' ? $a3 : $a4);

            if (!$this->isAllowedAttribute($tag, $name)) continue;
            if (str_starts_with($name, 'on') || $name === 'srcdoc') continue;

            if (in_array($name, ['srcset', 'data-srcset'], true)) {
                $value = $this->sanitizeSrcset($value);
                if ($value === '') continue;
            } elseif (in_array($name, ['href', 'src', 'action', 'formaction', 'xlink:href', 'data-src', 'data-lazy-src'], true)
                && !$this->isSafeUrl($value, $tag === 'a' && $name === 'href')) {
                continue;
            }

            if ($name === 'style') {
                $value = $this->sanitizeInlineStyle($value);
                if ($value === '') continue;
            }

            // Safe boolean attributes are emitted without a value.
            if ($value === '' && in_array($name, ['open', 'controls', 'autoplay', 'muted', 'loop', 'disabled'], true)) {
                $attributes[] = $name;
                continue;
            }

            $attributes[] = $name . '="' . htmlspecialchars($value, ENT_QUOTES | ENT_HTML5) . '"';
        }

        if ($tag === 'a') {
            $targetBlank = false;
            foreach ($attributes as $attribute) {
                if (preg_match('/^target="_blank"$/i', $attribute)) { $targetBlank = true; break; }
            }
            if ($targetBlank && !array_filter($attributes, fn ($a) => str_starts_with(strtolower($a), 'rel='))) {
                $attributes[] = 'rel="noopener noreferrer"';
            }
        }

        $void = in_array($tag, ['img', 'source', 'br', 'hr', 'col'], true);
        return '<' . $tag . ($attributes ? ' ' . implode(' ', $attributes) : '') . (($selfClosing || $void) ? ' />' : '>');
    }

    private function isAllowedAttribute(string $tag, string $name): bool
    {
        if (preg_match('/^aria-[a-z0-9_-]+$/', $name)) return true;
        if (in_array($name, ['class', 'id', 'title', 'role', 'style', 'dir', 'lang', 'tabindex'], true)) return true;
        if (in_array($name, ['data-section-name', 'data-image-label', 'data-component'], true)) return true;

        $byTag = [
            'a' => ['href', 'target', 'rel'],
            'img' => ['src', 'srcset', 'sizes', 'alt', 'width', 'height', 'loading', 'decoding', 'fetchpriority', 'data-src', 'data-lazy-src', 'data-srcset'],
            'source' => ['src', 'srcset', 'sizes', 'media', 'type', 'data-src', 'data-srcset'],
            'button' => ['type', 'disabled'],
            'details' => ['open'],
            'time' => ['datetime'],
            'th' => ['colspan', 'rowspan', 'scope'],
            'td' => ['colspan', 'rowspan'],
            'col' => ['span'],
        ];

        return in_array($name, $byTag[$tag] ?? [], true);
    }

    private function isSafeUrl(string $value, bool $allowContactSchemes = false): bool
    {
        $decoded = trim(html_entity_decode($value, ENT_QUOTES | ENT_HTML5));
        $normalized = preg_replace('/[\x00-\x20]+/', '', strtolower($decoded));
        if ($normalized === '') return true;
        if (str_starts_with($normalized, '#')) return true;
        if (str_starts_with($normalized, '/') || str_starts_with($normalized, './') || str_starts_with($normalized, '../')) return true;
        if (preg_match('#^https?://#i', $normalized)) return true;
        if ($allowContactSchemes && preg_match('#^(mailto|tel):#i', $normalized)) return true;
        // Plain relative paths are allowed; any explicit unapproved URI scheme is not.
        return !preg_match('/^[a-z][a-z0-9+.-]*:/i', $normalized);
    }

    private function sanitizeSrcset(string $value): string
    {
        $decoded = html_entity_decode($value, ENT_QUOTES | ENT_HTML5);
        $candidates = [];

        foreach (preg_split('/\s*,\s*/', trim($decoded)) ?: [] as $candidate) {
            if ($candidate === '') continue;

            if (!preg_match('/^([^\s]+)(?:\s+(.+))?$/s', trim($candidate), $match)) {
                continue;
            }

            $url = trim($match[1]);
            $descriptor = isset($match[2]) ? trim($match[2]) : '';

            if (!$this->isSafeUrl($url, false)) continue;

            // Accept standard srcset descriptors only: width (e.g. 640w) or density (e.g. 2x).
            if ($descriptor !== '' && !preg_match('/^(?:\d+w|(?:\d+(?:\.\d+)?|\.\d+)x)$/i', $descriptor)) {
                continue;
            }

            $candidates[] = $url . ($descriptor !== '' ? ' ' . $descriptor : '');
        }

        return implode(', ', $candidates);
    }

    private function sanitizeInlineStyle(string $style): string
    {
        $decoded = html_entity_decode($style, ENT_QUOTES | ENT_HTML5);
        if (preg_match('/expression\s*\(|(?:javascript|vbscript)\s*:|behavior\s*:|-moz-binding\s*:/i', $decoded)) {
            return '';
        }
        if (preg_match('/url\s*\(\s*["\']?\s*data\s*:\s*(?!image\/(?:png|jpe?g|webp|avif))/i', $decoded)) {
            return '';
        }
        return $style;
    }

    private function assetMap(Page $page): array
    {
        $map = [];
        foreach ($page->assets()->where('status', 'uploaded')->get() as $asset) {
            if ($asset->stored_path) {
                // This installation serves Laravel from the project root (pages/index.php),
                // so files physically under Laravel's public/ directory are browser-visible
                // through /pages/public/... rather than /pages/.... Keep stored_path
                // relative to public_path() for filesystem operations and add public/ only
                // when building the browser URL.
                $map[$asset->reference] = asset('public/' . ltrim($asset->stored_path, '/'));
            }
        }
        return $map;
    }

    private function replaceAssetReferences(string $content, array $assetMap): string
    {
        if ($content === '' || empty($assetMap)) return $content;

        $canonicalMap = [];
        foreach ($assetMap as $reference => $url) {
            $canonicalMap[$this->canonicalReference($reference)] = $url;
        }

        $replaceOne = function (string $reference) use ($assetMap, $canonicalMap): string {
            $decoded = html_entity_decode($reference, ENT_QUOTES | ENT_HTML5);
            if (isset($assetMap[$decoded])) return $assetMap[$decoded];
            $key = $this->canonicalReference($decoded);
            return $canonicalMap[$key] ?? $reference;
        };

        // Standard URL attributes.
        $content = preg_replace_callback(
            '/\b(src|href|data-src|data-lazy-src)\s*=\s*("([^"]*)"|\'([^\']*)\'|([^\s>]+))/is',
            function ($m) use ($replaceOne) {
                $raw = $m[3] !== '' ? $m[3] : ($m[4] !== '' ? $m[4] : ($m[5] ?? ''));
                $replacement = $replaceOne($raw);
                if ($replacement === $raw) return $m[0];
                return $m[1] . '="' . htmlspecialchars($replacement, ENT_QUOTES | ENT_HTML5) . '"';
            },
            $content
        );

        // srcset/data-srcset can carry multiple URL + descriptor candidates.
        $content = preg_replace_callback(
            '/\b(srcset|data-srcset)\s*=\s*("([^"]*)"|\'([^\']*)\')/is',
            function ($m) use ($replaceOne) {
                $raw = $m[3] !== '' ? $m[3] : ($m[4] ?? '');
                $parts = [];
                foreach (explode(',', $raw) as $candidate) {
                    $candidate = trim($candidate);
                    if ($candidate === '') continue;
                    $pieces = preg_split('/\s+/', $candidate, 2);
                    $url = $replaceOne($pieces[0]);
                    $parts[] = $url . (isset($pieces[1]) ? ' ' . $pieces[1] : '');
                }
                return $m[1] . '="' . htmlspecialchars(implode(', ', $parts), ENT_QUOTES | ENT_HTML5) . '"';
            },
            $content
        );

        // CSS url(...) in custom CSS, inline styles and <style> blocks.
        $content = preg_replace_callback('/url\(\s*(["\']?)(.*?)\1\s*\)/is', function ($m) use ($replaceOne) {
            $replacement = $replaceOne($m[2]);
            return 'url(' . $replacement . ')';
        }, $content);

        return $content;
    }

    private function canonicalReference(string $reference): string
    {
        $reference = trim(html_entity_decode($reference, ENT_QUOTES | ENT_HTML5), " \t\n\r\0\x0B\"'");
        if (preg_match('#^(https?:)?//#i', $reference) || preg_match('#^(data:|blob:|javascript:|vbscript:)#i', $reference)) {
            return '__remote__:' . $reference;
        }
        $path = preg_replace('/[?#].*$/', '', str_replace('\\', '/', $reference));
        $path = preg_replace('#(^|/)\./#', '$1', $path);
        $path = preg_replace('#/+#', '/', $path);
        return ltrim($path, '/');
    }

    private function namespaceKeyframes(string $css, string $scope): string
    {
        $prefix = 'pm-' . preg_replace('/[^A-Za-z0-9_-]+/', '-', trim($scope, '#. ')) . '-';
        $map = [];

        $css = preg_replace_callback('/@(keyframes|-webkit-keyframes)\s+([A-Za-z_][A-Za-z0-9_-]*)/i', function ($m) use (&$map, $prefix) {
            $old = $m[2];
            $new = $prefix . $old;
            $map[$old] = $new;
            return '@' . $m[1] . ' ' . $new;
        }, $css);

        if (!$map) return $css;

        return preg_replace_callback('/((?:-webkit-)?animation(?:-name)?\s*:\s*)([^;{}]+)/i', function ($m) use ($map) {
            $value = $m[2];
            foreach ($map as $old => $new) {
                $value = preg_replace('/(?<![A-Za-z0-9_-])' . preg_quote($old, '/') . '(?![A-Za-z0-9_-])/', $new, $value);
            }
            return $m[1] . $value;
        }, $css);
    }

    private function scopeBlock(string $css, string $scope): string
    {
        $css = preg_replace('#/\*.*?\*/#s', '', $css);
        $css = $this->stripBraceLessAtRules($css);
        $out = '';
        $len = strlen($css);
        $i = 0;

        while ($i < $len) {
            $open = $this->nextTopLevelBrace($css, $i);
            if ($open === null) {
                $out .= trim(substr($css, $i));
                break;
            }

            $header = trim(substr($css, $i, $open - $i));
            [$body, $next] = $this->balancedBody($css, $open);
            if ($next <= $open) break;

            if ($header === '') {
                $i = $next;
                continue;
            }

            if (preg_match('/^@(media|supports|layer|container)\b/i', $header)) {
                $out .= $header . '{' . $this->scopeBlock($body, $scope) . '}';
            } elseif (preg_match('/^@(keyframes|-webkit-keyframes)\b/i', $header)) {
                $out .= $header . '{' . $body . '}';
            } elseif (preg_match('/^@(font-face|page|property|counter-style)\b/i', $header)) {
                // These at-rules create document-global state and are intentionally omitted.
            } elseif (str_starts_with(ltrim($header), '@')) {
                // Unknown block at-rules are omitted rather than allowed to create global state.

            } else {
                $selectors = $this->splitSelectors($header);
                $selectors = array_map(function ($selector) use ($scope) {
                    $selector = trim($selector);
                    if ($selector === '') return $selector;
                    if (preg_match('/^(html|body|:root)(\b|\s|\.|#|:)/i', $selector)) {
                        return preg_replace('/^(html|body|:root)/i', $scope, $selector, 1);
                    }
                    if (str_starts_with($selector, $scope)) return $selector;
                    return $scope . ' ' . $selector;
                }, $selectors);
                $out .= implode(', ', $selectors) . '{' . $body . '}';
            }

            $i = $next;
        }

        return $out;
    }

    private function stripBraceLessAtRules(string $css): string
    {
        // @charset/@namespace cannot help page-local styles; @import is intentionally removed
        // because imported styles would be global and defeat the isolation boundary.
        do {
            $before = $css;
            $css = preg_replace('/(^|[;}])(\s*)@(charset|import|namespace)\b[^;{}]*;\s*/i', '$1$2', $css);
        } while ($css !== $before);

        return $css;
    }

    private function nextTopLevelBrace(string $css, int $start): ?int
    {
        $quote = null;
        $paren = 0;
        $bracket = 0;
        $len = strlen($css);
        for ($i = $start; $i < $len; $i++) {
            $c = $css[$i];
            if ($quote !== null) {
                if ($c === $quote && ($i === 0 || $css[$i - 1] !== '\\')) $quote = null;
                continue;
            }
            if ($c === '"' || $c === "'") { $quote = $c; continue; }
            if ($c === '(') { $paren++; continue; }
            if ($c === ')') { $paren = max(0, $paren - 1); continue; }
            if ($c === '[') { $bracket++; continue; }
            if ($c === ']') { $bracket = max(0, $bracket - 1); continue; }
            if ($c === '{' && $paren === 0 && $bracket === 0) return $i;
        }
        return null;
    }

    private function balancedBody(string $css, int $open): array
    {
        $depth = 1;
        $quote = null;
        $len = strlen($css);
        for ($j = $open + 1; $j < $len; $j++) {
            $c = $css[$j];
            if ($quote !== null) {
                if ($c === $quote && $css[$j - 1] !== '\\') $quote = null;
                continue;
            }
            if ($c === '"' || $c === "'") { $quote = $c; continue; }
            if ($c === '{') $depth++;
            elseif ($c === '}') {
                $depth--;
                if ($depth === 0) {
                    return [substr($css, $open + 1, $j - $open - 1), $j + 1];
                }
            }
        }
        return [substr($css, $open + 1), $len];
    }

    private function splitSelectors(string $header): array
    {
        $parts = [];
        $current = '';
        $paren = 0;
        $bracket = 0;
        $quote = null;
        $len = strlen($header);

        for ($i = 0; $i < $len; $i++) {
            $c = $header[$i];
            if ($quote !== null) {
                $current .= $c;
                if ($c === $quote && ($i === 0 || $header[$i - 1] !== '\\')) $quote = null;
                continue;
            }
            if ($c === '"' || $c === "'") { $quote = $c; $current .= $c; continue; }
            if ($c === '(') $paren++;
            elseif ($c === ')') $paren = max(0, $paren - 1);
            elseif ($c === '[') $bracket++;
            elseif ($c === ']') $bracket = max(0, $bracket - 1);

            if ($c === ',' && $paren === 0 && $bracket === 0) {
                $parts[] = trim($current);
                $current = '';
            } else {
                $current .= $c;
            }
        }

        if (trim($current) !== '') $parts[] = trim($current);
        return $parts;
    }
}
