<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index()
    {
        $products = Product::query()
            ->latest()
            ->paginate(12)
            ->through(fn($product) => [
                'id'             => $product->id,
                'name'           => $product->name,
                'description'    => $product->description,
                'price'          => $product->price,
                'sku'            => $product->sku,
                'stock_quantity' => $product->stock_quantity,
                'image'          => $product->image,
                'created_at_formatted' => $product->created_at_formatted,
                'updated_at_formatted' => $product->updated_at_formatted,
            ]);

        return Inertia::render('products/Index', [
            'products' => $products,
        ]);
    }

    /**
     * Delete a product
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->back();
    }
}