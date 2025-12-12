<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\Tenant\Cart;
use App\Models\Tenant\Order;
use App\Models\Tenant\PaymentSetting;
use App\Services\Payment\PaymentGatewayFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderApiController extends Controller
{
    /**
     * Crear un pedido desde el carrito.
     */
    public function store(Request $request, Tenant $tenant): JsonResponse
    {
        $request->validate([
            'cart_token' => 'required|string',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'shipping_address' => 'nullable|string|max:500',
            'shipping_city' => 'nullable|string|max:100',
            'shipping_state' => 'nullable|string|max:100',
            'shipping_postal_code' => 'nullable|string|max:20',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string|max:1000',
        ]);

        $cart = Cart::where('session_id', $request->cart_token)
            ->with(['items.product'])
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'El carrito está vacío o no existe.',
            ], 400);
        }

        // Verificar método de pago
        $paymentSetting = PaymentSetting::where('gateway', $request->payment_method)
            ->where('is_active', true)
            ->first();

        if (!$paymentSetting) {
            return response()->json([
                'success' => false,
                'message' => 'Método de pago no disponible.',
            ], 400);
        }

        // Verificar stock de todos los productos
        foreach ($cart->items as $item) {
            if (!$item->product || $item->product->stock < $item->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => "Stock insuficiente para: {$item->product?->name ?? 'Producto no disponible'}",
                ], 400);
            }
        }

        try {
            DB::beginTransaction();

            // Crear orden
            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'shipping_address' => $request->shipping_address,
                'shipping_city' => $request->shipping_city,
                'shipping_state' => $request->shipping_state,
                'shipping_postal_code' => $request->shipping_postal_code,
                'subtotal' => $cart->subtotal,
                'tax' => 0,
                'shipping_cost' => 0,
                'discount' => 0,
                'total' => $cart->subtotal,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'status' => 'pending',
                'notes' => $request->notes,
            ]);

            // Crear items del pedido y reducir stock
            foreach ($cart->items as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'product_sku' => $item->product->sku,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'subtotal' => $item->subtotal,
                ]);

                // Reducir stock
                $item->product->decrement('stock', $item->quantity);
            }

            // Vaciar carrito
            $cart->items()->delete();
            $cart->delete();

            DB::commit();

            // Procesar pago si aplica
            $paymentResponse = null;
            $paymentInstructions = null;

            if (in_array($request->payment_method, ['mercadopago', 'uala'])) {
                try {
                    $gateway = PaymentGatewayFactory::make($request->payment_method, $paymentSetting->credentials);
                    $paymentResponse = $gateway->createPayment($order, route('api.orders.webhook', [$tenant, $order]));

                    if (isset($paymentResponse['init_point'])) {
                        $order->update([
                            'payment_data' => ['checkout_url' => $paymentResponse['init_point']],
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::error("Error al crear pago: " . $e->getMessage());
                }
            } else {
                // Para transferencia y efectivo
                $paymentInstructions = $paymentSetting->instructions;
            }

            return response()->json([
                'success' => true,
                'message' => 'Pedido creado exitosamente.',
                'data' => [
                    'order_number' => $order->order_number,
                    'order_id' => $order->id,
                    'total' => $order->total,
                    'payment_method' => $order->payment_method,
                    'status' => $order->status,
                    'payment_url' => $paymentResponse['init_point'] ?? null,
                    'payment_instructions' => $paymentInstructions,
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Error al crear pedido: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el pedido. Intente nuevamente.',
            ], 500);
        }
    }

    /**
     * Obtener detalles de un pedido.
     */
    public function show(Request $request, Tenant $tenant, string $orderNumber): JsonResponse
    {
        $email = $request->get('email');

        $query = Order::where('order_number', $orderNumber)
            ->with(['items']);

        // Si se proporciona email, verificar que coincida
        if ($email) {
            $query->where('customer_email', $email);
        }

        $order = $query->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pedido no encontrado.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'order_number' => $order->order_number,
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'customer' => [
                    'name' => $order->customer_name,
                    'email' => $order->customer_email,
                    'phone' => $order->customer_phone,
                ],
                'shipping' => [
                    'address' => $order->shipping_address,
                    'city' => $order->shipping_city,
                    'state' => $order->shipping_state,
                    'postal_code' => $order->shipping_postal_code,
                ],
                'items' => $order->items->map(fn($item) => [
                    'product_name' => $item->product_name,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'subtotal' => $item->subtotal,
                ]),
                'subtotal' => $order->subtotal,
                'tax' => $order->tax,
                'shipping_cost' => $order->shipping_cost,
                'discount' => $order->discount,
                'total' => $order->total,
                'payment_method' => $order->payment_method,
                'notes' => $order->notes,
                'created_at' => $order->created_at->toIso8601String(),
            ],
        ]);
    }

    /**
     * Webhook para notificaciones de pago.
     */
    public function webhook(Request $request, Tenant $tenant, Order $order): JsonResponse
    {
        $paymentSetting = PaymentSetting::where('gateway', $order->payment_method)
            ->where('is_active', true)
            ->first();

        if (!$paymentSetting) {
            return response()->json(['success' => false], 400);
        }

        try {
            $gateway = PaymentGatewayFactory::make($order->payment_method, $paymentSetting->credentials);
            $result = $gateway->handleWebhook($request->all());

            if ($result['success']) {
                $order->update([
                    'payment_status' => $result['status'],
                    'status' => $result['status'] === 'paid' ? 'processing' : $order->status,
                    'payment_data' => array_merge($order->payment_data ?? [], [
                        'webhook_data' => $result,
                        'updated_at' => now()->toIso8601String(),
                    ]),
                ]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error("Webhook error: " . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }

    /**
     * Listar métodos de pago disponibles.
     */
    public function paymentMethods(Tenant $tenant): JsonResponse
    {
        $methods = PaymentSetting::where('is_active', true)
            ->get(['gateway', 'display_name', 'description'])
            ->map(fn($m) => [
                'id' => $m->gateway,
                'name' => $m->display_name,
                'description' => $m->description,
            ]);

        return response()->json([
            'success' => true,
            'data' => $methods,
        ]);
    }
}
