<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreProductRequest;
use App\Http\Requests\Tenant\UpdateProductRequest;
use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Tenant $tenant): View
    {
        $products = Product::query()
            ->with('category')
            ->latest()
            ->paginate(12);

        return view('tenant.products.index', compact('tenant', 'products'));
    }

    public function create(Tenant $tenant): View
    {
        return view('tenant.products.create', compact('tenant'));
    }

    public function store(StoreProductRequest $request, Tenant $tenant): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store("tenants/{$tenant->slug}/products", 'public');
            $imageUrl = $path;
        }

        Product::query()->create([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'sku' => $data['sku'] ?? null,
            'category_id' => $data['category_id'] ?? null,
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'compare_price' => $data['compare_price'] ?? null,
            'stock' => $data['stock'] ?? 0,
            'status' => $data['status'],
            'is_featured' => $data['is_featured'] ?? false,
            'image_url' => $imageUrl,
        ]);

        return Redirect::route('tenant.products.index', ['tenant' => $tenant])
            ->with('status', __('Producto creado correctamente.'));
    }

    public function edit(Tenant $tenant, Product $product): View
    {
        return view('tenant.products.edit', compact('tenant', 'product'));
    }

    public function update(UpdateProductRequest $request, Tenant $tenant, Product $product): RedirectResponse
    {
        $data = $request->validated();

        $updateData = [
            'name' => $data['name'],
            'slug' => $data['slug'] ?: Str::slug($data['name']),
            'sku' => $data['sku'] ?? null,
            'category_id' => $data['category_id'] ?? null,
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'compare_price' => $data['compare_price'] ?? null,
            'stock' => $data['stock'] ?? 0,
            'status' => $data['status'],
            'is_featured' => $data['is_featured'] ?? false,
        ];

        if ($request->hasFile('image')) {
            // Eliminar imagen anterior si existe
            if ($product->image_url && Storage::disk('public')->exists($product->image_url)) {
                Storage::disk('public')->delete($product->image_url);
            }
            $path = $request->file('image')->store("tenants/{$tenant->slug}/products", 'public');
            $updateData['image_url'] = $path;
        }

        if ($request->boolean('remove_image')) {
            if ($product->image_url && Storage::disk('public')->exists($product->image_url)) {
                Storage::disk('public')->delete($product->image_url);
            }
            $updateData['image_url'] = null;
        }

        $product->update($updateData);

        return Redirect::route('tenant.products.edit', ['tenant' => $tenant, 'product' => $product])
            ->with('status', __('Producto actualizado.'));
    }

    public function destroy(Tenant $tenant, Product $product): RedirectResponse
    {
        // Eliminar imagen si existe
        if ($product->image_url && Storage::disk('public')->exists($product->image_url)) {
            Storage::disk('public')->delete($product->image_url);
        }

        $product->delete();

        return Redirect::route('tenant.products.index', ['tenant' => $tenant])
            ->with('status', __('Producto eliminado.'));
    }
}
