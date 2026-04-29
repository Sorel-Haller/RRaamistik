<?php

namespace App\Http\Controllers;

use App\Models\Product;
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

    private function buildCartItems(): array
    {
        $items = [];
        $total = 0;

        foreach ($this->getCart() as $productId => $quantity) {
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

    public function index()
    {
        ['items' => $items, 'total' => $total] = $this->buildCartItems();

        return Inertia::render('products/Cart', [
            'cartItems' => $items,
            'total'     => $total,
        ]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'integer|min:1',
        ]);

        $cart = $this->getCart();
        $id   = $request->product_id;
        $cart[$id] = ($cart[$id] ?? 0) + ($request->quantity ?? 1);
        $this->saveCart($cart);

        return redirect()->back();
    }

    public function update(Request $request, $productId)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        $cart = $this->getCart();
        $cart[$productId] = $request->quantity;
        $this->saveCart($cart);

        return redirect()->back();
    }

    public function remove($productId)
    {
        $cart = $this->getCart();
        unset($cart[$productId]);
        $this->saveCart($cart);

        return redirect()->back();
    }
    public function pay(Request $request)
    {
        return back()->with('success', 'Payment successful (test)');
    }
}