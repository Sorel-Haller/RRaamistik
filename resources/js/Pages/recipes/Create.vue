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

            <!-- Header -->
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

            <!-- Main Recipe Info -->
            <div
                class="border border-gray-200 rounded-2xl bg-white dark:bg-neutral-900 p-8 grid lg:grid-cols-2 gap-10"
            >
                <!-- Image URL + Preview -->
                <div>
                    <p class="font-semibold text-gray-700 dark:text-gray-200 mb-4">
                        Recipe Image URL
                    </p>

                    <input
                        v-model="form.image"
                        type="text"
                        placeholder="https://example.com/recipe-image.jpg"
                        class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 outline-none shadow-sm"
                    />

                    <div
                        class="mt-6 border border-gray-200 dark:border-gray-700 rounded-3xl h-80 flex items-center justify-center bg-gray-50 dark:bg-neutral-800 overflow-hidden"
                    >
                        <img
                            v-if="form.image"
                            :src="form.image"
                            alt="Recipe Preview"
                            @error="form.image = ''"
                            class="w-full h-full object-cover rounded-3xl"
                        />

                        <span
                            v-else
                            class="text-gray-500 dark:text-gray-400 font-medium"
                        >
                            Image preview will appear here
                        </span>
                    </div>
                </div>

                <!-- Form Fields -->
                <div class="space-y-6">
                    <div>
                        <label
                            class="block font-semibold text-gray-700 dark:text-gray-200 mb-2"
                        >
                            Recipe Title
                        </label>

                        <input
                            v-model="form.title"
                            class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 outline-none shadow-sm"
                        />
                    </div>

                    <div>
                        <label
                            class="block font-semibold text-gray-700 dark:text-gray-200 mb-2"
                        >
                            Description
                        </label>

                        <textarea
                            v-model="form.description"
                            placeholder="Write a short, appetizing summary of the recipe..."
                            class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 h-32 focus:ring-2 focus:ring-indigo-500 outline-none shadow-sm"
                        />
                    </div>

                    <!-- Meta Info -->
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 justify-end items-end">
                        <div>
                            <label class="block text-sm font-semibold mb-2">
                                Difficulty
                            </label>

                            <select
                                v-model="form.difficulty"
                                class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3 py-3 text-gray-500"
                            >
                                <option value="" disabled>Select...</option>
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advanced">Advanced</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2">
                                Cooking Time
                            </label>

                            <input
                                v-model="form.cooking_time"
                                placeholder="e.g. 30 mins"
                                class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3 py-3"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2">
                                Prep Time
                            </label>

                            <input
                                v-model="form.prep_time"
                                placeholder="e.g. 15 mins"
                                class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3 py-3"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2">
                                Servings
                            </label>

                            <input
                                v-model="form.servings"
                                placeholder="e.g. 4"
                                class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3 py-3"
                            />
                        </div>
                    </div>

                    <!-- Nutrition -->
                    <div>
                        <label
                            class="block font-semibold text-gray-700 dark:text-gray-200 mb-4"
                        >
                            Nutritional Information (Optional)
                        </label>

                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                            <input
                                v-model="form.calories"
                                placeholder="Calories (e.g. 450 kcal)"
                                class="border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3"
                            />

                            <input
                                v-model="form.protein"
                                placeholder="Protein (e.g. 38g)"
                                class="border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3"
                            />

                            <input
                                v-model="form.carbs"
                                placeholder="Carbs (e.g. 12g)"
                                class="border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3"
                            />

                            <input
                                v-model="form.fat"
                                placeholder="Fat (e.g. 28g)"
                                class="border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ingredients + Instructions -->
            <div class="grid lg:grid-cols-2 gap-6">

                <!-- Ingredients -->
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
                            class="border border-gray-300 dark:border-gray-700 rounded-xl px-4 py-3"
                        />

                        <input
                            v-model="ingredient.name"
                            placeholder="Ingredient name"
                            class="border border-gray-300 dark:border-gray-700 rounded-xl px-4 py-3"
                        />
                    </div>

                    <button
                        type="button"
                        @click="addIngredient"
                        class="w-full border border-dashed rounded-xl py-3 text-white bg-green-600 hover:bg-green-700"
                    >
                        + Add Ingredient
                    </button>
                </div>

                <!-- Instructions -->
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
                            class="w-full border border-gray-300 dark:border-gray-700 rounded-xl px-4 py-3"
                        />

                        <textarea
                            v-model="step.description"
                            placeholder="Describe this step"
                            class="w-full border border-gray-300 dark:border-gray-700 rounded-xl px-4 py-3 h-24"
                        />
                    </div>

                    <button
                        type="button"
                        @click="addStep"
                        class="w-full border border-dashed rounded-xl py-3 text-white bg-green-600 hover:bg-green-700"
                    >
                        + Add Next Step
                    </button>
                </div>

            </div>

        </div>
    </AppLayout>
</template>