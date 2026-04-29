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
        {
            amount: '',
            name: '',
        },
    ],
    instructions: [
        {
            title: '',
            description: '',
        },
    ],
});

const addIngredient = () => {
    form.value.ingredients.push({
        amount: '',
        name: '',
    });
};

const addStep = () => {
    form.value.instructions.push({
        title: '',
        description: '',
    });
};

const submit = () => {
    router.post('/recipes', form.value, {
        onSuccess: () => {
            console.log('Recipe created successfully');
        },
    });
};
</script>

<template>
    <Head title="Create Recipe" />

    <AppLayout>
        <div class="p-6 flex flex-col gap-6">

            <div class="flex items-center justify-between">
                <div>
                    <Link
                        href="/recipes"
                        class="text-sm text-gray-500 hover:text-black"
                    >
                        ← Back
                    </Link>

                    <h1 class="text-3xl font-bold mt-2">
                        Create New Recipe
                    </h1>
                </div>

                <button
                    type="button"
                    @click="submit"
                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl font-medium"
                >
                    Publish Recipe
                </button>
            </div>

            <div
                class="border rounded-2xl bg-white dark:bg-neutral-900 p-6 grid lg:grid-cols-2 gap-6"
            >
                <div>
                    <p class="font-medium mb-3">
                        Recipe Image
                    </p>

                    <input
                        v-model="form.image"
                        placeholder="Paste image URL"
                        class="w-full border rounded-xl px-4 py-3"
                    />
                </div>

                <div class="space-y-4">
                    <input
                        v-model="form.title"
                        placeholder="Recipe Title"
                        class="w-full border rounded-xl px-4 py-3"
                    />

                    <textarea
                        v-model="form.description"
                        placeholder="Description"
                        class="w-full border rounded-xl px-4 py-3 h-28"
                    />

                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                        <select
                            v-model="form.difficulty"
                            class="border rounded-xl px-4 py-3"
                        >
                            <option value="" disabled>
                                Select Difficulty
                            </option>
                            <option value="beginner">
                                Beginner
                            </option>
                            <option value="intermediate">
                                Intermediate
                            </option>
                            <option value="advanced">
                                Advanced
                            </option>
                        </select>

                        <input
                            v-model="form.cooking_time"
                            placeholder="Cooking Time"
                            class="border rounded-xl px-4 py-3"
                        />

                        <input
                            v-model="form.prep_time"
                            placeholder="Prep Time"
                            class="border rounded-xl px-4 py-3"
                        />

                        <input
                            v-model="form.servings"
                            placeholder="Servings"
                            class="border rounded-xl px-4 py-3"
                        />
                    </div>

                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                        <input
                            v-model="form.calories"
                            placeholder="Calories"
                            class="border rounded-xl px-4 py-3"
                        />

                        <input
                            v-model="form.protein"
                            placeholder="Protein"
                            class="border rounded-xl px-4 py-3"
                        />

                        <input
                            v-model="form.carbs"
                            placeholder="Carbs"
                            class="border rounded-xl px-4 py-3"
                        />

                        <input
                            v-model="form.fat"
                            placeholder="Fat"
                            class="border rounded-xl px-4 py-3"
                        />
                    </div>
                </div>
            </div>

            <div class="grid lg:grid-cols-2 gap-6">

                <div
                    class="border rounded-2xl bg-white dark:bg-neutral-900 p-6"
                >
                    <h2 class="text-xl font-bold mb-4">
                        Ingredients
                    </h2>

                    <div
                        v-for="(ingredient, index) in form.ingredients"
                        :key="index"
                        class="grid grid-cols-2 gap-3 mb-3"
                    >
                        <input
                            v-model="ingredient.amount"
                            placeholder="Amount"
                            class="border rounded-xl px-4 py-3"
                        />

                        <input
                            v-model="ingredient.name"
                            placeholder="Ingredient name"
                            class="border rounded-xl px-4 py-3"
                        />
                    </div>

                    <button
                        type="button"
                        @click="addIngredient"
                        class="w-full border border-dashed rounded-xl py-3 text-indigo-600"
                    >
                        + Add Ingredient
                    </button>
                </div>

                <div
                    class="border rounded-2xl bg-white dark:bg-neutral-900 p-6"
                >
                    <h2 class="text-xl font-bold mb-4">
                        Instructions
                    </h2>

                    <div
                        v-for="(step, index) in form.instructions"
                        :key="index"
                        class="space-y-3 mb-5"
                    >
                        <input
                            v-model="step.title"
                            :placeholder="`Step ${index + 1} title`"
                            class="w-full border rounded-xl px-4 py-3"
                        />

                        <textarea
                            v-model="step.description"
                            placeholder="Describe this step"
                            class="w-full border rounded-xl px-4 py-3 h-24"
                        />
                    </div>

                    <button
                        type="button"
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