<?php

namespace App\Models;

use Database\Factories\AlumniFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'name',
    'batch_year',
    'graduation_year',
    'job_position',
    'company',
    'testimonial',
    'photo',
    'status',
])]
class Alumni extends Model
{
    /** @use HasFactory<AlumniFactory> */
    use HasFactory;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    public const STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_INACTIVE,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'alumni';

    public function getPhotoExistsAttribute(): bool
    {
        if (blank($this->photo)) {
            return false;
        }

        $key = 'alumni:photo_exists:'.$this->id.':'.md5((string) $this->photo);

        return (bool) Cache::remember($key, 3600, fn (): bool => Storage::disk('public')->exists($this->photo));
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if (! $this->photo_exists) {
            return null;
        }

        return Storage::disk('public')->url($this->photo);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_INACTIVE => 'Nonaktif',
            default => 'Aktif',
        };
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'batch_year' => 'integer',
            'graduation_year' => 'integer',
        ];
    }
}
