<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'hero_title',
    'hero_subtitle',
    'hero_slides',
    'cta_text',
    'cta_link',
    'welcome_title',
    'welcome_description',
])]
class HomeSection extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'hero_slides' => 'array',
        ];
    }
}
