<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    public function rules(): array
    {
        $tenantId = $this->route('tenant')?->getKey();

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('tenants', 'slug')->ignore($tenantId)],
            'domain' => ['nullable', 'string', 'max:255', Rule::unique('tenants', 'domain')->ignore($tenantId)],
            'database' => ['required', 'string', 'max:255', Rule::unique('tenants', 'database')->ignore($tenantId)],
            'database_host' => ['required', 'string', 'max:255'],
            'database_port' => ['required', 'string', 'max:10'],
            'database_username' => ['required', 'string', 'max:255'],
            'database_password' => ['required', 'string', 'max:255'],
            'settings' => ['nullable', 'array'],
            'settings.brand' => ['nullable', 'array'],
            'settings.theme' => ['nullable', 'array'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active', true),
        ]);
    }
}
