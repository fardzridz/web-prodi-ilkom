<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === User::ROLE_ADMIN;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email:rfc',
                'max:255',
                Rule::unique(User::class, 'email')->ignore($this->user()?->getAuthIdentifier()),
            ],
            'current_password' => ['required', 'current_password'],
            'password' => [
                'nullable',
                'confirmed',
                Password::min(10)->mixedCase()->letters()->numbers(),
            ],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat surel wajib diisi.',
            'email.email' => 'Format alamat surel tidak valid.',
            'email.unique' => 'Alamat surel sudah digunakan akun lain.',
            'current_password.required' => 'Kata sandi saat ini wajib diisi untuk menyimpan perubahan.',
            'current_password.current_password' => 'Kata sandi saat ini tidak sesuai.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak sesuai.',
            'password.min' => 'Kata sandi baru minimal 10 karakter.',
            'password.mixed' => 'Kata sandi baru harus mengandung huruf besar dan kecil.',
            'password.letters' => 'Kata sandi baru harus mengandung huruf.',
            'password.numbers' => 'Kata sandi baru harus mengandung angka.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $password = (string) $this->input('password');

        $this->merge([
            'name' => trim((string) $this->input('name')),
            'email' => strtolower(trim((string) $this->input('email'))),
            'password' => $password !== '' ? $password : null,
        ]);
    }
}
