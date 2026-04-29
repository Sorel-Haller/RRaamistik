<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Button from '@/components/ui/button/Button.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Products', href: '/products' },
    { title: 'Cart',     href: '/cart' },
];

type CartItem = {
    product: { id: number; name: string; price: number; image?: string };
    quantity: number;
    subtotal: number;
};

const props = defineProps<{ cartItems: CartItem[]; total: number }>();

const update = (productId: number, quantity: number) => {
    if (quantity < 1) {
        remove(productId);
        return;
    }
    router.patch(`/cart/${productId}`, { quantity }, { preserveScroll: true });
};

const remove = (productId: number) => {
    router.delete(`/cart/${productId}`, { preserveScroll: true });
};
</script>

<template>
    <Head title="Cart" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex justify-center p-6">
            <div class="w-full max-w-3xl space-y-6">

                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold">Shopping Cart</h1>
                    <Link href="/products">
                        <Button variant="ghost">← Continue Shopping</Button>
                    </Link>
                </div>

                <div v-if="cartItems.length === 0" class="rounded-xl border p-12 text-center text-muted-foreground">
                    Your cart is empty.
                    <div class="mt-4">
                        <Link href="/products"><Button>Browse Products</Button></Link>
                    </div>
                </div>

                <template v-else>
                    <div class="rounded-xl border overflow-hidden bg-white dark:bg-neutral-900">
                        <div
                            v-for="item in cartItems"
                            :key="item.product.id"
                            class="flex items-center gap-4 p-4 border-b last:border-0"
                        >
                            <img
                                :src="item.product.image ?? 'https://picsum.photos/80/80'"
                                :alt="item.product.name"
                                class="w-16 h-16 object-cover rounded-lg flex-shrink-0"
                            />

                            <div class="flex-1 min-w-0">
                                <p class="font-medium truncate">{{ item.product.name }}</p>
                                <p class="text-sm text-muted-foreground">{{ item.product.price }} € each</p>
                            </div>

                            <div class="flex items-center gap-2">
                                <Button size="icon" variant="outline" class="w-8 h-8" @click="update(item.product.id, item.quantity - 1)">−</Button>
                                <span class="w-8 text-center font-medium">{{ item.quantity }}</span>
                                <Button size="icon" variant="outline" class="w-8 h-8" @click="update(item.product.id, item.quantity + 1)">+</Button>
                            </div>

                            <div class="text-right min-w-[80px]">
                                <p class="font-semibold">{{ item.subtotal.toFixed(2) }} €</p>
                            </div>

                            <Button size="icon" variant="ghost" class="text-destructive" @click="remove(item.product.id)">✕</Button>
                        </div>
                    </div>

                    <div class="rounded-xl border p-6 bg-white dark:bg-neutral-900 space-y-4">
                        <div class="flex justify-between text-lg font-semibold">
                            <span>Total</span>
                            <span>{{ total.toFixed(2) }} €</span>
                        </div>
                        <Link href="/checkout" class="block">
                            <Button class="w-full" size="lg">Proceed to Checkout →</Button>
                        </Link>
                    </div>
                </template>

            </div>
        </div>
    </AppLayout>
</template>