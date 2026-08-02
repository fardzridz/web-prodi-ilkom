<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateContactRequest extends FormRequest
{
    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'address' => ['required', 'string', 'max:5000'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9+().\-\s]{6,30}$/'],
            'instagram' => ['nullable', 'url:http,https', 'max:2048'],
            'youtube' => ['nullable', 'url:http,https', 'max:2048'],
            'facebook' => ['nullable', 'url:http,https', 'max:2048'],
            'map_embed' => ['nullable', 'url:https', 'max:5000'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'address.required' => 'Alamat kampus wajib diisi.',
            'email.required' => 'Surel program studi wajib diisi.',
            'email.email' => 'Format surel program studi tidak valid.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.regex' => 'Nomor telepon hanya boleh berisi angka, spasi, tanda +, titik, kurung, atau tanda hubung.',
            'instagram.url' => 'Tautan Instagram harus berupa URL HTTP/HTTPS yang valid.',
            'youtube.url' => 'Tautan YouTube harus berupa URL HTTP/HTTPS yang valid.',
            'facebook.url' => 'Tautan Facebook harus berupa URL HTTP/HTTPS yang valid.',
            'map_embed.url' => 'Sematan peta harus berupa URL HTTPS Google Maps atau kode iframe Google Maps.',
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $socialHosts = [
                    'instagram' => ['instagram.com'],
                    'youtube' => ['youtube.com', 'youtu.be'],
                    'facebook' => ['facebook.com', 'fb.com'],
                ];

                foreach ($socialHosts as $field => $hosts) {
                    $value = $this->input($field);

                    if ($value && ! $this->hasAllowedHost($value, $hosts)) {
                        $validator->errors()->add($field, "Tautan {$field} harus menggunakan domain resmi {$field}.");
                    }
                }

                $mapUrl = $this->input('map_embed');

                if ($mapUrl && (! $this->hasAllowedHost($mapUrl, ['google.com', 'google.co.id']) || ! str_starts_with((string) parse_url($mapUrl, PHP_URL_PATH), '/maps/embed'))) {
                    $validator->errors()->add('map_embed', 'Sematan peta harus berasal dari URL /maps/embed Google Maps.');
                }
            },
        ];
    }

    protected function prepareForValidation(): void
    {
        $mapEmbed = trim((string) $this->input('map_embed'));

        if (preg_match('/<iframe\b[^>]*\bsrc\s*=\s*(["\'])(.*?)\1/is', $mapEmbed, $matches) === 1) {
            $mapEmbed = html_entity_decode($matches[2], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        $this->merge([
            'address' => trim((string) $this->input('address')),
            'email' => strtolower(trim((string) $this->input('email'))),
            'phone' => trim((string) $this->input('phone')),
            'instagram' => trim((string) $this->input('instagram')) ?: null,
            'youtube' => trim((string) $this->input('youtube')) ?: null,
            'facebook' => trim((string) $this->input('facebook')) ?: null,
            'map_embed' => $mapEmbed ?: null,
        ]);
    }

    /** @param array<int, string> $allowedHosts */
    private function hasAllowedHost(string $url, array $allowedHosts): bool
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));

        foreach ($allowedHosts as $allowedHost) {
            if ($host === $allowedHost || str_ends_with($host, ".{$allowedHost}")) {
                return true;
            }
        }

        return false;
    }
}
