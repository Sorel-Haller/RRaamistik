<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

type Recipe = {
    id: number;
    title: string;
    description: string;
    cooking_time: number;
    difficulty: string;
    image?: string;
    servings?: number;
};

const props = defineProps<{
    recipes: Recipe[];
    filters: {
        search?: string;
        difficulty?: string;
        sort?: string;
        limit?: number;
    };
}>();

const search = ref(props.filters.search || '');
const difficulty = ref(props.filters.difficulty || 'all');
const sort = ref(props.filters.sort || 'latest');
const limit = ref(props.filters.limit || 12);

/*
Better watch() version:
- preserveScroll improves UX
- cleaner formatting
*/
watch(
    [search, difficulty, sort, limit],
    () => {
        router.get(
            '/recipes',
            {
                search: search.value,
                difficulty: difficulty.value,
                sort: sort.value,
                limit: limit.value,
            },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            }
        );
    }
);
</script>

<template>
    <Head title="Recipes" />

    <AppLayout>
        <div class="p-6 flex flex-col gap-6">

            <!-- HEADER -->
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold">Recipes</h1>
                    <p class="text-sm text-gray-500">
                        Manage your recipe collection
                    </p>
                </div>

                <Link
                    href="/recipes/create"
                    class="bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl font-medium"
                >
                    + Add New Recipe
                </Link>
            </div>

            <!-- FILTER BAR -->
            <div
                class="flex flex-wrap gap-3 items-center bg-white dark:bg-neutral-900 p-4 rounded-xl border"
            >
                <input
                    v-model="search"
                    placeholder="Search recipes..."
                    class="border px-4 py-2 rounded-lg w-64"
                />

                <select
                    v-model="difficulty"
                    class="border px-4 py-2 rounded-lg"
                >
                    <option value="all" class="bg-gray-400 dark:bg-gray-700 text-black dark:text-white">All Difficulty</option>
                    <option value="beginner" class="bg-gray-400 dark:bg-gray-700 text-black dark:text-white">Beginner</option>
                    <option value="intermediate" class="bg-gray-400 dark:bg-gray-700 text-black dark:text-white">Intermediate</option>
                    <option value="advanced" class="bg-gray-400 dark:bg-gray-700 text-black dark:text-white">Advanced</option>
                </select>

                <select
                    v-model="sort"
                    class="border px-4 py-2 rounded-lg"
                >
                    <option value="all" class="bg-gray-400 dark:bg-gray-700 text-black dark:text-white">Sort</option>
                    <option value="latest" class="bg-gray-400 dark:bg-gray-700 text-black dark:text-white">Newest</option>
                    <option value="time" class="bg-gray-400 dark:bg-gray-700 text-black dark:text-white">Cooking Time</option>
                </select>

                <select
                    v-model="limit"
                    class="border px-4 py-2 rounded-lg"
                >
                    <option :value="6" class="bg-gray-400 dark:bg-gray-700 text-black dark:text-white">6</option>
                    <option :value="12" class="bg-gray-400 dark:bg-gray-700 text-black dark:text-white">12</option>
                    <option :value="24" class="bg-gray-400 dark:bg-gray-700 text-black dark:text-white">24</option>
                </select>
            </div>

            <!-- GRID -->
            <div
                class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
            >
                <Link
                    v-for="recipe in recipes"
                    :key="recipe.id"
                    :href="`/recipes/${recipe.id}`"
                    class="rounded-2xl border bg-white dark:bg-neutral-900 shadow-sm overflow-hidden flex flex-col hover:shadow-md transition"
                >
                    <img
                        :src="recipe.image || 'https://picsum.photos/400/300'"
                        class="w-full h-48 object-cover"
                    />

                    <div class="p-4 flex flex-col gap-3 flex-1">
                        <h3 class="font-semibold text-lg">
                            {{ recipe.title }}
                        </h3>

                        <p class="text-sm text-gray-500 line-clamp-2">
                            {{ recipe.description }}
                        </p>

                        <div class="flex justify-between text-sm mt-auto">
                            <span>
                                {{ recipe.cooking_time }} mins
                            </span>

                            <span class="uppercase text-orange-500 font-medium">
                                {{ recipe.difficulty }}
                            </span>
                        </div>
                    </div>
                </Link>
            </div>

        </div>
    </AppLayout>
</template>