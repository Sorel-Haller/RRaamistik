    <script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Button from '@/components/ui/button/Button.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { edit, index, show } from '@/routes/posts';
import type { BreadcrumbItem } from '@/types';

const props = defineProps<{
  post: {
    id: number;
    title: string;
    content: string;
    author: string;
    published: boolean;
    comments_count: number;
    created_at: string;
    updated_at: string;
    created_at_formatted: string;
    updated_at_formatted: string;
  };
}>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Posts', href: index().url },
  { title: props.post.title, href: show.url(props.post.id) },
];

const statusLabel = computed(() => (props.post.published ? 'Published' : 'Draft'));
const statusClasses = computed(() =>
  props.post.published
    ? 'bg-emerald-100 text-emerald-700 border border-emerald-200'
    : 'bg-slate-100 text-slate-600 border border-slate-200',
);
</script>

<template>
  <Head :title="props.post.title" />

<AppLayout :breadcrumbs="breadcrumbs">
  <div class="flex flex-col gap-6 p-6 h-full">
    <!-- Post Header -->
    <header class="rounded-xl bg-muted/40 p-6 shadow-sm">
      <h1 class="text-3xl font-semibold tracking-tight">{{ props.post.title }}</h1>
    </header>

    <!-- Post Content -->
    <section class="rounded-xl border border-border/60 bg-background p-6 shadow-sm">
      <h2 class="mb-4 text-lg font-semibold text-foreground">Content</h2>
      <div class="prose max-w-none text-sm leading-relaxed text-foreground/90 dark:prose-invert">
        <p class="whitespace-pre-line">{{ props.post.content }}</p>
      </div>
    </section>
    <section>
      <Link :href="edit.url(props.post.id)">
        <Button>Edit Post</Button>
      </Link>
    </section>
  </div>
</AppLayout>
</template>

