<?php

namespace App\Http\Requests\Admin;

use App\Models\HomeSection;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateHomeSectionRequest extends FormRequest
{
    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        $homeSection = HomeSection::query()->first();
        $existingPaths = collect($homeSection?->hero_slides ?? [])
            ->pluck('path')
            ->filter()
            ->values()
            ->all();
        $existingAdvantagePaths = collect(HomeSection::advantageItems($homeSection?->advantages))
            ->pluck('image')
            ->filter()
            ->values()
            ->all();

        return [
            'hero_title' => ['required', 'string', 'max:500'],
            'hero_subtitle' => ['required', 'string', 'max:5000'],
            'cta_text' => ['required', 'string', 'max:100'],
            'cta_link' => [
                'required',
                'string',
                'max:2048',
                'regex:/^(\/(?!\/)[^\s]*|https?:\/\/[^\s]+)$/i',
                function (string $attribute, mixed $value, Closure $fail): void {
                    $value = trim((string) $value);

                    // Path internal — hanya izinkan path absolut tanpa scheme.
                    if (str_starts_with($value, '/')) {
                        if (str_starts_with($value, '//')) {
                            $fail('Tujuan tombol tidak boleh berupa URL protokol-relatif.');
                        }

                        return;
                    }

                    // URL absolut — hanya http/https, dan TANPA karakter control/whitespace.
                    if (! filter_var($value, FILTER_VALIDATE_URL) || preg_match('/[\x00-\x20]/', $value)) {
                        $fail('Tujuan tombol harus berupa path internal atau URL HTTP/HTTPS yang valid.');
                    }

                    $scheme = strtolower((string) parse_url($value, PHP_URL_SCHEME));

                    if (! in_array($scheme, ['http', 'https'], true)) {
                        $fail('Tujuan tombol hanya boleh menggunakan skema http atau https.');
                    }
                },
            ],
            'welcome_title' => ['required', 'string', 'max:255'],
            'welcome_description' => ['required', 'string', 'max:10000'],
            'slides' => ['nullable', 'array', 'max:5'],
            'slides.*.existing_path' => ['nullable', 'string', 'max:255', Rule::in($existingPaths)],
            'slides.*.alt' => ['nullable', 'string', 'max:255'],
            'slides.*.file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'slides.*.remove' => ['nullable', 'boolean'],
            'advantages_heading' => ['required', 'string', 'max:255'],
            'advantages' => ['nullable', 'array', 'max:20'],
            'advantages.*.order' => ['required', 'integer', 'min:1', 'max:20'],
            'advantages.*.title' => ['required', 'string', 'max:255'],
            'advantages.*.description' => ['required', 'string', 'max:1000'],
            'advantages.*.image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'advantages.*.existing_path' => ['nullable', 'string', 'max:255', Rule::in($existingAdvantagePaths)],
            'advantages.*.remove' => ['nullable', 'boolean'],
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
            'advantages.max' => 'Keunggulan maksimal 20 item.',
            'advantages_heading.required' => 'Judul bagian keunggulan wajib diisi.',
            'advantages.*.order.required' => 'Urutan keunggulan wajib diisi.',
            'advantages.*.order.integer' => 'Urutan keunggulan harus berupa angka.',
            'advantages.*.order.min' => 'Urutan keunggulan minimal 1.',
            'advantages.*.order.max' => 'Urutan keunggulan maksimal 20.',
            'advantages.*.title.required' => 'Judul keunggulan wajib diisi.',
            'advantages.*.description.required' => 'Deskripsi keunggulan wajib diisi.',
            'advantages.*.image.image' => 'Berkas keunggulan harus berupa gambar.',
            'advantages.*.image.mimes' => 'Gambar keunggulan harus berformat JPG, PNG, atau WebP.',
            'advantages.*.image.max' => 'Ukuran gambar keunggulan maksimal 4 MB.',
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

                // Verifikasi MIME asli agar file polyglot (gambar + script) tidak tersimpan.
                foreach (['slides' => 'file', 'advantages' => 'image'] as $group => $field) {
                    foreach ((array) $this->file($group, []) as $index => $file) {
                        if (! $file) {
                            continue;
                        }

                        $mime = strtolower((string) $file->getMimeType());

                        if (! in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
                            $label = $group === 'slides' ? 'Berkas slide' : 'Gambar keunggulan';
                            $validator->errors()->add("{$group}.{$index}.{$field}", "{$label} harus berupa gambar asli (JPG/PNG/WebP).");
                        }
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

        $advantages = collect((array) $this->input('advantages', []))
            ->map(function (mixed $advantage, int $index): array {
                $advantage = is_array($advantage) ? $advantage : [];
                $removing = filter_var($advantage['remove'] ?? false, FILTER_VALIDATE_BOOL);

                $clean = [
                    ...$advantage,
                    'order' => (string) ($advantage['order'] ?? '') !== '' ? $advantage['order'] : $index + 1,
                    // Baris yang dihapus (tombstone) membawa judul/deskripsi kosong dari JS —
                    // nilai sentinel menjaga rule `required`; controller membuang baris tersebut.
                    'title' => $removing ? '-' : trim((string) ($advantage['title'] ?? '')),
                    'description' => $removing ? '-' : trim((string) ($advantage['description'] ?? '')),
                    'existing_path' => trim((string) ($advantage['existing_path'] ?? '')) ?: null,
                    'remove' => $removing,
                ];

                if (! $this->hasFile("advantages.{$index}.image")) {
                    unset($clean['image']);
                }

                return $clean;
            })
            ->all();

        $this->merge([
            'hero_title' => trim(str_replace(["\r\n", "\r"], "\n", (string) $this->input('hero_title'))),
            'hero_subtitle' => trim((string) $this->input('hero_subtitle')),
            'cta_text' => trim((string) $this->input('cta_text')),
            'cta_link' => trim((string) $this->input('cta_link')),
            'welcome_title' => trim((string) $this->input('welcome_title')),
            'welcome_description' => trim((string) $this->input('welcome_description')),
            'advantages_heading' => trim((string) $this->input('advantages_heading')) ?: HomeSection::DEFAULT_ADVANTAGE_HEADING,
            'slides' => $slides,
            'advantages' => $advantages,
        ]);
    }
}
