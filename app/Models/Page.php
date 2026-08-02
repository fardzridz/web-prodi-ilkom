<?php

namespace App\Models;

use App\Models\Concerns\SanitizesHtml;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'title',
    'slug',
    'content',
])]
class Page extends Model
{
    use SanitizesHtml;

    public function getSanitizedContentAttribute(): string
    {
        return $this->sanitizeHtml($this->content, ['p', 'br', 'strong', 'b', 'em', 'i', 'ul', 'ol', 'li', 'h2', 'h3', 'h4', 'a', 'span']);
    }
}
