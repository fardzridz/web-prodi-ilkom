<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateProgramProfileRequest extends FormRequest
{
    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'history' => ['required', 'string', 'max:50000'],
            'description' => ['required', 'string', 'max:50000'],
            'vision' => ['required', 'string', 'max:5000'],
            'mission' => ['required', 'string', 'max:50000'],
            'goals' => ['required', 'string', 'max:50000'],
            // Strip Unicode bidi / zero-width control chars to defeat RTL override
            // spoofing. Allow letters, digits, whitespace, and a narrow punctuation
            // set typical of BAN-PT accreditation labels ("Baik Sekali", "A",
            // "Unggul (2024)").
            'accreditation' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\pL\pN\s\-\/\.,()]+$/u',
            ],
            'advantages' => ['required', 'string', 'max:50000'],
            'description_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'history_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'goals_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'advantages_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'description_image_remove' => ['nullable', 'boolean'],
            'history_image_remove' => ['nullable', 'boolean'],
            'goals_image_remove' => ['nullable', 'boolean'],
            'advantages_image_remove' => ['nullable', 'boolean'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'history.required' => 'Sejarah program studi wajib diisi.',
            'description.required' => 'Deskripsi program studi wajib diisi.',
            'vision.required' => 'Visi program studi wajib diisi.',
            'mission.required' => 'Misi program studi wajib diisi.',
            'goals.required' => 'Tujuan program studi wajib diisi.',
            'accreditation.required' => 'Akreditasi program studi wajib diisi.',
            'advantages.required' => 'Keunggulan program studi wajib diisi.',
            'vision.max' => 'Visi maksimal 5.000 karakter.',
            'accreditation.max' => 'Akreditasi maksimal 255 karakter.',
            'accreditation.regex' => 'Akreditasi hanya boleh berisi huruf, angka, dan tanda baca umum.',
            'description_image.image' => 'Berkas gambar deskripsi harus berupa gambar.',
            'description_image.mimes' => 'Gambar deskripsi harus berformat JPG, PNG, atau WebP.',
            'description_image.max' => 'Ukuran gambar deskripsi maksimal 4 MB.',
            'history_image.image' => 'Berkas gambar sejarah harus berupa gambar.',
            'history_image.mimes' => 'Gambar sejarah harus berformat JPG, PNG, atau WebP.',
            'history_image.max' => 'Ukuran gambar sejarah maksimal 4 MB.',
            'goals_image.image' => 'Berkas gambar tujuan harus berupa gambar.',
            'goals_image.mimes' => 'Gambar tujuan harus berformat JPG, PNG, atau WebP.',
            'goals_image.max' => 'Ukuran gambar tujuan maksimal 4 MB.',
            'advantages_image.image' => 'Berkas gambar keunggulan harus berupa gambar.',
            'advantages_image.mimes' => 'Gambar keunggulan harus berformat JPG, PNG, atau WebP.',
            'advantages_image.max' => 'Ukuran gambar keunggulan maksimal 4 MB.',
        ];
    }

    /**
     * Verifikasi MIME asli agar file polyglot (gambar + script) tidak tersimpan.
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                foreach ([
                    'description_image' => 'Gambar deskripsi',
                    'history_image' => 'Gambar sejarah',
                    'goals_image' => 'Gambar tujuan',
                    'advantages_image' => 'Gambar keunggulan',
                ] as $field => $label) {
                    $file = $this->file($field);

                    if (! $file) {
                        continue;
                    }

                    $mime = strtolower((string) $file->getMimeType());

                    if (! in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
                        $validator->errors()->add($field, "{$label} harus berupa gambar asli (JPG/PNG/WebP).");
                    }
                }
            },
        ];
    }

    protected function prepareForValidation(): void
    {
        $stripControl = static function (string $value): string {
            // Drop Unicode bidi controls and zero-width / format chars.
            $cleaned = preg_replace(
                '/[\x{200B}-\x{200F}\x{202A}-\x{202E}\x{2060}-\x{206F}\x{FEFF}]/u',
                '',
                $value,
            );

            return trim($cleaned ?? $value);
        };

        $this->merge([
            'history' => $stripControl(trim((string) $this->input('history'))),
            'description' => $stripControl(trim((string) $this->input('description'))),
            'vision' => $stripControl(trim((string) $this->input('vision'))),
            'mission' => $stripControl(trim((string) $this->input('mission'))),
            'goals' => $stripControl(trim((string) $this->input('goals'))),
            'accreditation' => $stripControl(trim((string) $this->input('accreditation'))),
            'advantages' => $stripControl(trim((string) $this->input('advantages'))),
            'description_image_remove' => filter_var($this->input('description_image_remove'), FILTER_VALIDATE_BOOL),
            'history_image_remove' => filter_var($this->input('history_image_remove'), FILTER_VALIDATE_BOOL),
            'goals_image_remove' => filter_var($this->input('goals_image_remove'), FILTER_VALIDATE_BOOL),
            'advantages_image_remove' => filter_var($this->input('advantages_image_remove'), FILTER_VALIDATE_BOOL),
        ]);
    }
}
