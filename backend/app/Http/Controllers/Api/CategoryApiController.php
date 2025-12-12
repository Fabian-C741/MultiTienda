<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Tenant\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryApiController extends Controller
{
    /**
     * Listar todas las categorías de un tenant.
     */
    public function index(Request $request, Tenant $tenant): JsonResponse
    {
        $query = Category::query()
            ->where('is_active', true)
            ->withCount('products');

        // Solo raíces (sin padre)
        if ($request->boolean('roots_only')) {
            $query->whereNull('parent_id');
        }

        // Incluir hijos
        if ($request->boolean('with_children')) {
            $query->with(['children' => function ($q) {
                $q->where('is_active', true)
                  ->withCount('products')
                  ->orderBy('sort_order');
            }]);
        }

        $categories = $query->orderBy('sort_order')->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    /**
     * Obtener una categoría con sus productos.
     */
    public function show(Tenant $tenant, string $slug): JsonResponse
    {
        $category = Category::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->with(['children' => function ($q) {
                $q->where('is_active', true)->orderBy('sort_order');
            }])
            ->withCount('products')
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $category,
        ]);
    }

    /**
     * Obtener productos de una categoría.
     */
    public function products(Request $request, Tenant $tenant, string $slug): JsonResponse
    {
        $category = Category::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $query = $category->products()
            ->where('status', 'published')
            ->with('category:id,name,slug');

        // Ordenamiento
        $sortField = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $allowedSorts = ['name', 'price', 'created_at'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortOrder === 'asc' ? 'asc' : 'desc');
        }

        $perPage = min((int) $request->get('per_page', 20), 100);
        $products = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
            ],
            'data' => $products->items(),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    /**
     * Árbol completo de categorías.
     */
    public function tree(Tenant $tenant): JsonResponse
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->with(['children' => function ($q) {
                $q->where('is_active', true)
                  ->with(['children' => function ($q2) {
                      $q2->where('is_active', true)->orderBy('sort_order');
                  }])
                  ->orderBy('sort_order');
            }])
            ->withCount('products')
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }
}
