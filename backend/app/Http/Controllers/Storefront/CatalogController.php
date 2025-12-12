<?php

declare(strict_types=1);

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\Tenant\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function home(Tenant $tenant): View
    {
        $categories = Category::active()
            ->roots()
            ->ordered()
            ->withCount('products')
            ->get();

        $featuredProducts = Product::query()
            ->where('status', 'published')
            ->where('is_featured', true)
            ->limit(8)
            ->get();

        $latestProducts = Product::query()
            ->where('status', 'published')
            ->latest()
            ->limit(8)
            ->get();

        return view('storefront.home', compact('tenant', 'categories', 'featuredProducts', 'latestProducts'));
    }

    public function index(Request $request, Tenant $tenant): View
    {
        $query = Product::query()
            ->where('status', 'published')
            ->with('category');

        // Filtro por categoría
        if ($request->filled('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
        }

        // Búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Ordenamiento
        $sort = $request->get('sort', 'newest');
        $query->when($sort === 'newest', fn($q) => $q->latest())
              ->when($sort === 'price_asc', fn($q) => $q->orderBy('price', 'asc'))
              ->when($sort === 'price_desc', fn($q) => $q->orderBy('price', 'desc'))
              ->when($sort === 'name', fn($q) => $q->orderBy('name', 'asc'));

        $products = $query->paginate(12);
        $categories = Category::active()->roots()->ordered()->get();

        return view('storefront.catalog.index', compact('tenant', 'products', 'categories'));
    }

    public function show(Tenant $tenant, string $slug): View
    {
        $product = Product::query()
            ->where('slug', $slug)
            ->where('status', 'published')
            ->with(['category', 'media'])
            ->firstOrFail();

        $relatedProducts = Product::query()
            ->where('status', 'published')
            ->where('id', '!=', $product->id)
            ->when($product->category_id, fn($q) => $q->where('category_id', $product->category_id))
            ->limit(4)
            ->get();

        return view('storefront.catalog.show', compact('tenant', 'product', 'relatedProducts'));
    }

    public function category(Tenant $tenant, string $slug): View
    {
        $category = Category::where('slug', $slug)->active()->firstOrFail();

        $products = Product::query()
            ->where('status', 'published')
            ->where('category_id', $category->id)
            ->paginate(12);

        $categories = Category::active()->roots()->ordered()->get();

        return view('storefront.catalog.category', compact('tenant', 'category', 'products', 'categories'));
    }
}
