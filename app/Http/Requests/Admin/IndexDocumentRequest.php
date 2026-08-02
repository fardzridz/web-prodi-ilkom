<?php

namespace App\Http\Requests\Admin;

use App\Models\Document;
use App\Models\DocumentCategory;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexDocumentRequest extends FormRequest
{
    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'document_category_id' => ['nullable', 'integer', Rule::exists(DocumentCategory::class, 'id')],
            'status' => ['nullable', Rule::in(Document::STATUSES)],
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
