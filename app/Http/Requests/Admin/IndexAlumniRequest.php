<?php

namespace App\Http\Requests\Admin;

use App\Models\Alumni;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexAlumniRequest extends FormRequest
{
    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(Alumni::STATUSES)],
            'batch_year' => ['nullable', 'integer', 'min:1950', 'max:'.now()->addYear()->year],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'search' => trim((string) $this->input('search')) ?: null,
            'status' => strtolower(trim((string) $this->input('status'))) ?: null,
        ]);
    }
}
