<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

type Recipe = {
    id: number;
    title: string;
    description: string;
    cooking_time: number;
    difficulty: string;
    image?: string;
    servings?: number;
};

const { recipe } = defineProps<{
    recipe: Recipe;
}>();
</script>

<template>
    <Head :title="recipe.title" />

    <AppLayout>
        <div class="p-6 flex flex-col gap-6">

            <Link
                href="/recipes"
                class="text-sm text-gray-500 hover:text-black"
            >
                ← Back to Recipes
            </Link>

            <div
                class="border rounded-2xl bg-white dark:bg-neutral-900 p-6 grid lg:grid-cols-2 gap-6"
            >
                <img
                    :src="recipe.image || 'https://picsum.photos/800/500'"
                    class="w-full h-[320px] object-cover rounded-2xl"
                />

                <div class="space-y-4">
                    <p class="uppercase text-orange-500 font-medium">
                        {{ recipe.difficulty }}
                    </p>

                    <h1 class="text-4xl font-bold">
                        {{ recipe.title }}
                    </h1>

                    <p class="text-gray-500">
                        {{ recipe.description }}
                    </p>

                    <div class="grid grid-cols-2 gap-4 pt-4">
                        <div>
                            <p class="text-sm text-gray-400">
                                Cooking Time
                            </p>

                            <p class="font-semibold">
                                {{ recipe.cooking_time }} mins
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-400">
                                Servings
                            </p>

                            <p class="font-semibold">
                                {{ recipe.servings || 4 }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </AppLayout>
</template>