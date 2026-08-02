<?php

namespace App\Http\Requests\Admin;

use App\Models\Lecturer;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLecturerRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $lecturer = $this->route('lecturer');

        return [
            'name' => ['required', 'string', 'max:255'],
            'nidn' => [
                'required',
                'string',
                'regex:/^[0-9]{8,20}$/',
                Rule::unique(Lecturer::class, 'nidn')->ignore($lecturer),
            ],
            'position' => ['nullable', 'string', 'max:255'],
            'expertise' => ['nullable', 'string', 'max:255'],
            'education' => ['nullable', 'string', 'max:255'],
            'email' => [
                'nullable',
                'email:rfc',
                'max:255',
                Rule::unique(Lecturer::class, 'email')->ignore($lecturer),
            ],
            'bio' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', Rule::in(Lecturer::STATUSES)],
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'photo' => ['nullable', 'sometimes', 'image', 'mimes:jpeg,png,gif,webp', 'max:2048'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'nidn.regex' => 'NIDN harus terdiri dari 8 sampai 20 angka.',
            'nidn.unique' => 'NIDN sudah digunakan dosen lain.',
            'email.unique' => 'Alamat surel sudah digunakan dosen lain.',
            'sort_order.max' => 'Urutan tampil maksimal 9.999.',
            'photo.image' => 'File harus berupa gambar.',
            'photo.mimes' => 'Format foto harus JPEG, PNG, GIF, atau WebP.',
            'photo.max' => 'Ukuran foto maksimal 2 MB.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->input('name')),
            'nidn' => trim((string) $this->input('nidn')),
            'position' => trim((string) $this->input('position')) ?: null,
            'expertise' => trim((string) $this->input('expertise')) ?: null,
            'education' => trim((string) $this->input('education')) ?: null,
            'email' => strtolower(trim((string) $this->input('email'))) ?: null,
            'bio' => trim((string) $this->input('bio')) ?: null,
            'status' => strtolower(trim((string) $this->input('status'))),
        ]);
    }
}
