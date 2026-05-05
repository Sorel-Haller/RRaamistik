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
import { destroy, edit, show, create, index } from '@/routes/posts';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { MoreVertical } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Posts',
    href: index().url,
  },
];

interface PaginationLink {
  url: string | null;
  label: string;
  page?: number | null;
  active: boolean;
}

interface PaginatedResponse {
  current_page: number;
  data: Post[];
  first_page_url: string;
  from: number;
  last_page: number;
  last_page_url: string;
  links: PaginationLink[];
  next_page_url: string | null;
  path: string;
  per_page: number;
  prev_page_url: string | null;
  to: number;
  total: number;
}

type Comment = {
  id: number;
  post_id: number;
  user_id: number;
  content: string;
  user: User;
  created_at_formatted: string;
  updated_at_formatted: string;
};

type User = {
  id: number;
  name: string;
  email: string;
};

export type Post = {
  id: number;
  title: string;
  content: string;
  author_id: number;
  published: boolean;
  created_at: string;
  updated_at: string;
  created_at_formatted: string;
  updated_at_formatted: string;
  author: {
    id: number;
    first_name: string;
    last_name: string;
  };
  comments?: Comment[];
};

defineProps<{
  posts: PaginatedResponse;
  fallbackPost: Post | null;
}>();
const deletePost = (postId: number) => {
  if (!confirm('Aga miks sa kustutad?')) return;
  router.delete(destroy.url(postId), {
    preserveScroll: true,
    onSuccess: () => {
      console.log('Postitus sai kustutatud.');
    },
    onError: (err) => {
      console.error(err);
      alert('Ups, sa ei saanud eluga hakkama.');
    },
  });
}
</script>

<template>
  <Head title="Posts" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-col gap-6 p-6">

      <!-- HEADER -->
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">Blog Posts</h1>

        <!-- NEW POST BUTTON -->
          <Link :href="create.url()">
            <Button class="bg-green-600 hover:bg-green-700 text-white">
              Add New Post
            </Button>
          </Link>
      </div>

      <!-- GRID -->
      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <Link
          v-if="!posts?.data?.length && fallbackPost"
          :href="show.url(fallbackPost.id)"
          class="block rounded-xl border p-4 bg-white dark:bg-neutral-900 shadow-sm hover:shadow-md transition"
        >
          <h2 class="font-semibold text-lg leading-tight">
            {{ fallbackPost.title }}
          </h2>

          <p class="text-sm opacity-60 mt-1 line-clamp-3">
            {{ fallbackPost.content }}
          </p>

          <div class="flex items-center justify-between mt-4">
            <div class="text-xs opacity-70">
              {{ fallbackPost.author.first_name }}
              {{ fallbackPost.author.last_name }}
            </div>
          </div>
        </Link>
        <div
          v-for="post in posts.data"
          :key="post.id"
          class="rounded-xl border bg-white dark:bg-neutral-900 shadow-sm
                hover:shadow-md transition flex flex-col justify-between"
        >
          <!-- Clickable area -->
          <Link :href="show.url(post.id)" class="flex flex-col gap-2 p-4">
            <h2 class="font-semibold text-lg leading-tight">{{ post.title }}</h2>

            <p class="text-sm opacity-60 line-clamp-3">{{ post.content }}</p>

            <div class="flex items-center justify-between mt-2">
              <div class="text-xs opacity-70">
                {{ post.author.first_name }} {{ post.author.last_name }}
              </div>
              <span
                class="px-2 py-1 rounded-full text-xs"
                :class="post.published ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600'"
              >
                {{ post.published ? 'Published' : 'Draft' }}
              </span>
            </div>
          </Link>

          <!-- Dropdown stays outside the Link so it doesn't trigger navigation -->
          <div class="flex justify-end px-4 pb-3">
            <DropdownMenu>
              <DropdownMenuTrigger as-child>
                <Button size="icon" variant="ghost"><MoreVertical /></Button>
              </DropdownMenuTrigger>
              <DropdownMenuContent>
                <DropdownMenuItem as-child>
                  <Link :href="show.url(post.id)">View</Link>
                </DropdownMenuItem>
                <DropdownMenuItem as-child>
                  <Link :href="edit.url(post.id)">Edit</Link>
                </DropdownMenuItem>
                <DropdownMenuSeparator />
                <DropdownMenuItem class="text-destructive" @click="deletePost(post.id)">
                  Delete
                </DropdownMenuItem>
              </DropdownMenuContent>
            </DropdownMenu>
          </div>
        </div>

      </div>

      <!-- PAGINATION -->
      <Pagination
        class="w-full"
        :page="posts.current_page"
        v-slot="{ page }"
        :total="posts.total"
        :items-per-page="posts.per_page"
        @update:page="(page) => router.get(index().url, { page: page })"
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
                class="w-9 h-9 p-0"
                :variant="item.value === page ? 'default' : 'outline'"
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
