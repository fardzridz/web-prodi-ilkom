<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'nidn',
    'position',
    'expertise',
    'education',
    'email',
    'photo',
    'bio',
    'status',
    'sort_order',
])]
class Lecturer extends Model
{
    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    public const STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_INACTIVE,
    ];

    /**
     * Get a human-readable label for the lecturer's status.
     */
    public function statusLabel(): string
    {
        return $this->status === self::STATUS_ACTIVE ? 'Aktif' : 'Nonaktif';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }
}
