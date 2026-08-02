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
            'accreditation' => ['required', 'string', 'max:255'],
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
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'history' => trim((string) $this->input('history')),
            'description' => trim((string) $this->input('description')),
            'vision' => trim((string) $this->input('vision')),
            'mission' => trim((string) $this->input('mission')),
            'goals' => trim((string) $this->input('goals')),
            'accreditation' => trim((string) $this->input('accreditation')),
            'advantages' => trim((string) $this->input('advantages')),
        ]);
    }
}
