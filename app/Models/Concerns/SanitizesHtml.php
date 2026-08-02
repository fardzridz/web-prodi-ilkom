<?php

namespace App\Models\Concerns;

trait SanitizesHtml
{
    protected function sanitizeHtml(?string $html, array $allowedTags, bool $stripStyles = false): string
    {
        if (blank($html)) {
            return '';
        }

        $safe = strip_tags($html, '<'.implode('><', $allowedTags).'>');

        // Drop event-handler attributes (onclick, onerror, …).
        $safe = preg_replace('/\s+on[a-z]+\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $safe) ?? $safe;

        // Neutralize javascript: / data: / vbscript: in href (and similar URL attrs).
        $safe = preg_replace_callback(
            '/\s(href|src|xlink:href)\s*=\s*(["\'])(.*?)\2/i',
            function (array $matches): string {
                $url = trim(html_entity_decode($matches[3], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

                if (in_array($scheme, ['javascript', 'data', 'vbscript'], true)) {
                    return '';
                }

                return ' '.$matches[1].'='.$matches[2].$matches[3].$matches[2];
            },
            $safe
        ) ?? $safe;

        if ($stripStyles) {
            $safe = preg_replace('/\sstyle\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $safe) ?? $safe;
        }

        return $safe;
    }
}
