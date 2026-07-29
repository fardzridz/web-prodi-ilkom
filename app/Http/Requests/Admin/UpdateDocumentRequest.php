<?php

namespace App\Http\Requests\Admin;

use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === User::ROLE_ADMIN;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        $document = $this->route('document');

        return [
            'document_category_id' => ['required', 'integer', Rule::exists(DocumentCategory::class, 'id')],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique(Document::class, 'slug')->ignore($document)],
            'description' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', Rule::in(Document::STATUSES)],
            'document_file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'document_category_id.required' => 'Kategori dokumen wajib dipilih.',
            'document_category_id.exists' => 'Kategori dokumen tidak valid.',
            'slug.unique' => 'Slug sudah digunakan dokumen lain.',
            'document_file.mimes' => 'Berkas harus berformat PDF, DOC, atau DOCX.',
            'document_file.max' => 'Ukuran berkas maksimal 10 MB.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $title = trim((string) $this->input('title'));
        $slug = trim((string) $this->input('slug'));

        $this->merge([
            'title' => $title,
            'slug' => Str::slug($slug ?: $title),
            'description' => trim((string) $this->input('description')) ?: null,
            'status' => strtolower(trim((string) $this->input('status'))),
        ]);
    }
}
