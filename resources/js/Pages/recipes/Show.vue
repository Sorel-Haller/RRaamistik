<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Flame } from 'lucide-vue-next';

type Recipe = {
    id: number;
    title: string;
    description: string;
    instructions: string;
    calories: number | null;
    difficulty: string;
    image?: string;
    created_at: string;
};

const { recipe } = defineProps<{ recipe: Recipe }>();

const steps = recipe.instructions
    .split('\n')
    .map(s => s.trim())
    .filter(Boolean);

const difficultyStyle: Record<string, string> = {
    beginner:   'bg-emerald-50 text-emerald-600',
    intermediate: 'bg-amber-50   text-amber-600',
    advanced:   'bg-red-50     text-red-600',
};
</script>

<template>
    <Head :title="recipe.title" />
    <AppLayout>
        <div class="p-6 flex flex-col gap-6 max-w-3xl mx-auto">
            <Link href="/recipes" class="text-sm text-muted-foreground hover:text-foreground transition-colors">
                ← Back to Recipes
            </Link>
            <div class="bg-background border border-border rounded-2xl overflow-hidden shadow-sm">
                <img
                    :src="recipe.image || `https://picsum.photos/seed/${recipe.id}/800/400`"
                    :alt="recipe.title"
                    class="w-full h-64 object-cover"
                />
                <div class="p-6 flex flex-col gap-3">
                    <h1 class="text-3xl font-bold text-foreground leading-tight">{{ recipe.title }}</h1>
                    <p class="text-muted-foreground leading-relaxed">{{ recipe.description }}</p>
                    <div class="flex items-center gap-4 pt-4 border-t border-border mt-2 flex-wrap">
                        <span
                            class="text-xs font-bold px-2.5 py-1 rounded-full uppercase tracking-wide"
                            :class="difficultyStyle[recipe.difficulty] ?? 'bg-muted text-muted-foreground'"
                        >
                            {{ recipe.difficulty }}
                        </span>
                        <div v-if="recipe.calories" class="flex items-center gap-1.5 text-sm font-medium text-foreground">
                            <Flame :size="16" class="text-orange-500" />
                            {{ recipe.calories }} kcal
                        </div>
                        <span class="text-sm text-muted-foreground ml-auto">
                            Added {{ new Date(recipe.created_at).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' }) }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="bg-background border border-border rounded-2xl p-6 shadow-sm">
                <h2 class="text-xl font-bold text-foreground mb-5">Instructions</h2>
                <ol v-if="steps.length" class="flex flex-col gap-4">
                    <li v-for="(step, i) in steps" :key="i" class="flex gap-4">
                        <p class="text-sm text-foreground leading-relaxed pt-0.5">{{ step }}</p>
                    </li>
                </ol>
                <p v-else class="text-sm text-muted-foreground">No instructions provided.</p>
            </div>
        </div>
    </AppLayout>
</template>