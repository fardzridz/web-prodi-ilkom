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
        return $this->sanitizeHtml($this->content, ['p', 'br', 'div', 'strong', 'b', 'em', 'i', 'u', 's', 'ul', 'ol', 'li', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'a', 'span', 'blockquote', 'pre', 'code', 'img', 'figure', 'figcaption']);
    }
}
