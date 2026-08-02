<?php

namespace App\Http\Requests\Admin;

use App\Models\DocumentCategory;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexDocumentCategoryRequest extends FormRequest
{
    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'edit' => ['nullable', 'integer', Rule::exists(DocumentCategory::class, 'id')],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
