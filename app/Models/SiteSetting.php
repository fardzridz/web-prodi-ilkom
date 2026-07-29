<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'site_name',
    'university_name',
    'faculty_name',
    'logo',
    'favicon',
    'journal_url',
    'footer_text',
    'footer_academic_links',
])]
class SiteSetting extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'footer_academic_links' => 'array',
        ];
    }
}
