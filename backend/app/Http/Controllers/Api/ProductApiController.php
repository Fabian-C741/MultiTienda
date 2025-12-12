<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductApiController extends Controller
{
    /**
     * Listar productos de un tenant.
     */
    public function index(Request $request, Tenant $tenant): JsonResponse
    {
        $query = Product::query()
            ->where('status', 'published')
            ->with('category:id,name,slug');

        // Filtro por categoría
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('category_slug')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category_slug));
        }

        // Búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Filtro por precio
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Solo con stock
        if ($request->boolean('in_stock')) {
            $query->where('stock', '>', 0);
        }

        // Productos destacados
        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        // Ordenamiento
        $sortField = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $allowedSorts = ['name', 'price', 'created_at', 'stock'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortOrder === 'asc' ? 'asc' : 'desc');
        }

        // Paginación
        $perPage = min((int) $request->get('per_page', 20), 100);
        $products = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $products->items(),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
            'links' => [
                'first' => $products->url(1),
                'last' => $products->url($products->lastPage()),
                'prev' => $products->previousPageUrl(),
                'next' => $products->nextPageUrl(),
            ],
        ]);
    }

    /**
     * Obtener un producto por slug o ID.
     */
    public function show(Tenant $tenant, string $identifier): JsonResponse
    {
        $product = Product::query()
            ->where('status', 'published')
            ->with(['category:id,name,slug', 'media'])
            ->where(function ($q) use ($identifier) {
                $q->where('slug', $identifier)
                  ->orWhere('id', $identifier)
                  ->orWhere('sku', $identifier);
            })
            ->firstOrFail();

        // Productos relacionados
        $related = Product::query()
            ->where('status', 'published')
            ->where('id', '!=', $product->id)
            ->when($product->category_id, fn($q) => $q->where('category_id', $product->category_id))
            ->limit(4)
            ->get(['id', 'name', 'slug', 'price', 'image_url']);

        return response()->json([
            'success' => true,
            'data' => $product,
            'related' => $related,
        ]);
    }

    /**
     * Obtener productos destacados.
     */
    public function featured(Tenant $tenant): JsonResponse
    {
        $products = Product::query()
            ->where('status', 'published')
            ->where('is_featured', true)
            ->with('category:id,name,slug')
            ->limit(12)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    /**
     * Obtener últimos productos.
     */
    public function latest(Tenant $tenant): JsonResponse
    {
        $products = Product::query()
            ->where('status', 'published')
            ->with('category:id,name,slug')
            ->latest()
            ->limit(12)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }
}
