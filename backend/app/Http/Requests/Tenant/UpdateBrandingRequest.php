<?php

declare(strict_types=1);

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBrandingRequest extends FormRequest
{
    public function authorize(): bool
    {
        $tenant = $this->route('tenant');

        return $this->user('tenant')?->isTenantAdminFor($tenant) ?? false;
    }

    public function rules(): array
    {
        return [
            'brand.name' => ['required', 'string', 'max:255'],
            'brand.tagline' => ['nullable', 'string', 'max:255'],
            'theme.primary_color' => ['nullable', 'regex:/^#?[0-9A-Fa-f]{6}$/'],
            'theme.secondary_color' => ['nullable', 'regex:/^#?[0-9A-Fa-f]{6}$/'],
            'appearance.logo' => ['nullable', 'image', 'max:2048'],
            'appearance.favicon' => ['nullable', 'image', 'max:1024'],
            'footer.text' => ['nullable', 'string', 'max:255'],
            'footer.links' => ['nullable', 'array'],
            'footer.links.*.label' => ['nullable', 'string', 'max:255'],
            'footer.links.*.url' => ['nullable', 'url', 'max:255'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('theme.primary_color')) {
            $this->merge([
                'theme' => array_merge($this->input('theme', []), [
                    'primary_color' => $this->normalizeColor($this->input('theme.primary_color')),
                ]),
            ]);
        }

        if ($this->filled('theme.secondary_color')) {
            $this->merge([
                'theme' => array_merge($this->input('theme', []), [
                    'secondary_color' => $this->normalizeColor($this->input('theme.secondary_color')),
                ]),
            ]);
        }
    }

    private function normalizeColor(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        $value = ltrim($value, '#');

        return '#' . strtoupper($value);
    }
}
