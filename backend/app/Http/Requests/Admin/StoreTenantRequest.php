<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('tenants', 'slug')],
            'domain' => ['nullable', 'string', 'max:255', Rule::unique('tenants', 'domain')],
            'database' => ['required', 'string', 'max:255', Rule::unique('tenants', 'database')],
            'database_host' => ['required', 'string', 'max:255'],
            'database_port' => ['required', 'string', 'max:10'],
            'database_username' => ['required', 'string', 'max:255'],
            'database_password' => ['required', 'string', 'max:255'],
            'admin.name' => ['required', 'string', 'max:255'],
            'admin.email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'admin.password' => ['required', 'string', 'min:8'],
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
