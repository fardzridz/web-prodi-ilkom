<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

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
