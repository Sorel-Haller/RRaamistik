<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

type Ingredient = {
    amount: string;
    name: string;
};

type Instruction = {
    title: string;
    description: string;
};

type Recipe = {
    id: number;
    title: string;
    description: string;
    cooking_time: number;
    prep_time?: number;
    difficulty: string;
    image?: string;
    servings?: number;

    calories?: number;
    protein?: string;
    carbs?: string;
    fat?: string;

    ingredients?: Ingredient[];
    instructions?: Instruction[];
};

const { recipe } = defineProps<{
    recipe: Recipe;
}>();
</script>

<template>
    <Head :title="recipe.title" />

    <AppLayout>
        <div class="p-6 flex flex-col gap-6">

            <!-- TOP BAR -->
            <div class="flex items-center justify-between">
                <Link
                    href="/recipes"
                    class="text-sm text-gray-500 hover:text-black"
                >
                    ← Back to Recipes
                </Link>

            </div>

            <!-- MAIN CARD -->
            <div
                class="border rounded-2xl bg-white dark:bg-neutral-900 overflow-hidden"
            >
                <!-- HERO SECTION -->
                <div
                    class="p-6 border-b grid lg:grid-cols-[380px_1fr] gap-8 items-center"
                >
                    <!-- IMAGE -->
                    <div>
                        <img
                            :src="recipe.image || 'https://picsum.photos/800/500'"
                            class="w-full h-[250px] object-cover rounded-2xl"
                        />
                    </div>

                    <!-- CONTENT -->
                    <div class="space-y-5">
                        <!-- META -->
                        <div class="flex flex-wrap items-center gap-4">
                            <span
                                class="px-3 py-1 rounded-full text-sm font-semibold bg-orange-100 text-orange-500 uppercase"
                            >
                                {{ recipe.difficulty }}
                            </span>

                            <span class="text-sm text-gray-500">
                                ⏱ {{ recipe.cooking_time }} mins
                            </span>

                            <span class="text-sm text-gray-500">
                                👥 {{ recipe.servings || 4 }} Servings
                            </span>
                        </div>

                        <!-- TITLE -->
                        <h1 class="text-4xl font-bold leading-tight">
                            {{ recipe.title }}
                        </h1>

                        <!-- DESCRIPTION -->
                        <p class="text-gray-500 leading-relaxed text-lg">
                            {{ recipe.description }}
                        </p>

                        <!-- NUTRITION -->
                        <div
                            class="grid grid-cols-2 md:grid-cols-4 gap-6 pt-4 border-t"
                        >
                            <div>
                                <p class="text-xs font-semibold text-gray-400 uppercase">
                                    Calories
                                </p>
                                <p class="text-2xl font-bold">
                                    {{ recipe.calories || 0 }} kcal
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold text-gray-400 uppercase">
                                    Protein
                                </p>
                                <p class="text-2xl font-bold">
                                    {{ recipe.protein || '0g' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold text-gray-400 uppercase">
                                    Carbs
                                </p>
                                <p class="text-2xl font-bold">
                                    {{ recipe.carbs || '0g' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold text-gray-400 uppercase">
                                    Fat
                                </p>
                                <p class="text-2xl font-bold">
                                    {{ recipe.fat || '0g' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BOTTOM SECTION -->
                <div class="p-6 grid lg:grid-cols-2 gap-6">

                    <!-- INGREDIENTS -->
                    <div
                        class="border rounded-2xl p-6"
                    >
                        <div class="flex items-center justify-between mb-5">
                            <h2 class="text-2xl font-bold">
                                Ingredients
                            </h2>

                        </div>

                        <div
                            v-if="recipe.ingredients?.length"
                            class="space-y-4"
                        >
                            <div
                                v-for="(ingredient, index) in recipe.ingredients"
                                :key="index"
                                class="flex items-start gap-4 border-b pb-4 last:border-b-0"
                            >
                                <input
                                    type="checkbox"
                                    class="mt-1 rounded"
                                />

                                <div class="flex gap-2 text-base">
                                    <span class="font-semibold">
                                        {{ ingredient.amount }}
                                    </span>

                                    <span class="text-gray-600">
                                        {{ ingredient.name }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div
                            v-else
                            class="text-gray-400"
                        >
                            No ingredients added
                        </div>
                    </div>

                    <!-- INSTRUCTIONS -->
                    <div
                        class="border rounded-2xl p-6"
                    >
                        <h2 class="text-2xl font-bold mb-5">
                            Instructions
                        </h2>

                        <div
                            v-if="recipe.instructions?.length"
                            class="space-y-6"
                        >
                            <div
                                v-for="(step, index) in recipe.instructions"
                                :key="index"
                                class="flex gap-4"
                            >
                                <!-- STEP NUMBER -->
                                <div
                                    class="w-9 h-9 rounded-full border flex items-center justify-center text-sm font-semibold text-indigo-600 shrink-0"
                                >
                                    {{ index + 1 }}
                                </div>

                                <!-- STEP CONTENT -->
                                <div class="space-y-1">
                                    <h3 class="font-semibold text-lg">
                                        {{ step.title }}
                                    </h3>

                                    <p class="text-gray-500 leading-relaxed">
                                        {{ step.description }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div
                            v-else
                            class="text-gray-400"
                        >
                            No instructions added
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </AppLayout>
</template>