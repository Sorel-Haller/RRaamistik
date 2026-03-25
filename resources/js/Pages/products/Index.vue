<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue'
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuSeparator, DropdownMenuTrigger} from '@/components/ui/dropdown-menu'
import Pagination from '@/components/ui/pagination/Pagination.vue'
import PaginationContent from '@/components/ui/pagination/PaginationContent.vue'
import PaginationEllipsis from '@/components/ui/pagination/PaginationEllipsis.vue'
import PaginationFirst from '@/components/ui/pagination/PaginationFirst.vue'
import PaginationItem from '@/components/ui/pagination/PaginationItem.vue'
import PaginationLast from '@/components/ui/pagination/PaginationLast.vue'
import PaginationNext from '@/components/ui/pagination/PaginationNext.vue'
import PaginationPrevious from '@/components/ui/pagination/PaginationPrevious.vue'

import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import AppLayout from '@/layouts/AppLayout.vue'
import { index } from '@/routes/products'
import { type BreadcrumbItem } from '@/types'
import { Head, router } from '@inertiajs/vue3'
import { MoreVertical } from 'lucide-vue-next'

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Products',
    href: index().url,
  },
]

interface PaginationLink {
  url: string | null
  label: string
  page?: number | null
  active: boolean
}

interface PaginatedResponse {
  current_page: number
  data: Product[]
  first_page_url: string
  from: number
  last_page: number
  last_page_url: string
  links: PaginationLink[]
  next_page_url: string | null
  path: string
  per_page: number
  prev_page_url: string | null
  to: number
  total: number
}

type Product = {
  id: number
  name: string
  description: string
  price: string
  sku: string
  stock_quantity: number
  created_at_formatted: string
  updated_at_formatted: string
  image_url?: string
}

export type { Product }

defineProps<{
  products: PaginatedResponse
}>()

const addToCart = (product: Product) => {
  let cart = JSON.parse(localStorage.getItem('cart') || '[]')

  const existing = cart.find((item: any) => item.id === product.id)

  if (existing) {
    existing.quantity++
  } else {
    cart.push({
      ...product,
      quantity: 1,
    })
  }

  localStorage.setItem('cart', JSON.stringify(cart))
  alert('Added to cart!')
}

const deleteProduct = (id: number) => {
  if (!confirm('Delete this product?')) return

  router.delete(`/products/${id}`, {
    preserveScroll: true,
  })
}
</script>

<template>
  <Head title="Products" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-col gap-4 overflow-x-auto rounded-xl p-4">

<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
  <div
    v-for="product in products.data"
    :key="product.id"
    class="border rounded-xl p-4 flex flex-col gap-3 shadow-sm"
  >
    <!-- IMAGE -->
    <img
      :src="product.image"
      alt="Product image"
      class="w-full h-40 object-cover rounded-lg"
    />

    <!-- INFO -->
    <div class="flex flex-col gap-1">
      <h3 class="font-semibold text-lg">{{ product.name }}</h3>
      <p class="text-sm text-muted-foreground line-clamp-2">
        {{ product.description }}
      </p>
    </div>

    <!-- PRICE + STOCK -->
    <div class="flex justify-between items-center mt-auto">
      <span class="font-bold">{{ product.price }} €</span>
      <span class="text-sm text-muted-foreground">
        Stock: {{ product.stock_quantity }}
      </span>
    </div>

    <!-- ACTIONS -->
    <div class="flex justify-between items-center mt-2">
      <Button size="sm" @click="addToCart(product)">
        Add to cart
      </Button>

      <DropdownMenu>
        <DropdownMenuTrigger as-child>
          <Button size="icon" variant="ghost">
            <MoreVertical />
          </Button>
        </DropdownMenuTrigger>

        <DropdownMenuContent>
          <DropdownMenuItem
            class="text-destructive"
            @click="deleteProduct(product.id)"
          >
            Delete
          </DropdownMenuItem>
        </DropdownMenuContent>
      </DropdownMenu>
    </div>
  </div>
</div>

      <Pagination
        class="w-full"
        :page="products.current_page"
        :total="products.total"
        :items-per-page="products.per_page"
        @update:page="(page) => router.get(index().url, { page })"
      >
        <PaginationContent v-slot="{ items }" class="flex items-center gap-1">

          <PaginationFirst />
          <PaginationPrevious />

          <template v-for="(item, index) in items" :key="index">
            <PaginationItem
              v-if="item.type === 'page'"
              :value="item.value"
              as-child
            >
              <Button
                class="w-10 h-10 p-0"
                :variant="item.value === products.current_page ? 'default' : 'outline'"
              >
                {{ item.value }}
              </Button>
            </PaginationItem>

            <PaginationEllipsis v-else :index="index" />
          </template>

          <PaginationNext />
          <PaginationLast />

        </PaginationContent>
      </Pagination>

    </div>
  </AppLayout>
</template>