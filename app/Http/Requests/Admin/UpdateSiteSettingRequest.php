<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class UpdateSiteSettingRequest extends FormRequest
{
    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'site_name' => ['required', 'string', 'max:255'],
            'university_name' => ['required', 'string', 'max:255'],
            'faculty_name' => ['required', 'string', 'max:255'],
            'journal_url' => ['required', 'url:http,https', 'max:2048'],
            'registration_url' => ['required', 'url:http,https', 'max:2048'],
            'footer_text' => ['required', 'string', 'max:5000'],
            'footer_links' => ['nullable', 'array', 'max:10'],
            'footer_links.*.label' => ['required', 'string', 'max:255'],
            'footer_links.*.url' => ['required', 'url:http,https', 'max:2048'],
            'logo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,gif,webp',
                function (string $attribute, mixed $value, callable $fail): void {
                    if (! $value instanceof UploadedFile) {
                        return;
                    }

                    $limit = $value->getClientOriginalExtension() === 'gif' ? 7168 * 1024 : 2048 * 1024;

                    if ($value->getSize() > $limit) {
                        $fail('Ukuran logo maksimal '.($limit / 1024).' KB.');
                    }
                },
            ],
            'favicon' => ['nullable', 'file', 'mimes:ico,png', 'max:512'],
            'remove_logo' => ['nullable', 'boolean'],
            'remove_favicon' => ['nullable', 'boolean'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'site_name.required' => 'Nama situs wajib diisi.',
            'university_name.required' => 'Nama universitas wajib diisi.',
            'faculty_name.required' => 'Nama fakultas wajib diisi.',
            'journal_url.required' => 'Alamat e-jurnal wajib diisi.',
            'journal_url.url' => 'Alamat e-jurnal harus berupa URL HTTP/HTTPS yang valid.',
            'registration_url.required' => 'Tautan pendaftaran wajib diisi.',
            'registration_url.url' => 'Tautan pendaftaran harus berupa URL HTTP/HTTPS yang valid.',
            'footer_text.required' => 'Teks footer wajib diisi.',
            'footer_links.max' => 'Tautan akademik maksimal 10 item.',
            'footer_links.*.label.required' => 'Label tautan akademik wajib diisi.',
            'footer_links.*.url.url' => 'Alamat tautan akademik harus berupa URL HTTP/HTTPS yang valid.',
            'logo.image' => 'Logo harus berupa gambar.',
            'logo.mimes' => 'Logo harus berformat JPG, PNG, GIF, atau WebP.',
            'favicon.mimes' => 'Favicon harus berformat ICO atau PNG.',
            'favicon.max' => 'Ukuran favicon maksimal 512 KB.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $links = collect((array) $this->input('footer_links', []))
            ->map(fn (mixed $link): array => [
                'label' => trim((string) (is_array($link) ? ($link['label'] ?? '') : '')),
                'url' => trim((string) (is_array($link) ? ($link['url'] ?? '') : '')),
            ])
            ->filter(fn (array $link): bool => $link['label'] !== '' || $link['url'] !== '')
            ->values()
            ->all();

        $this->merge([
            'site_name' => trim((string) $this->input('site_name')),
            'university_name' => trim((string) $this->input('university_name')),
            'faculty_name' => trim((string) $this->input('faculty_name')),
            'journal_url' => trim((string) $this->input('journal_url')),
            'registration_url' => trim((string) $this->input('registration_url')),
            'footer_text' => trim((string) $this->input('footer_text')),
            'footer_links' => $links,
            'remove_logo' => $this->boolean('remove_logo'),
            'remove_favicon' => $this->boolean('remove_favicon'),
        ]);
    }
}
