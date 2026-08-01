<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'history',
    'description',
    'vision',
    'mission',
    'goals',
    'accreditation',
    'advantages',
])]
class ProgramProfile extends Model
{
    /**
     * Sanitasi HTML dari Quill editor untuk output publik.
     * Hanya mengizinkan tag HTML yang aman.
     */
    public function getSanitizedHistoryAttribute(): string
    {
        return $this->sanitizeHtml($this->history);
    }

    public function getSanitizedDescriptionAttribute(): string
    {
        return $this->sanitizeHtml($this->description);
    }

    public function getSanitizedVisionAttribute(): string
    {
        return $this->sanitizeHtml($this->vision);
    }

    public function getSanitizedMissionAttribute(): string
    {
        return $this->sanitizeHtml($this->mission);
    }

    public function getSanitizedGoalsAttribute(): string
    {
        return $this->sanitizeHtml($this->goals);
    }

    public function getSanitizedAdvantagesAttribute(): string
    {
        return $this->sanitizeHtml($this->advantages);
    }

    /**
     * Sanitasi HTML dengan allowlist tag aman.
     */
    private function sanitizeHtml(?string $html): string
    {
        if (blank($html)) {
            return '';
        }

        $allowedTags = [
            'p', 'br', 'strong', 'em', 'u', 's',
            'ul', 'ol', 'li',
            'a', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
            'blockquote', 'pre', 'code', 'hr',
            'table', 'thead', 'tbody', 'tr', 'th', 'td',
            'div', 'span',
        ];

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

        // Strip style attributes.
        $safe = preg_replace('/\sstyle\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $safe) ?? $safe;

        return $safe;
    }
}
