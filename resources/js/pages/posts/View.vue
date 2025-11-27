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
    author_id: number;
    author: {
      id: number;
      first_name: string;
      last_name: string;
    };
    published: boolean;
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
    <div class="flex h-full justify-center overflow-x-auto p-6">
      <div class="w-full max-w-3xl space-y-8">
        <!-- Blog header -->
        <header class="space-y-4 rounded-xl border border-border/60 bg-card p-8 shadow-sm">
          <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="space-y-3">
              <span class="inline-flex items-center rounded-full bg-muted px-3 py-1 text-xs font-medium uppercase tracking-wide text-muted-foreground">
                Blog Post
              </span>

              <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">
                {{ props.post.title }}
              </h1>

              <p class="flex flex-wrap items-center gap-2 text-sm text-muted-foreground">
                <span>Written by</span>
                <span class="font-medium text-foreground">
                  {{ props.post.author.first_name }} {{ props.post.author.last_name }}
                </span>
                <span class="text-xs text-border">•</span>
                <span>
                  Published
                  <span class="font-medium text-foreground">{{ props.post.created_at_formatted }}</span>
                </span>
              </p>
            </div>

            <div class="flex flex-col items-end gap-3">
              <span
                class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-wide"
                :class="statusClasses"
              >
                {{ statusLabel }}
              </span>

              <div class="flex flex-wrap items-center gap-2">
                <Button as-child size="sm" variant="outline">
                  <Link :href="edit.url(props.post.id)">Edit Post</Link>
                </Button>

                <Button as-child size="sm" variant="ghost">
                  <Link :href="index().url">Back to Posts</Link>
                </Button>
              </div>
            </div>
          </div>

          <dl class="mt-4 grid gap-4 border-t border-border/40 pt-4 text-xs text-muted-foreground sm:grid-cols-3">
            <div class="space-y-1">
              <dt class="uppercase tracking-wide">Created</dt>
              <dd class="space-y-0.5 text-foreground">
                <div class="text-sm">{{ props.post.created_at_formatted }}</div>
                <div class="text-xs text-muted-foreground">{{ props.post.created_at }}</div>
              </dd>
            </div>

            <div class="space-y-1">
              <dt class="uppercase tracking-wide">Last updated</dt>
              <dd class="space-y-0.5 text-foreground">
                <div class="text-sm">{{ props.post.updated_at_formatted }}</div>
                <div class="text-xs text-muted-foreground">{{ props.post.updated_at }}</div>
              </dd>
            </div>

            <div class="space-y-1">
              <dt class="uppercase tracking-wide">Post ID</dt>
              <dd class="text-sm text-foreground">#{{ props.post.id }}</dd>
            </div>
          </dl>
        </header>

        <!-- Blog content -->
        <section class="rounded-xl border border-border/60 bg-background p-8 shadow-sm">
          <article class="prose prose-slate max-w-none text-base leading-relaxed text-foreground/90 dark:prose-invert">
            <p class="whitespace-pre-line">
              {{ props.post.content }}
            </p>
          </article>
        </section>
      </div>
    </div>
  </AppLayout>
</template>