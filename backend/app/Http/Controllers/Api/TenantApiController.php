<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;

class TenantApiController extends Controller
{
    /**
     * Obtener información pública del tenant/tienda.
     */
    public function info(Tenant $tenant): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'name' => $tenant->name,
                'slug' => $tenant->slug,
                'domain' => $tenant->domain,
                'branding' => [
                    'name' => $tenant->getSetting('brand.name', $tenant->name),
                    'tagline' => $tenant->getSetting('brand.tagline'),
                    'logo_url' => $tenant->getSetting('brand.logo_url') 
                        ? asset('storage/' . $tenant->getSetting('brand.logo_url')) 
                        : null,
                    'primary_color' => $tenant->getSetting('brand.primary_color', '#4f46e5'),
                ],
                'contact' => [
                    'email' => $tenant->getSetting('contact.email'),
                    'phone' => $tenant->getSetting('contact.phone'),
                    'address' => $tenant->getSetting('contact.address'),
                ],
                'social' => [
                    'whatsapp' => $tenant->getSetting('social.whatsapp'),
                    'instagram' => $tenant->getSetting('social.instagram'),
                    'facebook' => $tenant->getSetting('social.facebook'),
                ],
                'store_url' => route('storefront.home', $tenant),
            ],
        ]);
    }
}
