<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'Wireless Headphones',  'description' => 'Premium noise-cancelling over-ear headphones with 30h battery life.', 'price' => 89.99,  'sku' => 'WH-001', 'stock_quantity' => 25, 'image' => 'https://picsum.photos/seed/headphones/400/300'],
            ['name' => 'Mechanical Keyboard',   'description' => 'Compact TKL mechanical keyboard with Cherry MX switches.', 'price' => 129.99, 'sku' => 'KB-002', 'stock_quantity' => 15, 'image' => 'https://picsum.photos/seed/keyboard/400/300'],
            ['name' => 'USB-C Hub',              'description' => '7-in-1 USB-C hub with HDMI, SD card reader and 100W PD.', 'price' => 49.99,  'sku' => 'HB-003', 'stock_quantity' => 40, 'image' => 'https://picsum.photos/seed/usbhub/400/300'],
            ['name' => 'Webcam 4K',              'description' => '4K autofocus webcam with built-in ring light and noise-cancelling mic.', 'price' => 79.99,  'sku' => 'WC-004', 'stock_quantity' => 20, 'image' => 'https://picsum.photos/seed/webcam/400/300'],
            ['name' => 'Desk Lamp',              'description' => 'LED desk lamp with wireless charging pad and 5 brightness levels.', 'price' => 39.99,  'sku' => 'DL-005', 'stock_quantity' => 30, 'image' => 'https://picsum.photos/seed/lamp/400/300'],
            ['name' => 'Mouse Pad XL',           'description' => 'Extended gaming mouse pad with stitched edges, 90×40 cm.', 'price' => 24.99,  'sku' => 'MP-006', 'stock_quantity' => 50, 'image' => 'https://picsum.photos/seed/mousepad/400/300'],
            ['name' => 'Portable SSD 1TB',       'description' => 'USB 3.2 portable SSD with 1050 MB/s read speed.', 'price' => 109.99, 'sku' => 'SSD-007', 'stock_quantity' => 18, 'image' => 'https://picsum.photos/seed/ssd/400/300'],
            ['name' => 'Ergonomic Mouse',        'description' => 'Vertical ergonomic mouse with 6 buttons and silent clicks.', 'price' => 44.99,  'sku' => 'EM-008', 'stock_quantity' => 22, 'image' => 'https://picsum.photos/seed/mouse/400/300'],
            ['name' => 'Monitor Stand',          'description' => 'Adjustable aluminum monitor stand with storage drawer.', 'price' => 59.99,  'sku' => 'MS-009', 'stock_quantity' => 12, 'image' => 'https://picsum.photos/seed/stand/400/300'],
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(['sku' => $product['sku']], $product);
        }
    }
}