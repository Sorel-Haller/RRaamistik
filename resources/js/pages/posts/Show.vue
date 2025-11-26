<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { index, show } from '@/routes/posts';
import { type BreadcrumbItem } from '@/types';
import { Post } from './Index.vue';

const props = defineProps<{ post: Post }>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Posts',
    href: index().url,
  },
  {
    title: props.post.title,
    href: show(props.post.id).url,
  },
];
</script>

<template>
  <div class="flex flex-col gap-6 p-6 h-full">
    <!-- Post Header -->
    <header class="rounded-xl bg-muted/40 p-6 shadow-sm">
      <h1 class="text-3xl font-semibold tracking-tight">{{ props.post.title }}</h1>
      <p class="text-sm text-muted-foreground mt-2">{{ props.post.author }}</p>
    </header>

    <!-- Post Content -->
    <section class="rounded-xl border border-border/60 bg-background p-6 shadow-sm">
      <h2 class="mb-4 text-lg font-semibold text-foreground">Content</h2>
      <div class="prose max-w-none text-sm leading-relaxed text-foreground/90 dark:prose-invert">
        <p class="whitespace-pre-line">{{ props.post.content }}</p>
      </div>
    </section>
  </div>
</template>
