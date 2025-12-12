<?php

declare(strict_types=1);

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Tenant\Cart;
use App\Models\Tenant\Order;
use App\Models\Tenant\PaymentSetting;
use App\Services\Payment\PaymentGatewayFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function index(Tenant $tenant): View|RedirectResponse
    {
        $cart = $this->getCart();
        $cart->load('items.product');

        if ($cart->items->isEmpty()) {
            return redirect()->route('storefront.cart.index', $tenant)
                ->with('error', 'Tu carrito está vacío.');
        }

        $paymentMethods = PaymentSetting::active()->ordered()->get();

        return view('storefront.checkout.index', compact('tenant', 'cart', 'paymentMethods'));
    }

    public function process(Request $request, Tenant $tenant): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'payment_method' => ['required', 'string', 'exists:payment_settings,gateway'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $cart = $this->getCart();
        $cart->load('items.product');

        if ($cart->items->isEmpty()) {
            return redirect()->route('storefront.cart.index', $tenant)
                ->with('error', 'Tu carrito está vacío.');
        }

        // Verificar stock
        foreach ($cart->items as $item) {
            if (!$item->product || !$item->product->isInStock() || $item->product->stock < $item->quantity) {
                return back()->with('error', "El producto '{$item->product?->name}' no tiene stock suficiente.");
            }
        }

        DB::beginTransaction();

        try {
            // Crear orden
            $order = Order::createFromCart($cart, $validated);
            $order->update([
                'payment_method' => $validated['payment_method'],
            ]);

            // Decrementar stock
            foreach ($cart->items as $item) {
                $item->product->decrementStock($item->quantity);
            }

            // Limpiar carrito
            $cart->clear();

            DB::commit();

            // Procesar pago según método
            $paymentSetting = PaymentSetting::where('gateway', $validated['payment_method'])->first();

            if (in_array($validated['payment_method'], ['mercadopago', 'uala'])) {
                // Redirigir a gateway de pago
                try {
                    $gateway = PaymentGatewayFactory::make($paymentSetting);
                    $paymentData = $gateway->createPayment($order);

                    if ($paymentData['success'] && $url = $gateway->getPaymentUrl($paymentData)) {
                        // Guardar referencia del pago
                        $order->update([
                            'metadata' => array_merge($order->metadata ?? [], [
                                'payment_init' => $paymentData,
                            ]),
                        ]);

                        return redirect()->away($url);
                    }
                } catch (\Exception $e) {
                    Log::error("Error creando pago: {$e->getMessage()}", [
                        'order' => $order->order_number,
                        'gateway' => $validated['payment_method'],
                    ]);
                }
            }

            // Para transferencia/efectivo o si falla el gateway, mostrar confirmación
            return redirect()->route('storefront.checkout.confirmation', [$tenant, $order->order_number]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error en checkout: {$e->getMessage()}");

            return back()->with('error', 'Hubo un error procesando tu pedido. Por favor intenta nuevamente.');
        }
    }

    public function confirmation(Tenant $tenant, string $orderNumber): View
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();
        $order->load('items');

        $paymentSetting = $order->payment_method 
            ? PaymentSetting::where('gateway', $order->payment_method)->first() 
            : null;

        // Obtener instrucciones de pago si aplica
        $paymentInstructions = null;
        if ($paymentSetting && in_array($order->payment_method, ['transfer', 'cash'])) {
            try {
                $gateway = PaymentGatewayFactory::make($paymentSetting);
                $paymentData = $gateway->createPayment($order);
                $paymentInstructions = $paymentData['instructions'] ?? null;
            } catch (\Exception $e) {
                // Ignorar errores
            }
        }

        return view('storefront.checkout.confirmation', compact('tenant', 'order', 'paymentSetting', 'paymentInstructions'));
    }

    public function success(Tenant $tenant, string $orderNumber): View
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        return view('storefront.checkout.success', compact('tenant', 'order'));
    }

    public function failure(Tenant $tenant, string $orderNumber): View
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        return view('storefront.checkout.failure', compact('tenant', 'order'));
    }

    public function pending(Tenant $tenant, string $orderNumber): View
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        return view('storefront.checkout.pending', compact('tenant', 'order'));
    }

    protected function getCart(): Cart
    {
        $sessionId = session()->getId();
        return Cart::getOrCreate($sessionId);
    }
}
