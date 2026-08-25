<?php

namespace App\Models;

use Database\Factories\HomeSectionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'hero_title',
    'hero_subtitle',
    'hero_slides',
    'advantages',
    'cta_text',
    'cta_link',
    'welcome_title',
    'welcome_description',
    'welcome_image',
])]
class HomeSection extends Model
{
    /** @use HasFactory<HomeSectionFactory> */
    use HasFactory;

    public const DEFAULT_ADVANTAGE_HEADING = 'Mengapa Memilih Ilmu Komputer?';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'hero_slides' => 'array',
            'advantages' => 'array',
        ];
    }

    /**
     * Normalize stored advantages (legacy item list OR new {heading, items})
     * into a stable item list, sorted by order then original index.
     *
     * @return array<int, array{order: int, title: string, description: string, image: string|null}>
     */
    public static function advantageItems(mixed $value): array
    {
        $value = is_array($value) ? $value : [];
        $items = array_is_list($value) ? $value : ($value['items'] ?? []);

        return collect($items)
            ->filter(fn (mixed $item): bool => is_array($item) && filled($item['title'] ?? null))
            ->values()
            ->sortBy(fn (array $item, int $key): array => [(int) ($item['order'] ?? $key + 1), $key])
            ->values()
            ->map(fn (array $item, int $key): array => [
                'order' => (int) ($item['order'] ?? $key + 1),
                'title' => (string) ($item['title'] ?? ''),
                'description' => (string) ($item['description'] ?? $item['copy'] ?? ''),
                'image' => filled($item['image'] ?? null) ? (string) $item['image'] : null,
            ])
            ->all();
    }

    /**
     * Resolve the section heading from stored advantages, falling back
     * to the default for legacy item-list values.
     */
    public static function advantageHeading(mixed $value, string $default = self::DEFAULT_ADVANTAGE_HEADING): string
    {
        $value = is_array($value) ? $value : [];

        if (! array_is_list($value) && filled($value['heading'] ?? null)) {
            return (string) $value['heading'];
        }

        return $default;
    }
}
