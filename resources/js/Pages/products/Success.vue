<script setup>
import { Head, Link } from '@inertiajs/vue3';
const props = defineProps({ order: Object });
</script>

<template>
    <Head title="Order Confirmed" />
    <div class="min-h-screen bg-gray-50 flex items-center justify-center px-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 max-w-lg w-full p-10 text-center">
            <div class="text-6xl mb-4">✅</div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Payment Successful!</h1>
            <p class="text-gray-500 mb-6">
                Thank you for your order, <strong>{{ order.first_name }}</strong>!
                A confirmation will be sent to <strong>{{ order.email }}</strong>.
            </p>

            <div class="bg-gray-50 rounded-xl p-4 text-left mb-6 space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Order #</span>
                    <span class="font-mono font-medium">#{{ order.id }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Payment ID</span>
                    <span class="font-mono text-xs">{{ order.payment_id }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Method</span>
                    <span class="capitalize">{{ order.payment_method }}</span>
                </div>
                <div class="flex justify-between border-t border-gray-200 pt-2 mt-2">
                    <span class="font-semibold">Total Paid</span>
                    <span class="font-bold text-primary">{{ Number(order.total).toFixed(2) }} €</span>
                </div>
            </div>

            <div class="text-left mb-6">
                <p class="text-sm font-medium text-gray-700 mb-2">Items ordered:</p>
                <div v-for="item in order.items" :key="item.id" class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                    <div class="flex items-center gap-3">
                        <img :src="item.product.image" :alt="item.product.name" class="w-10 h-10 object-cover rounded" />
                        <div>
                            <p class="text-sm font-medium">{{ item.product.name }}</p>
                            <p class="text-xs text-gray-400">× {{ item.quantity }}</p>
                        </div>
                    </div>
                    <p class="text-sm font-semibold">{{ (item.price * item.quantity).toFixed(2) }} €</p>
                </div>
            </div>

            <Link href="/products" class="block w-full bg-primary hover:opacity-90 text-white font-semibold py-3 rounded-lg transition">
                Back to Shop →
            </Link>
        </div>
    </div>
</template>