<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

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
        ]);
    }
}
