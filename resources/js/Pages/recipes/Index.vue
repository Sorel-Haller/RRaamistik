<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Plus, Search, Flame } from 'lucide-vue-next';
import { ref, watch } from 'vue';

type Recipe = {
    id: number;
    title: string;
    description: string;
    instructions: string;
    calories: number | null;
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

const search     = ref(props.filters.search     ?? '');
const difficulty = ref(props.filters.difficulty ?? '');
const sort       = ref(props.filters.sort       ?? 'latest');
const limit      = ref(props.filters.limit      ?? 12);

const difficultyStyle: Record<string, string> = {
    beginner:   'bg-emerald-50 text-emerald-600',
    intermediate: 'bg-amber-50   text-amber-600',
    advanced:   'bg-red-50     text-red-600',
};

// Debounced Inertia request whenever any filter changes
let debounce: ReturnType<typeof setTimeout>;
watch([search, difficulty, sort, limit], () => {
    clearTimeout(debounce);
    debounce = setTimeout(() => {
        router.get(
            '/recipes',
            {
                search:     search.value     || undefined,
                difficulty: difficulty.value || undefined,
                sort:       sort.value,
                limit:      limit.value,
            },
            { preserveState: true, preserveScroll: true, replace: true },
        );
    }, 300);
});
</script>

<template>
    <Head title="Recipes" />

    <AppLayout>
        <div class="p-6 flex flex-col gap-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-foreground">Recipe Database</h1>
                    <p class="text-sm text-muted-foreground mt-1">Browse and manage your cooking blog recipes.</p>
                </div>
                <Link
                    href="/recipes/create"
                    class="flex items-center gap-2 bg-primary text-primary-foreground px-4 py-2 rounded-lg text-sm font-medium shadow-sm hover:bg-primary/90 transition-colors"
                >
                    <Plus :size="16" />
                    Add New Recipe
                </Link>
            </div>
            <div class="flex flex-wrap items-center gap-3 bg-background border border-border p-4 rounded-xl shadow-sm">
                <div class="flex items-center gap-2 bg-muted border border-border rounded-lg px-3 py-2 w-60 text-muted-foreground">
                    <Search :size="16" class="shrink-0" />
                    <input
                        v-model="search"
                        placeholder="Search recipes..."
                        class="bg-transparent text-sm text-foreground placeholder:text-muted-foreground outline-none w-full"
                    />
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-sm font-medium text-muted-foreground">Difficulty:</span>
                    <select
                        v-model="difficulty"
                        class="border border-border bg-background rounded-lg px-3 py-2 text-sm text-foreground outline-none hover:bg-muted/50 transition-colors"
                    >
                        <option value="">All</option>
                        <option value="beginner">Beginner</option>
                        <option value="intermediate">Intermediate</option>
                        <option value="advanced">Advanced</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-sm font-medium text-muted-foreground">Sort:</span>
                    <select
                        v-model="sort"
                        class="border border-border bg-background rounded-lg px-3 py-2 text-sm text-foreground outline-none hover:bg-muted/50 transition-colors"
                    >
                        <option value="latest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                        <option value="calories_asc">Fewest Calories</option>
                        <option value="calories_desc">Most Calories</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-sm font-medium text-muted-foreground">Show:</span>
                    <select
                        v-model="limit"
                        class="border border-border bg-background rounded-lg px-3 py-2 text-sm text-foreground outline-none hover:bg-muted/50 transition-colors"
                    >
                        <option :value="6">6</option>
                        <option :value="12">12</option>
                        <option :value="24">24</option>
                    </select>
                </div>

                <p class="ml-auto text-sm text-muted-foreground">
                    {{ recipes.length }} recipe{{ recipes.length !== 1 ? 's' : '' }}
                </p>
            </div>
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                <Link
                    v-for="recipe in recipes"
                    :key="recipe.id"
                    :href="`/recipes/${recipe.id}`"
                    class="bg-background border border-border rounded-xl overflow-hidden shadow-sm flex flex-col group hover:shadow-md transition-shadow"
                >
                    <div class="h-44 overflow-hidden bg-muted relative">
                        <img
                            :src="recipe.image || `https://picsum.photos/seed/${recipe.id}/400/300`"
                            :alt="recipe.title"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                        />
                    </div>

                    <div class="p-4 flex flex-col flex-1">
                        <h3 class="font-bold text-base text-foreground line-clamp-1 mb-1">{{ recipe.title }}</h3>
                        <p class="text-sm text-muted-foreground line-clamp-2 flex-1">{{ recipe.description }}</p>

                        <div class="flex items-center justify-between pt-4 mt-4 border-t border-border">
                            <div v-if="recipe.calories" class="flex items-center gap-1.5 text-xs font-medium text-foreground">
                                <Flame :size="13" class="text-orange-500" />
                                {{ recipe.calories }} kcal
                            </div>
                            <span v-else class="text-xs text-muted-foreground">—</span>

                            <span
                                class="text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wide"
                                :class="difficultyStyle[recipe.difficulty] ?? 'bg-muted text-muted-foreground'"
                            >
                                {{ recipe.difficulty }}
                            </span>
                        </div>
                    </div>
                </Link>
                <div v-if="recipes.length === 0" class="col-span-full py-20 flex flex-col items-center gap-3 text-muted-foreground">
                    <Search :size="40" class="opacity-30" />
                    <p class="text-sm">No recipes found. Try a different search.</p>
                </div>
            </div>

        </div>
    </AppLayout>
</template>