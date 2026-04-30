<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuSeparator, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import Pagination from '@/components/ui/pagination/Pagination.vue';
import PaginationContent from '@/components/ui/pagination/PaginationContent.vue';
import PaginationEllipsis from '@/components/ui/pagination/PaginationEllipsis.vue';
import PaginationFirst from '@/components/ui/pagination/PaginationFirst.vue';
import PaginationItem from '@/components/ui/pagination/PaginationItem.vue';
import PaginationLast from '@/components/ui/pagination/PaginationLast.vue';
import PaginationNext from '@/components/ui/pagination/PaginationNext.vue';
import PaginationPrevious from '@/components/ui/pagination/PaginationPrevious.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { index } from '@/routes/products';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { MoreVertical, ShoppingCart } from 'lucide-vue-next';
import { ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Products', href: index().url }];

type Product = {
    id: number;
    name: string;
    description: string;
    price: number;
    sku: string;
    stock_quantity: number;
    image?: string;
};

interface PaginatedResponse {
    current_page: number;
    data: Product[];
    last_page: number;
    total: number;
    per_page: number;
}

defineProps<{ products: PaginatedResponse }>();

const addedId = ref<number | null>(null);

const addToCart = (product: Product) => {
    router.post('/cart/add', { product_id: product.id, quantity: 1 }, {
        preserveScroll: true,
        onSuccess: () => {
            addedId.value = product.id;
            setTimeout(() => (addedId.value = null), 1500);
        },
    });
};

const deleteProduct = (id: number) => {
    if (!confirm('Delete this product?')) return;
    router.delete(`/products/${id}`, { preserveScroll: true });
};
</script>

<template>
    <Head title="Products" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-6">

            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Products</h1>
                <Link href="/cart">
                    <Button variant="outline">
                        <ShoppingCart class="w-4 h-4 mr-2" />
                        View Cart
                    </Button>
                </Link>
            </div>

            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                <div
                    v-for="product in products.data"
                    :key="product.id"
                    class="rounded-xl border bg-white dark:bg-neutral-900 shadow-sm flex flex-col overflow-hidden"
                >
                    <img
                        :src="product.image ?? 'https://picsum.photos/400/300'"
                        :alt="product.name"
                        class="w-full h-44 object-cover"
                    />

                    <div class="flex flex-col gap-2 p-4 flex-1">
                        <h3 class="font-semibold text-base leading-tight">{{ product.name }}</h3>
                        <p class="text-sm text-muted-foreground line-clamp-2 flex-1">{{ product.description }}</p>

                        <div class="flex items-center justify-between mt-1">
                            <span class="font-bold text-lg">{{ product.price }} €</span>
                            <span class="text-xs text-muted-foreground">
                                {{ product.stock_quantity > 0 ? `${product.stock_quantity} in stock` : 'Out of stock' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between mt-2 gap-2">
                            <Button
                                size="sm"
                                class="flex-1"
                                :disabled="product.stock_quantity === 0 || addedId === product.id"
                                @click="addToCart(product)"
                            >
                                {{ addedId === product.id ? '✓ Added' : 'Add to cart' }}
                            </Button>
                        </div>
                    </div>
                </div>
            </div>

            <Pagination
                :page="products.current_page"
                :total="products.total"
                :items-per-page="products.per_page"
                @update:page="(page) => router.get(index().url, { page })"
            >
                <PaginationContent v-slot="{ items }" class="flex items-center gap-1">
                    <PaginationFirst /><PaginationPrevious />
                    <template v-for="(item, i) in items" :key="i">
                        <PaginationItem v-if="item.type === 'page'" :value="item.value" as-child>
                            <Button class="w-9 h-9 p-0" :variant="item.value === products.current_page ? 'default' : 'outline'">
                                {{ item.value }}
                            </Button>
                        </PaginationItem>
                        <PaginationEllipsis v-else :index="i" />
                    </template>
                    <PaginationNext /><PaginationLast />
                </PaginationContent>
            </Pagination>

        </div>
    </AppLayout>
</template>