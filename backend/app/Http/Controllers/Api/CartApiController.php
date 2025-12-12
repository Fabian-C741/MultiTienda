<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\Tenant\Cart;
use App\Models\Tenant\CartItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CartApiController extends Controller
{
    /**
     * Obtener carrito actual por session/token.
     */
    public function show(Request $request, Tenant $tenant): JsonResponse
    {
        $cartToken = $request->header('X-Cart-Token') ?? $request->get('cart_token');
        
        if (!$cartToken) {
            return response()->json([
                'success' => true,
                'data' => [
                    'items' => [],
                    'item_count' => 0,
                    'subtotal' => 0,
                ],
            ]);
        }

        $cart = Cart::where('session_id', $cartToken)
            ->with(['items.product:id,name,slug,price,stock,image_url'])
            ->first();

        if (!$cart) {
            return response()->json([
                'success' => true,
                'data' => [
                    'items' => [],
                    'item_count' => 0,
                    'subtotal' => 0,
                ],
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'cart_token' => $cart->session_id,
                'items' => $cart->items->map(fn($item) => [
                    'product_id' => $item->product_id,
                    'product' => $item->product,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'subtotal' => $item->subtotal,
                ]),
                'item_count' => $cart->item_count,
                'subtotal' => $cart->subtotal,
            ],
        ]);
    }

    /**
     * Agregar producto al carrito.
     */
    public function add(Request $request, Tenant $tenant): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'integer|min:1',
        ]);

        $product = Product::where('id', $request->product_id)
            ->where('status', 'published')
            ->firstOrFail();

        if (!$product->isInStock()) {
            return response()->json([
                'success' => false,
                'message' => 'Producto sin stock disponible.',
            ], 400);
        }

        $cartToken = $request->header('X-Cart-Token') ?? $request->get('cart_token');
        
        if (!$cartToken) {
            $cartToken = Str::uuid()->toString();
        }

        $cart = Cart::firstOrCreate(
            ['session_id' => $cartToken],
            ['expires_at' => now()->addDays(7)]
        );

        $quantity = (int) $request->get('quantity', 1);

        $cartItem = $cart->items()->where('product_id', $product->id)->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $quantity;
            
            if ($newQuantity > $product->stock) {
                return response()->json([
                    'success' => false,
                    'message' => "Stock insuficiente. Máximo disponible: {$product->stock}",
                ], 400);
            }

            $cartItem->update([
                'quantity' => $newQuantity,
                'subtotal' => $newQuantity * $cartItem->unit_price,
            ]);
        } else {
            if ($quantity > $product->stock) {
                return response()->json([
                    'success' => false,
                    'message' => "Stock insuficiente. Máximo disponible: {$product->stock}",
                ], 400);
            }

            $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $product->price,
                'subtotal' => $quantity * $product->price,
            ]);
        }

        $cart->load(['items.product:id,name,slug,price,stock,image_url']);

        return response()->json([
            'success' => true,
            'message' => 'Producto agregado al carrito.',
            'data' => [
                'cart_token' => $cart->session_id,
                'item_count' => $cart->item_count,
                'subtotal' => $cart->subtotal,
            ],
        ]);
    }

    /**
     * Actualizar cantidad de un producto.
     */
    public function update(Request $request, Tenant $tenant): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:0',
        ]);

        $cartToken = $request->header('X-Cart-Token') ?? $request->get('cart_token');
        
        if (!$cartToken) {
            return response()->json([
                'success' => false,
                'message' => 'Carrito no encontrado.',
            ], 404);
        }

        $cart = Cart::where('session_id', $cartToken)->first();

        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'Carrito no encontrado.',
            ], 404);
        }

        $quantity = (int) $request->quantity;

        if ($quantity === 0) {
            $cart->items()->where('product_id', $request->product_id)->delete();
        } else {
            $product = Product::findOrFail($request->product_id);

            if ($quantity > $product->stock) {
                return response()->json([
                    'success' => false,
                    'message' => "Stock insuficiente. Máximo disponible: {$product->stock}",
                ], 400);
            }

            $cart->items()->where('product_id', $request->product_id)->update([
                'quantity' => $quantity,
                'subtotal' => $quantity * $product->price,
            ]);
        }

        $cart->load(['items.product:id,name,slug,price,stock,image_url']);

        return response()->json([
            'success' => true,
            'message' => 'Carrito actualizado.',
            'data' => [
                'cart_token' => $cart->session_id,
                'item_count' => $cart->item_count,
                'subtotal' => $cart->subtotal,
            ],
        ]);
    }

    /**
     * Eliminar producto del carrito.
     */
    public function remove(Request $request, Tenant $tenant): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|integer',
        ]);

        $cartToken = $request->header('X-Cart-Token') ?? $request->get('cart_token');
        
        if (!$cartToken) {
            return response()->json([
                'success' => false,
                'message' => 'Carrito no encontrado.',
            ], 404);
        }

        $cart = Cart::where('session_id', $cartToken)->first();

        if ($cart) {
            $cart->items()->where('product_id', $request->product_id)->delete();
            $cart->load(['items.product:id,name,slug,price,stock,image_url']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado del carrito.',
            'data' => [
                'cart_token' => $cart?->session_id,
                'item_count' => $cart?->item_count ?? 0,
                'subtotal' => $cart?->subtotal ?? 0,
            ],
        ]);
    }

    /**
     * Vaciar carrito.
     */
    public function clear(Request $request, Tenant $tenant): JsonResponse
    {
        $cartToken = $request->header('X-Cart-Token') ?? $request->get('cart_token');
        
        if ($cartToken) {
            $cart = Cart::where('session_id', $cartToken)->first();
            if ($cart) {
                $cart->items()->delete();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Carrito vaciado.',
            'data' => [
                'items' => [],
                'item_count' => 0,
                'subtotal' => 0,
            ],
        ]);
    }
}
