<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

type Recipe = {
    id: number;
    title: string;
    description: string;
    cooking_time: number;
    difficulty: string;
    image?: string;
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

watch([search, difficulty, sort, limit], () => {
    router.get('/recipes', {
        search: search.value,
        difficulty: difficulty.value,
        sort: sort.value,
        limit: limit.value,
    }, { preserveState: true, replace: true });
});

const showForm = ref(false);

const form = ref({
    title: '',
    image: '',
    description: '',
    cooking_time: 0,
    difficulty: 'beginner',
});

const submit = () => {
    router.post('/recipes', form.value, {
        onSuccess: () => {
            showForm.value = false;
            form.value = {
                title: '',
                image: '',
                description: '',
                cooking_time: 0,
                difficulty: 'beginner',
            };
        },
    });
};
</script>

<template>
<Head title="Recipes" />

<AppLayout>
<div class="p-6 flex flex-col gap-6">

    <!-- HEADER -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold">Recipes</h1>
        </div>

        <button
            @click="showForm = !showForm"
            class="bg-green-600 text-white px-4 py-2 rounded-lg"
        >
            Add New Recipe
        </button>
    </div>

    <!-- FILTER BAR -->
    <div class="flex flex-wrap gap-3 items-center bg-white dark:bg-neutral-900 p-4 rounded-xl border">

        <input
            id="search"
            v-model="search"
            placeholder="Search recipes..."
            class="border px-3 py-2 rounded w-60"
        />

        <select v-model="difficulty" class="border px-3 py-2 rounded">
            <option value="all">All</option>
            <option value="beginner">Beginner</option>
            <option value="intermediate">Intermediate</option>
            <option value="advanced">Advanced</option>
        </select>

        <select v-model="sort" class="border px-3 py-2 rounded">
            <option value="latest">Newest</option>
            <option value="time">Cooking Time</option>
        </select>

        <select v-model="limit" class="border px-3 py-2 rounded">
            <option :value="6">6</option>
            <option :value="12">12</option>
            <option :value="24">24</option>
        </select>
    </div>

    <!-- FORM -->
    <div v-if="showForm" class="border rounded-xl p-4 bg-white dark:bg-neutral-900 flex flex-col gap-3 max-w-lg">
        <input v-model="form.title" placeholder="Title" class="border px-3 py-2 rounded" />
        <input v-model="form.image" placeholder="Image URL" class="border px-3 py-2 rounded" />
        <textarea v-model="form.description" placeholder="Description" class="border px-3 py-2 rounded" />
        <input v-model="form.cooking_time" type="number" placeholder="Cooking time" class="border px-3 py-2 rounded" />

        <select v-model="form.difficulty" class="border px-3 py-2 rounded">
            <option value="beginner">Beginner</option>
            <option value="intermediate">Intermediate</option>
            <option value="advanced">Advanced</option>
        </select>

        <button @click="submit" class="bg-black text-white py-2 rounded">
            Save Recipe
        </button>
    </div>

    <!-- GRID -->
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">

        <div
            v-for="recipe in recipes"
            :key="recipe.id"
            class="rounded-xl border bg-white dark:bg-neutral-900 shadow-sm overflow-hidden flex flex-col"
        >
            <img
                :src="recipe.image || 'https://picsum.photos/400/300'"
                class="w-full h-44 object-cover"
            />

            <div class="p-4 flex flex-col gap-2 flex-1">
                <h3 class="font-semibold">{{ recipe.title }}</h3>

                <p class="text-sm text-muted-foreground line-clamp-2">
                    {{ recipe.description }}
                </p>

                <div class="flex justify-between text-xs mt-2">
                    <span>{{ recipe.cooking_time }} mins</span>
                    <span class="uppercase text-orange-500">
                        {{ recipe.difficulty }}
                    </span>
                </div>
            </div>
        </div>

    </div>

</div>
</AppLayout>
</template>