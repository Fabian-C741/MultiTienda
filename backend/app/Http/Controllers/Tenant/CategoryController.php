<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::query()
            ->roots()
            ->with('children')
            ->ordered()
            ->paginate(20);

        return view('tenant.categories.index', compact('categories'));
    }

    public function create(): View
    {
        $parentCategories = Category::roots()->ordered()->get();

        return view('tenant.categories.create', compact('parentCategories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('categories', 'slug')],
            'description' => ['nullable', 'string', 'max:1000'],
            'parent_id' => ['nullable', 'exists:categories,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            $tenant = app('tenant.manager')->current();
            $path = $request->file('image')->store("tenants/{$tenant->id}/categories", 'public');
            $validated['image_url'] = $path;
        }

        Category::create($validated);

        return Redirect::route('tenant.categories.index')
            ->with('status', 'Categoría creada correctamente.');
    }

    public function edit(Category $category): View
    {
        $parentCategories = Category::roots()
            ->where('id', '!=', $category->id)
            ->ordered()
            ->get();

        return view('tenant.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('categories', 'slug')->ignore($category->id)],
            'description' => ['nullable', 'string', 'max:1000'],
            'parent_id' => ['nullable', 'exists:categories,id', Rule::notIn([$category->id])],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            $tenant = app('tenant.manager')->current();
            $path = $request->file('image')->store("tenants/{$tenant->id}/categories", 'public');
            $validated['image_url'] = $path;
        }

        $category->update($validated);

        return Redirect::back()->with('status', 'Categoría actualizada correctamente.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        // Mover productos a sin categoría
        $category->products()->update(['category_id' => null]);

        // Mover subcategorías al nivel raíz
        $category->children()->update(['parent_id' => null]);

        $category->delete();

        return Redirect::route('tenant.categories.index')
            ->with('status', 'Categoría eliminada correctamente.');
    }
}
