<?php

namespace App\Http\Requests\Admin;

use App\Models\DocumentCategory;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateDocumentCategoryRequest extends FormRequest
{
    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        $documentCategory = $this->route('documentCategory');

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique(DocumentCategory::class, 'slug')->ignore($documentCategory),
            ],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return ['slug.unique' => 'Slug sudah digunakan kategori lain.'];
    }

    protected function prepareForValidation(): void
    {
        $name = trim((string) $this->input('name'));
        $slug = trim((string) $this->input('slug'));

        $this->merge([
            'name' => $name,
            'slug' => Str::slug($slug ?: $name),
        ]);
    }
}
