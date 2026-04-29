<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Switch } from '@/components/ui/switch';
import { index, store } from '@/routes/posts';
import type { BreadcrumbItem } from '@/types';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import InputError from '@/components/InputError.vue';

const props = defineProps<{
    authors: Record<number, string>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Posts', href: index().url },
    { title: 'Create Post', href: '/posts/create' },
];

const form = useForm({
    title: '',
    content: '',
    author_id: '',
    published: false,
});

const submit = () => {
    form.post(store().url);
};
</script>

<template>
    <Head title="Create Post" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="w-2/3 min-w-[300px] mx-auto p-6 flex flex-col gap-6">
            <h1 class="text-2xl font-semibold">Create New Post</h1>

            <form @submit.prevent="submit" class="flex flex-col gap-4">

                <!-- TITLE -->
                <div>
                    <Label for="title">Title</Label>
                    <Input id="title" v-model="form.title" />
                    <InputError :message="form.errors.title" />
                </div>

                <!-- AUTHOR -->
                <div>
                    <Label>Author</Label>
                    <Select v-model="form.author_id">
                        <SelectTrigger>
                            <SelectValue placeholder="Select author" />
                        </SelectTrigger>

                        <SelectContent>
                            <SelectGroup>
                                <SelectItem
                                    v-for="(name, id) in authors"
                                    :key="id"
                                    :value="String(id)"
                                >
                                    {{ name }}
                                </SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.author_id" />
                </div>

                <!-- CONTENT -->
                <div>
                    <Label for="content">Content</Label>
                    <Textarea id="content" rows="6" v-model="form.content" />
                    <InputError :message="form.errors.content" />
                </div>

                <!-- PUBLISHED -->
                <div class="flex items-center justify-between">
                    <Label>Published</Label>
                    <Switch v-model="form.published" />
                </div>

                <!-- ACTIONS -->
                <div class="flex gap-3 mt-4">
                    <Button type="submit" :disabled="form.processing">
                        Create Post
                    </Button>

                    <Button
                        type="button"
                        variant="outline"
                        @click="router.visit(index().url)"
                    >
                        Cancel
                    </Button>
                </div>

            </form>
        </div>
    </AppLayout>
</template>