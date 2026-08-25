<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\DocumentCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(4);
        $slug = Str::slug($title).'-'.fake()->unique()->numerify('####');

        return [
            'document_category_id' => DocumentCategory::factory(),
            'title' => $title,
            'slug' => $slug,
            'description' => fake()->sentence(10),
            'file' => 'documents/'.$slug.'.pdf',
            'file_type' => 'pdf',
            'file_size' => fake()->numberBetween(10_000, 5_000_000),
            'status' => Document::STATUS_PUBLISHED,
            'uploaded_at' => now(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => Document::STATUS_DRAFT,
        ]);
    }
}
