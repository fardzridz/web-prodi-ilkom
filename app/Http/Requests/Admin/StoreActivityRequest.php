<?php

namespace App\Http\Requests\Admin;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === User::ROLE_ADMIN;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique(Activity::class, 'slug'),
            ],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'content' => ['required', 'string', 'max:100000'],
            'activity_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:2000-01-01', 'before_or_equal:2100-12-31'],
            'location' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
            'status' => ['required', Rule::in(Activity::STATUSES)],
            'published_at' => [
                Rule::requiredIf(fn (): bool => $this->input('status') === Activity::STATUS_SCHEDULED),
                Rule::prohibitedIf(fn (): bool => in_array($this->input('status'), [Activity::STATUS_DRAFT, Activity::STATUS_PUBLISHED], true)),
                'nullable',
                'date',
                Rule::when($this->input('status') === Activity::STATUS_SCHEDULED, ['after:now']),
            ],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'slug.regex' => 'Alamat halaman hanya boleh berisi huruf kecil, angka, dan tanda hubung.',
            'slug.unique' => 'Alamat halaman sudah digunakan kegiatan lain.',
            'published_at.required' => 'Jadwal tayang wajib diisi untuk kegiatan terjadwal.',
            'published_at.after' => 'Jadwal tayang harus berada di masa depan.',
            'published_at.prohibited' => 'Jadwal tayang hanya boleh diisi untuk status terjadwal.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format gambar harus JPG, PNG, atau WebP.',
            'image.max' => 'Ukuran gambar maksimal 2 MB.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'title' => trim((string) $this->input('title')),
            'slug' => Str::slug($this->input('slug') ?: $this->input('title')),
            'excerpt' => trim((string) $this->input('excerpt')) ?: null,
            'content' => (string) $this->input('content'),
            'location' => trim((string) $this->input('location')),
            'category' => trim((string) $this->input('category')) ?: null,
            'status' => strtolower(trim((string) $this->input('status'))),
        ]);
    }
}
