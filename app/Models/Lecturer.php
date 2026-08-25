<?php

namespace App\Models;

use Database\Factories\LecturerFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

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
    /** @use HasFactory<LecturerFactory> */
    use HasFactory;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    public const STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_INACTIVE,
    ];

    public function getPhotoExistsAttribute(): bool
    {
        if (blank($this->photo)) {
            return false;
        }

        $key = 'lecturer:photo_exists:'.$this->id.':'.md5((string) $this->photo);

        return (bool) Cache::remember($key, 3600, fn (): bool => Storage::disk('public')->exists($this->photo));
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if (! $this->photo_exists) {
            return null;
        }

        return Storage::disk('public')->url($this->photo);
    }

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
