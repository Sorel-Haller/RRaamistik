<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = ref({
    title: '',
    image: '',
    description: '',
    instructions: '',
    calories: '',
    difficulty: 'beginner',
});

const errors = ref<Record<string, string>>({});

const submit = () => {
    errors.value = {};
    router.post('/recipes', form.value, {
        onError: (e) => { errors.value = e; },
    });
};
</script>

<template>
    <Head title="Add Recipe" />
    <AppLayout>
        <div class="p-6 max-w-2xl mx-auto flex flex-col gap-6">
            <div>
                <Link href="/recipes" class="text-sm text-muted-foreground hover:text-foreground transition-colors">
                    ← Back to Recipes
                </Link>
                <h1 class="text-2xl font-bold text-foreground mt-2">Add New Recipe</h1>
            </div>
            <div class="bg-background border border-border rounded-2xl p-6 shadow-sm flex flex-col gap-5">
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-semibold text-foreground">
                        Title <span class="text-red-500">*</span>
                    </label>
                    <input
                        v-model="form.title"
                        placeholder="e.g. Creamy Tuscan Pasta"
                        class="border border-border rounded-lg px-3 py-2.5 text-sm bg-muted placeholder:text-muted-foreground outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                    />
                    <p v-if="errors.title" class="text-xs text-red-500">{{ errors.title }}</p>
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-semibold text-foreground">Image URL</label>
                    <input
                        v-model="form.image"
                        placeholder="https://example.com/image.jpg"
                        class="border border-border rounded-lg px-3 py-2.5 text-sm bg-muted placeholder:text-muted-foreground outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                    />
                    <div v-if="form.image" class="mt-1 rounded-xl overflow-hidden border border-border h-40 bg-muted">
                        <img :src="form.image" class="w-full h-full object-cover" @error="($event.target as HTMLImageElement).style.display='none'" />
                    </div>
                    <p v-if="errors.image" class="text-xs text-red-500">{{ errors.image }}</p>
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-semibold text-foreground">
                        Description <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        v-model="form.description"
                        placeholder="A short summary of the recipe..."
                        rows="3"
                        class="border border-border rounded-lg px-3 py-2.5 text-sm bg-muted placeholder:text-muted-foreground outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition resize-none"
                    />
                    <p v-if="errors.description" class="text-xs text-red-500">{{ errors.description }}</p>
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-semibold text-foreground">
                        Instructions <span class="text-red-500">*</span>
                    </label>
                    <p class="text-xs text-muted-foreground -mt-0.5">One step per line.</p>
                    <textarea
                        v-model="form.instructions"
                        placeholder="Step 1: Preheat oven to 200°C&#10;Step 2: Mix the ingredients&#10;Step 3: Bake for 25 minutes"
                        rows="6"
                        class="border border-border rounded-lg px-3 py-2.5 text-sm bg-muted placeholder:text-muted-foreground outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition resize-none"
                    />
                    <p v-if="errors.instructions" class="text-xs text-red-500">{{ errors.instructions }}</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-semibold text-foreground">Calories (kcal)</label>
                        <input
                            v-model="form.calories"
                            type="number"
                            min="0"
                            placeholder="e.g. 450"
                            class="border border-border rounded-lg px-3 py-2.5 text-sm bg-muted placeholder:text-muted-foreground outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                        />
                        <p v-if="errors.calories" class="text-xs text-red-500">{{ errors.calories }}</p>
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-semibold text-foreground">
                            Difficulty <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.difficulty"
                            class="border border-border rounded-lg px-3 py-2.5 text-sm bg-muted text-foreground outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                        >
                            <option value="beginner">Beginner</option>
                            <option value="intermediate">Intermediate</option>
                            <option value="advanced">Advanced</option>
                        </select>
                        <p v-if="errors.difficulty" class="text-xs text-red-500">{{ errors.difficulty }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 pt-2 border-t border-border">
                    <Link
                        href="/recipes"
                        class="px-4 py-2 border border-border rounded-lg text-sm font-medium hover:bg-muted transition-colors"
                    >
                        Cancel
                    </Link>
                    <button
                        @click="submit"
                        class="flex items-center gap-2 bg-primary text-primary-foreground px-5 py-2 rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors"
                    >
                        Publish Recipe
                    </button>
                </div>

            </div>
        </div>
    </AppLayout>
</template>