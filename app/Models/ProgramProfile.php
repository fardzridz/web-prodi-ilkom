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
        'p', 'br', 'strong', 'em', 'u', 's',
        'ul', 'ol', 'li',
        'a', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'blockquote', 'pre', 'code', 'hr',
        'table', 'thead', 'tbody', 'tr', 'th', 'td',
        'div', 'span',
    ];

    /**
     * Sanitasi HTML dari Quill editor untuk output publik.
     * Hanya mengizinkan tag HTML yang aman.
     */
    public function getSanitizedHistoryAttribute(): string
    {
        return $this->sanitizeHtml($this->history, self::ALLOWED_TAGS, true);
    }

    public function getSanitizedDescriptionAttribute(): string
    {
        return $this->sanitizeHtml($this->description, self::ALLOWED_TAGS, true);
    }

    public function getSanitizedVisionAttribute(): string
    {
        return $this->sanitizeHtml($this->vision, self::ALLOWED_TAGS, true);
    }

    public function getSanitizedMissionAttribute(): string
    {
        return $this->sanitizeHtml($this->mission, self::ALLOWED_TAGS, true);
    }

    public function getSanitizedGoalsAttribute(): string
    {
        return $this->sanitizeHtml($this->goals, self::ALLOWED_TAGS, true);
    }

    public function getSanitizedAdvantagesAttribute(): string
    {
        return $this->sanitizeHtml($this->advantages, self::ALLOWED_TAGS, true);
    }
}
