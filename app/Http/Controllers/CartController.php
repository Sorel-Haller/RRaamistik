<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
    private function getCart(): array
    {
        return session('cart', []);
    }

    private function saveCart(array $cart): void
    {
        session(['cart' => $cart]);
    }

    /**
     * Build cart items + total from session
     */
    private function buildCartItems(): array
    {
        $cart = $this->getCart();

        $items = [];
        $total = 0;

        foreach ($cart as $productId => $quantity) {
            $product = Product::find($productId);

            if (!$product) continue;

            $subtotal = $product->price * $quantity;

            $items[] = [
                'product' => $product,
                'quantity' => $quantity,
                'subtotal' => $subtotal,
            ];

            $total += $subtotal;
        }

        return [
            'items' => $items,
            'total' => round($total, 2),
        ];
    }

    /**
     * CART PAGE
     */
    public function index()
    {
        ['items' => $items, 'total' => $total] = $this->buildCartItems();

        return Inertia::render('products/Cart', [
            'cartItems' => $items,
            'total' => $total,
        ]);
    }

    /**
     * ADD TO CART
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'integer|min:1',
        ]);

        $cart = $this->getCart();
        $id = $request->product_id;

        $cart[$id] = ($cart[$id] ?? 0) + ($request->quantity ?? 1);

        $this->saveCart($cart);

        return redirect()->back();
    }

    /**
     * UPDATE CART ITEM
     */
    public function update(Request $request, $productId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = $this->getCart();
        $cart[$productId] = $request->quantity;

        $this->saveCart($cart);

        return redirect()->back();
    }

    /**
     * REMOVE ITEM
     */
    public function remove($productId)
    {
        $cart = $this->getCart();
        unset($cart[$productId]);

        $this->saveCart($cart);

        return redirect()->back();
    }

    /**
     * PAYMENT
     */
    public function pay(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'payment_method' => 'required',
        ]);

        $cart = $this->getCart();

        if (empty($cart)) {
            return back()->withErrors([
                'payment' => 'Cart is empty',
            ]);
        }

        $total = 0;

        foreach ($cart as $productId => $quantity) {
            $product = Product::find($productId);
            if (!$product) continue;

            $total += $product->price * $quantity;
        }

        $paymentId = 'PAY_' . rand(100000, 999999);

        $order = Order::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'payment_method' => $request->payment_method,
            'payment_id' => $paymentId,
            'total' => $total,
        ]);

        // Create order items
        foreach ($cart as $productId => $quantity) {
            $product = Product::find($productId);

            if (!$product) continue;

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $product->price,
            ]);
        }

        // Clear cart
        session()->forget('cart');

        return redirect()->route('checkout.success', $order->id);
    }

    /**
     * SUCCESS PAGE
     */
    public function success(Order $order)
    {
        $order->load('items.product');

        return Inertia::render('products/Success', [
            'order' => $order,
        ]);
    }
}