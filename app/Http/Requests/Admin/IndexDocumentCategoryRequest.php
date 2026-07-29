<?php

namespace App\Http\Requests\Admin;

use App\Models\DocumentCategory;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexDocumentCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === User::ROLE_ADMIN;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'edit' => ['nullable', 'integer', Rule::exists(DocumentCategory::class, 'id')],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
