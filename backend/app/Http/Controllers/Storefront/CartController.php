<?php

declare(strict_types=1);

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\Tenant\Cart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class CartController extends Controller
{
    public function index(Tenant $tenant): View
    {
        $cart = $this->getCart();
        $cart->load('items.product');

        return view('storefront.cart.index', compact('tenant', 'cart'));
    }

    public function add(Request $request, Tenant $tenant): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $product = Product::findOrFail($validated['product_id']);

        if (!$product->isInStock()) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Producto sin stock'], 400);
            }
            return back()->with('error', 'Este producto no tiene stock disponible.');
        }

        $cart = $this->getCart();
        $quantity = $validated['quantity'] ?? 1;
        $item = $cart->addProduct($product, $quantity);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Producto agregado al carrito',
                'cart_count' => $cart->fresh()->item_count,
                'item' => $item,
            ]);
        }

        return back()->with('status', 'Producto agregado al carrito.');
    }

    public function update(Request $request, Tenant $tenant): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $cart = $this->getCart();
        $cart->updateQuantity($validated['product_id'], $validated['quantity']);

        if ($request->wantsJson()) {
            $cart->refresh()->load('items.product');
            return response()->json([
                'success' => true,
                'cart_count' => $cart->item_count,
                'subtotal' => $cart->subtotal,
            ]);
        }

        return back()->with('status', 'Carrito actualizado.');
    }

    public function remove(Request $request, Tenant $tenant): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
        ]);

        $cart = $this->getCart();
        $cart->removeProduct($validated['product_id']);

        if ($request->wantsJson()) {
            $cart->refresh();
            return response()->json([
                'success' => true,
                'cart_count' => $cart->item_count,
                'subtotal' => $cart->subtotal,
            ]);
        }

        return back()->with('status', 'Producto eliminado del carrito.');
    }

    public function clear(Tenant $tenant): RedirectResponse
    {
        $cart = $this->getCart();
        $cart->clear();

        return back()->with('status', 'Carrito vaciado.');
    }

    protected function getCart(): Cart
    {
        $sessionId = session()->getId();
        return Cart::getOrCreate($sessionId);
    }
}
