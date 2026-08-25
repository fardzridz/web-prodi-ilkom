<?php

namespace App\Http\Requests\Public;

use Illuminate\Foundation\Http\FormRequest;

class AlumniIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:100'],
            'job' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:100'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'q' => $this->sanitizedSearch($this->input('q')),
            'job' => $this->sanitizedCategory($this->input('job', $this->input('category'))),
        ]);
    }

    public function search(): ?string
    {
        /** @var string|null $value */
        $value = $this->input('q');

        return $value !== '' ? $value : null;
    }

    public function job(): ?string
    {
        /** @var string|null $value */
        $value = $this->input('job');

        return $value !== '' ? $value : null;
    }

    private function sanitizedSearch(mixed $raw, int $maxLength = 100): ?string
    {
        if ($raw === null) {
            return null;
        }

        $raw = trim((string) $raw);

        if ($raw === '') {
            return null;
        }

        $raw = strip_tags($raw);
        $raw = (string) preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $raw);
        $raw = mb_substr($raw, 0, $maxLength);
        $raw = trim($raw);

        return $raw === '' ? null : $raw;
    }

    private function sanitizedCategory(mixed $raw, int $maxLength = 100): ?string
    {
        if ($raw === null) {
            return null;
        }

        $raw = trim((string) $raw);

        if ($raw === '' || strtolower($raw) === 'semua') {
            return null;
        }

        $raw = strip_tags($raw);
        $raw = (string) preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $raw);
        $raw = mb_substr($raw, 0, $maxLength);
        $raw = trim($raw);

        return $raw === '' ? null : $raw;
    }
}
