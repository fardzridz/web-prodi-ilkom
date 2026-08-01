<?php

namespace App\Http\Requests\Admin;

use App\Models\Alumni;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAlumniRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === User::ROLE_ADMIN;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        $maximumYear = now()->addYear()->year;

        return [
            'name' => ['required', 'string', 'max:255'],
            'batch_year' => ['required', 'integer', 'min:1950', "max:{$maximumYear}"],
            'graduation_year' => [
                'nullable',
                'integer',
                'min:1950',
                "max:{$maximumYear}",
                'gte:batch_year',
            ],
            'job_position' => ['nullable', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'testimonial' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', Rule::in(Alumni::STATUSES)],
            'photo' => ['nullable', 'sometimes', 'image', 'mimes:jpeg,png,gif,webp', 'max:2048'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'batch_year.required' => 'Tahun angkatan wajib diisi.',
            'batch_year.min' => 'Tahun angkatan minimal 1950.',
            'batch_year.max' => 'Tahun angkatan tidak boleh melebihi tahun depan.',
            'graduation_year.gte' => 'Tahun lulus tidak boleh sebelum tahun angkatan.',
            'graduation_year.max' => 'Tahun lulus tidak boleh melebihi tahun depan.',
            'testimonial.max' => 'Testimoni maksimal 5.000 karakter.',
            'photo.image' => 'File harus berupa gambar.',
            'photo.mimes' => 'Format foto harus JPEG, PNG, GIF, atau WebP.',
            'photo.max' => 'Ukuran foto maksimal 2 MB.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->input('name')),
            'job_position' => trim((string) $this->input('job_position')) ?: null,
            'company' => trim((string) $this->input('company')) ?: null,
            'testimonial' => trim((string) $this->input('testimonial')) ?: null,
            'status' => strtolower(trim((string) $this->input('status'))),
        ]);
    }
}
