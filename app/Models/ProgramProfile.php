<?php

namespace App\Models;

use App\Models\Concerns\SanitizesHtml;
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
    use SanitizesHtml;

    private const ALLOWED_TAGS = [
        'p',
        'br',
        'strong',
        'em',
        'u',
        's',
        'ul',
        'ol',
        'li',
        'a',
        'h1',
        'h2',
        'h3',
        'h4',
        'h5',
        'h6',
        'blockquote',
        'pre',
        'code',
        'hr',
        'table',
        'thead',
        'tbody',
        'tr',
        'th',
        'td',
        'img',
        'figure',
        'figcaption',
        'div',
        'span',
    ];

    /**
     * Sanitasi HTML dari Quill editor untuk output publik.
     * Hanya mengizinkan tag HTML yang aman.
     */
    public function getSanitizedHistoryAttribute(): string
    {
        return $this->sanitizeHtml($this->history, self::ALLOWED_TAGS);
    }

    public function getSanitizedDescriptionAttribute(): string
    {
        return $this->sanitizeHtml($this->description, self::ALLOWED_TAGS);
    }

    public function getSanitizedVisionAttribute(): string
    {
        return $this->sanitizeHtml($this->vision, self::ALLOWED_TAGS);
    }

    public function getSanitizedMissionAttribute(): string
    {
        return $this->sanitizeHtml($this->mission, self::ALLOWED_TAGS);
    }

    public function getSanitizedGoalsAttribute(): string
    {
        return $this->sanitizeHtml($this->goals, self::ALLOWED_TAGS);
    }

    public function getSanitizedAdvantagesAttribute(): string
    {
        return $this->sanitizeHtml($this->advantages, self::ALLOWED_TAGS);
    }

    /**
     * Detect Quill Delta payloads. The frontend serializes a delta as a JSON
     * object (always starting with "{" and parseable as a JSON object). We
     * also accept an array directly so the view can pass the raw stored value
     * through without guesswork. Plain prose that merely starts with "{" (e.g.
     * "{JSON} adalah format ...") must NOT be treated as a delta.
     */
    public function isDeltaPayload(mixed $value): bool
    {
        if (is_array($value)) {
            return true;
        }

        if (! is_string($value)) {
            return false;
        }

        $trimmed = ltrim($value);

        if (! str_starts_with($trimmed, '{')) {
            return false;
        }

        $decoded = json_decode($trimmed, true);

        return is_array($decoded) && ! array_is_list($decoded) && $decoded !== [];
    }

    public function isDeltaHistory(): bool
    {
        return $this->isDeltaPayload($this->history);
    }

    public function isDeltaDescription(): bool
    {
        return $this->isDeltaPayload($this->description);
    }

    public function isDeltaVision(): bool
    {
        return $this->isDeltaPayload($this->vision);
    }

    public function isDeltaMission(): bool
    {
        return $this->isDeltaPayload($this->mission);
    }

    public function isDeltaGoals(): bool
    {
        return $this->isDeltaPayload($this->goals);
    }

    public function isDeltaAdvantages(): bool
    {
        return $this->isDeltaPayload($this->advantages);
    }
}
