<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateJournalUrlRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === User::ROLE_ADMIN;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'journal_url' => ['required', 'url:http,https', 'max:2048'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'journal_url.required' => 'Alamat e-jurnal wajib diisi.',
            'journal_url.url' => 'Alamat e-jurnal harus berupa URL HTTP/HTTPS yang valid.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'journal_url' => trim((string) $this->input('journal_url')),
        ]);
    }
}
