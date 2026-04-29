# resources/js/Pages/Recipes/Index.vue

```vue
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

watch([search, difficulty, sort, limit], () => {
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
            replace: true,
        },
    );
});
</script>

<template>
    <Head title="Recipes" />

    <AppLayout>
        <div class="p-6 flex flex-col gap-6">
            <!-- HEADER -->
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold">Recipes</h1>
                    <p class="text-sm text-gray-500">Manage your recipe collection</p>
                </div>

                <Link
                    href="/recipes/create"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-medium"
                >
                    + Add New Recipe
                </Link>
            </div>

            <!-- FILTER BAR -->
            <div class="flex flex-wrap gap-3 items-center bg-white dark:bg-neutral-900 p-4 rounded-xl border">
                <input
                    v-model="search"
                    placeholder="Search recipes..."
                    class="border px-4 py-2 rounded-lg w-64"
                />

                <select v-model="difficulty" class="border px-4 py-2 rounded-lg">
                    <option value="all">All Difficulty</option>
                    <option value="beginner">Beginner</option>
                    <option value="intermediate">Intermediate</option>
                    <option value="advanced">Advanced</option>
                </select>

                <select v-model="sort" class="border px-4 py-2 rounded-lg">
                    <option value="latest">Newest</option>
                    <option value="time">Cooking Time</option>
                </select>

                <select v-model="limit" class="border px-4 py-2 rounded-lg">
                    <option :value="6">6</option>
                    <option :value="12">12</option>
                    <option :value="24">24</option>
                </select>
            </div>

            <!-- GRID -->
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
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
                        <h3 class="font-semibold text-lg">{{ recipe.title }}</h3>

                        <p class="text-sm text-gray-500 line-clamp-2">
                            {{ recipe.description }}
                        </p>

                        <div class="flex justify-between text-sm mt-auto">
                            <span>{{ recipe.cooking_time }} mins</span>
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
```

---

# resources/js/Pages/Recipes/Create.vue

```vue
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = ref({
    title: '',
    image: '',
    description: '',
    difficulty: '',
    cooking_time: '',
    prep_time: '',
    servings: '',
    calories: '',
    protein: '',
    carbs: '',
    fat: '',
    ingredients: [
        { amount: '', name: '' },
    ],
    instructions: [
        { title: '', description: '' },
    ],
});

const addIngredient = () => {
    form.value.ingredients.push({ amount: '', name: '' });
};

const addStep = () => {
    form.value.instructions.push({ title: '', description: '' });
};

const submit = () => {
    router.post('/recipes', form.value);
};
</script>

<template>
    <Head title="Create Recipe" />

    <AppLayout>
        <div class="p-6 flex flex-col gap-6">
            <div class="flex items-center justify-between">
                <div>
                    <Link href="/recipes" class="text-sm text-gray-500">← Back</Link>
                    <h1 class="text-3xl font-bold mt-2">Create New Recipe</h1>
                </div>

                <button
                    @click="submit"
                    class="bg-indigo-600 text-white px-6 py-3 rounded-xl font-medium"
                >
                    Publish Recipe
                </button>
            </div>

            <div class="border rounded-2xl bg-white p-6 grid lg:grid-cols-2 gap-6">
                <!-- LEFT -->
                <div>
                    <p class="font-medium mb-3">Recipe Image</p>
                    <input
                        v-model="form.image"
                        placeholder="Paste image URL"
                        class="w-full border rounded-xl px-4 py-3"
                    />
                </div>

                <!-- RIGHT -->
                <div class="space-y-4">
                    <input v-model="form.title" placeholder="Recipe Title" class="w-full border rounded-xl px-4 py-3" />

                    <textarea
                        v-model="form.description"
                        placeholder="Description"
                        class="w-full border rounded-xl px-4 py-3 h-28"
                    />

                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                        <input v-model="form.difficulty" placeholder="Difficulty" class="border rounded-xl px-4 py-3" />
                        <input v-model="form.cooking_time" placeholder="Cooking Time" class="border rounded-xl px-4 py-3" />
                        <input v-model="form.prep_time" placeholder="Prep Time" class="border rounded-xl px-4 py-3" />
                        <input v-model="form.servings" placeholder="Servings" class="border rounded-xl px-4 py-3" />
                    </div>

                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                        <input v-model="form.calories" placeholder="Calories" class="border rounded-xl px-4 py-3" />
                        <input v-model="form.protein" placeholder="Protein" class="border rounded-xl px-4 py-3" />
                        <input v-model="form.carbs" placeholder="Carbs" class="border rounded-xl px-4 py-3" />
                        <input v-model="form.fat" placeholder="Fat" class="border rounded-xl px-4 py-3" />
                    </div>
                </div>
            </div>

            <div class="grid lg:grid-cols-2 gap-6">
                <!-- INGREDIENTS -->
                <div class="border rounded-2xl bg-white p-6">
                    <h2 class="text-xl font-bold mb-4">Ingredients</h2>

                    <div
                        v-for="(ingredient, index) in form.ingredients"
                        :key="index"
                        class="grid grid-cols-2 gap-3 mb-3"
                    >
                        <input v-model="ingredient.amount" placeholder="Amount" class="border rounded-xl px-4 py-3" />
                        <input v-model="ingredient.name" placeholder="Ingredient name" class="border rounded-xl px-4 py-3" />
                    </div>

                    <button
                        @click="addIngredient"
                        class="w-full border border-dashed rounded-xl py-3 text-indigo-600"
                    >
                        + Add Ingredient
                    </button>
                </div>

                <!-- INSTRUCTIONS -->
                <div class="border rounded-2xl bg-white p-6">
                    <h2 class="text-xl font-bold mb-4">Instructions</h2>

                    <div
                        v-for="(step, index) in form.instructions"
                        :key="index"
                        class="space-y-3 mb-5"
                    >
                        <input v-model="step.title" :placeholder="`Step ${index + 1} title`" class="w-full border rounded-xl px-4 py-3" />
                        <textarea
                            v-model="step.description"
                            placeholder="Describe this step"
                            class="w-full border rounded-xl px-4 py-3 h-24"
                        />
                    </div>

                    <button
                        @click="addStep"
                        class="w-full border border-dashed rounded-xl py-3 text-indigo-600"
                    >
                        + Add Next Step
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
```

---

# resources/js/Pages/Recipes/View.vue

```vue
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    recipe: Object,
});
</script>

<template>
    <Head :title="recipe.title" />

    <AppLayout>
        <div class="p-6 flex flex-col gap-6">
            <Link href="/recipes" class="text-sm text-gray-500">← Back to Recipes</Link>

            <div class="border rounded-2xl bg-white p-6 grid lg:grid-cols-2 gap-6">
                <img
                    :src="recipe.image || 'https://picsum.photos/800/500'"
                    class="w-full h-[320px] object-cover rounded-2xl"
                />

                <div class="space-y-4">
                    <p class="uppercase text-orange-500 font-medium">{{ recipe.difficulty }}</p>
                    <h1 class="text-4xl font-bold">{{ recipe.title }}</h1>
                    <p class="text-gray-500">{{ recipe.description }}</p>

                    <div class="grid grid-cols-2 gap-4 pt-4">
                        <div>
                            <p class="text-sm text-gray-400">Cooking Time</p>
                            <p class="font-semibold">{{ recipe.cooking_time }} mins</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-400">Servings</p>
                            <p class="font-semibold">{{ recipe.servings || 4 }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
```
