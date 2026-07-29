<?php

namespace App\Http\Requests\Admin;

use App\Models\HomeSection;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateHomeSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === User::ROLE_ADMIN;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        $existingPaths = collect(HomeSection::query()->first()?->hero_slides ?? [])
            ->pluck('path')
            ->filter()
            ->values()
            ->all();

        return [
            'hero_title' => ['required', 'string', 'max:500'],
            'hero_subtitle' => ['required', 'string', 'max:5000'],
            'cta_text' => ['required', 'string', 'max:100'],
            'cta_link' => ['required', 'string', 'max:2048', 'regex:/^(\/(?!\/)[^\s]*|https?:\/\/[^\s]+)$/i'],
            'welcome_title' => ['required', 'string', 'max:255'],
            'welcome_description' => ['required', 'string', 'max:10000'],
            'slides' => ['nullable', 'array', 'max:5'],
            'slides.*.existing_path' => ['nullable', 'string', 'max:255', Rule::in($existingPaths)],
            'slides.*.alt' => ['nullable', 'string', 'max:255'],
            'slides.*.file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'slides.*.remove' => ['nullable', 'boolean'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'hero_title.required' => 'Judul utama wajib diisi.',
            'hero_subtitle.required' => 'Kalimat pembuka wajib diisi.',
            'cta_text.required' => 'Teks tombol wajib diisi.',
            'cta_link.regex' => 'Tujuan tombol harus berupa path internal atau URL HTTP/HTTPS yang valid.',
            'welcome_title.required' => 'Judul sambutan wajib diisi.',
            'welcome_description.required' => 'Isi sambutan wajib diisi.',
            'slides.max' => 'Gambar hero maksimal 5 slide.',
            'slides.*.file.image' => 'Berkas slide harus berupa gambar.',
            'slides.*.file.mimes' => 'Gambar slide harus berformat JPG, PNG, atau WebP.',
            'slides.*.file.max' => 'Ukuran gambar slide maksimal 4 MB.',
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                foreach ((array) $this->input('slides', []) as $index => $slide) {
                    if (($slide['remove'] ?? false) || (! ($slide['existing_path'] ?? null) && ! $this->file("slides.{$index}.file") && ! ($slide['alt'] ?? null))) {
                        continue;
                    }

                    if (! ($slide['existing_path'] ?? null) && ! $this->file("slides.{$index}.file")) {
                        $validator->errors()->add("slides.{$index}.file", 'Pilih gambar untuk slide baru.');
                    }

                    if (blank($slide['alt'] ?? null)) {
                        $validator->errors()->add("slides.{$index}.alt", 'Teks pengganti gambar wajib diisi.');
                    }
                }
            },
        ];
    }

    protected function prepareForValidation(): void
    {
        $slides = collect((array) $this->input('slides', []))
            ->map(function (mixed $slide): array {
                $slide = is_array($slide) ? $slide : [];

                return [
                    ...$slide,
                    'existing_path' => trim((string) ($slide['existing_path'] ?? '')) ?: null,
                    'alt' => trim((string) ($slide['alt'] ?? '')) ?: null,
                    'remove' => filter_var($slide['remove'] ?? false, FILTER_VALIDATE_BOOL),
                ];
            })
            ->all();

        $this->merge([
            'hero_title' => trim(str_replace(["\r\n", "\r"], "\n", (string) $this->input('hero_title'))),
            'hero_subtitle' => trim((string) $this->input('hero_subtitle')),
            'cta_text' => trim((string) $this->input('cta_text')),
            'cta_link' => trim((string) $this->input('cta_link')),
            'welcome_title' => trim((string) $this->input('welcome_title')),
            'welcome_description' => trim((string) $this->input('welcome_description')),
            'slides' => $slides,
        ]);
    }
}
