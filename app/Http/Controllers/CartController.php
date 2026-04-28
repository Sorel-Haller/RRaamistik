<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = $request->session()->get('cart', []);
        $cartItems = [];

        foreach ($cart as $productId => $quantity) {
            $product = Product::find($productId);
            if ($product) {
                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                ];
            }
        }

        return Inertia::render('Shop/Cart', [
            'cartItems' => $cartItems,
        ]);
    }

    public function add(Request $request)
    {
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity', 1);

        $cart = $request->session()->get('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId] += $quantity;
        } else {
            $cart[$productId] = $quantity;
        }

        $request->session()->put('cart', $cart);

        return back()->with('success', 'Toode lisatud ostukorvi');
    }

    public function update(Request $request)
    {
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');

        $cart = $request->session()->get('cart', []);

        if (isset($cart[$productId])) {
            if ($quantity <= 0) {
                unset($cart[$productId]);
            } else {
                $cart[$productId] = $quantity;
            }
        }

        $request->session()->put('cart', $cart);

        return back();
    }

    public function remove(Request $request)
    {
        $productId = $request->input('product_id');

        $cart = $request->session()->get('cart', []);

        unset($cart[$productId]);

        $request->session()->put('cart', $cart);

        return back();
    }

    public function checkout(Request $request)
    {
        $cart = $request->session()->get('cart', []);
        $cartItems = [];
        $total = 0;

        foreach ($cart as $productId => $quantity) {
            $product = Product::find($productId);
            if ($product) {
                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                ];
                $total += $product->price * $quantity;
            }
        }

        return Inertia::render('Shop/Checkout', [
            'cartItems' => $cartItems,
            'total' => $total,
        ]);
    }

    public function processPayment(Request $request)
    {
        $validated = $request->validate([
            'first_name'       => 'required|string|max:255',
            'last_name'        => 'required|string|max:255',
            'email'            => 'required|email|max:255',
            'phone'            => 'required|string|max:50',
            'payment_method'   => 'required|in:stripe,paypal',
            'stripe_token'     => 'required_if:payment_method,stripe|nullable|string',
        ]);

        $cart = $request->session()->get('cart', []);
        if (empty($cart)) return redirect()->route('cart.index');

        $total = 0;
        $items = [];

        foreach ($cart as $productId => $quantity) {
            $product = Product::find($productId);
            if ($product) {
                $items[] = ['product' => $product, 'quantity' => $quantity];
                $total += $product->price * $quantity;
            }
        }

        $totalCents = (int) round($total * 100);

        if ($validated['payment_method'] === 'stripe') {
            $result = $this->chargeStripe(
                $validated['stripe_token'],
                $totalCents,
                $validated['email']
            );

            if (!$result['success']) {
                return back()->withErrors(['payment' => $result['error']]);
            }

            $paymentId = $result['charge_id'];
            $status = 'paid';

        } else {
            $paymentId = 'PP-' . Str::upper(Str::random(16));
            $status = 'paid';
        }

        $order = Order::create([
            'user_id'        => auth()->id(),
            'first_name'     => $validated['first_name'],
            'last_name'      => $validated['last_name'],
            'email'          => $validated['email'],
            'phone'          => $validated['phone'],
            'total'          => round($total, 2),
            'status'         => $status,
            'payment_method' => $validated['payment_method'],
            'payment_id'     => $paymentId,
        ]);

        foreach ($items as $item) {
            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $item['product']->id,
                'quantity'   => $item['quantity'],
                'price'      => $item['product']->price,
            ]);
        }

        $request->session()->forget('cart');

        return Inertia::render('Shop/Success', [
            'order' => $order->load('items.product'),
        ]);
    }

    private function chargeStripe(string $token, int $amountCents, string $email): array
    {
        $secret = config('services.stripe.secret');

        $ch = curl_init('https://api.stripe.com/v1/charges');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_USERPWD        => $secret . ':',
            CURLOPT_POSTFIELDS     => http_build_query([
                'amount'        => $amountCents,
                'currency'      => 'eur',
                'source'        => $token,
                'description'   => 'Tellimus — ' . $email,
                'receipt_email' => $email,
            ]),
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $data = json_decode($response, true);

        if ($httpCode === 200 && isset($data['id'])) {
            return ['success' => true, 'charge_id' => $data['id']];
        }

        $errorMsg = $data['error']['message'] ?? 'Stripe makse ebaõnnestus.';
        return ['success' => false, 'error' => $errorMsg];
    }
}