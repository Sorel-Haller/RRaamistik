<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Exception\CardException;

class OrderController extends Controller
{
    private function resolveCartItems(): array
    {
        $items = [];
        $total = 0;

        foreach (session('cart', []) as $productId => $quantity) {
            $product = Product::find($productId);
            if ($product) {
                $subtotal = $product->price * $quantity;
                $total   += $subtotal;
                $items[]  = [
                    'product'  => $product,
                    'quantity' => $quantity,
                    'subtotal' => round($subtotal, 2),
                ];
            }
        }

        return ['items' => $items, 'total' => round($total, 2)];
    }

    public function checkout()
    {
        if (empty(session('cart', []))) {
            return redirect()->route('cart.index');
        }

        ['items' => $items, 'total' => $total] = $this->resolveCartItems();

        return Inertia::render('products/Checkout', [
            'cartItems' => $items,
            'total'     => $total,
        ]);
    }

    public function pay(Request $request)
    {
        $request->validate([
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'email'          => 'required|email',
            'phone'          => 'required|string|max:30',
            'payment_method' => 'required|in:stripe,paypal',
            'stripe_token'   => 'required_if:payment_method,stripe',
        ]);

        if (empty(session('cart', []))) {
            return redirect()->route('cart.index');
        }

        ['items' => $items, 'total' => $total] = $this->resolveCartItems();
        $paymentId = null;

        try {
            if ($request->payment_method === 'stripe') {
                Stripe::setApiKey(config('services.stripe.secret'));
                $charge    = Charge::create([
                    'amount'      => (int) round($total * 100),
                    'currency'    => 'eur',
                    'source'      => $request->stripe_token,
                    'description' => 'Order from ' . $request->email,
                ]);
                $paymentId = $charge->id;
            } else {
                $paymentId = 'PAYPAL_SIM_' . strtoupper(uniqid());
            }
        } catch (CardException $e) {
            return back()->withErrors(['payment' => $e->getMessage()]);
        } catch (\Exception $e) {
            return back()->withErrors(['payment' => 'Payment failed. Please try again.']);
        }

        $order = Order::create([
            'user_id'        => auth()->id(),
            'first_name'     => $request->first_name,
            'last_name'      => $request->last_name,
            'email'          => $request->email,
            'phone'          => $request->phone,
            'total'          => $total,
            'payment_method' => $request->payment_method,
            'payment_id'     => $paymentId,
            'status'         => 'paid',
        ]);

        foreach ($items as $item) {
            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $item['product']->id,
                'quantity'   => $item['quantity'],
                'price'      => $item['product']->price,
            ]);
        }

        session()->forget('cart');

        return Inertia::render('products/Success', [
            'order' => $order->load('items.product'),
        ]);
    }
}