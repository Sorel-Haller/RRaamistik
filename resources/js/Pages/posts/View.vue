<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Button from '@/components/ui/button/Button.vue';
import { Textarea } from '@/components/ui/textarea';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import { edit, index, show } from '@/routes/posts';
import { add } from '@/routes/comments';
import type { BreadcrumbItem } from '@/types';

type Comment = {
  id: number;
  post_id: number;
  user_id: number;
  content: string;
  user: { id: number; name: string; email: string };
  created_at_formatted: string;
};

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
    comments?: Comment[];
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

const form = useForm({ content: '' });

const submit = () => {
  form.post(add(props.post.id).url, {
    preserveScroll: true,
    onSuccess: () => form.reset(),
  });
};
</script>

<template>
  <Head :title="props.post.title" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full justify-center overflow-x-auto p-6">
      <div class="w-full max-w-3xl space-y-8">

        <!-- HEADER -->
        <header class="space-y-4 rounded-xl border border-border/60 bg-card p-8 shadow-sm">
          <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="space-y-3">
              <span class="inline-flex items-center rounded-full bg-muted px-3 py-1 text-xs font-medium uppercase tracking-wide text-muted-foreground">
                Blog Post
              </span>

              <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">
                {{ post.title }}
              </h1>

              <p class="flex flex-wrap items-center gap-2 text-sm text-muted-foreground">
                <span>Written by</span>
                <span class="font-medium text-foreground">
                  {{ post.author.first_name }} {{ post.author.last_name }}
                </span>
                <span class="text-xs text-border">•</span>
                <span>
                  Published
                <span class="font-medium text-foreground">{{ post.created_at_formatted }}</span>
                </span>  
                <span class="text-xs text-border">•</span>            
                <span
                class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-wide"
                :class="statusClasses"
              >
                {{ statusLabel }}
              </span>
              </p>              

            </div>

            <div class="flex flex-col items-end gap-3">


              <div class="flex flex-wrap items-center gap-2">
                <Button as-child size="sm" variant="outline">
                  <Link :href="edit.url(post.id)">Edit Post</Link>
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
                <div class="text-sm">{{ post.created_at_formatted }}</div>
                <div class="text-xs text-muted-foreground">{{ post.created_at }}</div>
              </dd>
            </div>
            <div class="space-y-1">
              <dt class="uppercase tracking-wide">Last updated</dt>
              <dd class="space-y-0.5 text-foreground">
                <div class="text-sm">{{ post.updated_at_formatted }}</div>
                <div class="text-xs text-muted-foreground">{{ post.updated_at }}</div>
              </dd>
            </div>
            <div class="space-y-1">
              <dt class="uppercase tracking-wide">Post ID</dt>
              <dd class="text-sm text-foreground">#{{ post.id }}</dd>
            </div>
          </dl>
        </header>

        <!-- CONTENT -->
        <section class="rounded-xl border border-border/60 bg-background p-8 shadow-sm">
          <article class="prose prose-slate max-w-none text-base leading-relaxed text-foreground/90 dark:prose-invert">
            <p class="whitespace-pre-line">{{ post.content }}</p>
          </article>
        </section>

        <!-- COMMENTS -->
        <section class="rounded-xl border border-border/60 bg-card p-8 shadow-sm space-y-6">
          <h2 class="text-xl font-semibold">Comments ({{ post.comments?.length ?? 0 }})</h2>

          <!-- ADD COMMENT FORM -->
          <form @submit.prevent="submit" class="space-y-3">
            <Textarea
              v-model="form.content"
              placeholder="Write a comment…"
              class="resize-none"
              rows="3"
            />
            <p v-if="form.errors.content" class="text-sm text-destructive">
              {{ form.errors.content }}
            </p>
            <div class="flex justify-end">
              <Button
                type="submit"
                :disabled="form.processing || !form.content.trim()"
              >
                {{ form.processing ? 'Posting…' : 'Post comment' }}
              </Button>
            </div>
          </form>

          <!-- COMMENT LIST -->
          <div v-if="post.comments && post.comments.length" class="flex flex-col gap-3">
            <div
              v-for="comment in post.comments"
              :key="comment.id"
              class="rounded-lg border border-border/60 bg-background p-4"
            >
              <div class="flex items-center justify-between mb-1">
                <span class="font-medium text-sm">{{ comment.user.name }}</span>
                <span class="text-xs text-muted-foreground">{{ comment.created_at_formatted }}</span>
              </div>
              <p class="text-sm text-foreground/80">{{ comment.content }}</p>
            </div>
          </div>

          <p v-else class="text-center text-sm text-muted-foreground py-2">
            No comments yet. Be the first to comment!
          </p>
        </section>

      </div>
    </div>
  </AppLayout>
</template>