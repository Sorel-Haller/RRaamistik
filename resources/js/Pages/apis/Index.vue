<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Search } from 'lucide-vue-next';
import { ref, computed, onMounted } from 'vue';

type Movie = {
    id: number;
    title: string;
    description: string;
    image?: string;
    director?: string;
    genre?: string;
    release_year?: number;
    rating?: number;
};

const movies = ref<Movie[]>([]);
const loading = ref(false);
const error = ref('');

const search = ref('');
const sort = ref('latest');

const OTHER_API =
    'https://ralfiharjutus.ta24siim.itmajakas.ee/api/movies';

async function fetchMovies() {
    loading.value = true;

    try {
        const res = await fetch(OTHER_API);
        const json = await res.json();

        movies.value = (json.data ?? json).map((m: any) => ({
            id: m.id,
            title: m.title,
            description: m.description,
            image: m.image,
            director: m.director,
            genre: m.genre,
            release_year: m.release_year,
            rating: m.rating,
        }));
    } catch (e) {
        error.value = 'Failed to load API';
    } finally {
        loading.value = false;
    }
}

onMounted(fetchMovies);

const filteredMovies = computed(() => {
    let result = [...movies.value];

    // SEARCH
    if (search.value) {
        result = result.filter(movie =>
            movie.title
                .toLowerCase()
                .includes(search.value.toLowerCase())
        );
    }

    // SORT
    switch (sort.value) {
        case 'oldest':
            result.sort(
                (a, b) =>
                    (a.release_year ?? 0) -
                    (b.release_year ?? 0)
            );
            break;

        case 'rating':
            result.sort(
                (a, b) =>
                    (b.rating ?? 0) -
                    (a.rating ?? 0)
            );
            break;

        default:
            result.sort(
                (a, b) =>
                    (b.release_year ?? 0) -
                    (a.release_year ?? 0)
            );
    }

    return result;
});
</script>

<template>
    <Head title="API" />

    <AppLayout>
        <div class="p-6 flex flex-col gap-6">

            <!-- HEADER -->
            <div>
                <h1 class="text-2xl font-bold text-foreground">
                    API Movies
                </h1>
            </div>

            <!-- FILTERS -->
            <div class="flex flex-wrap items-center gap-3 bg-background border border-border p-4 rounded-xl shadow-sm">

                <!-- SEARCH -->
                <div class="flex items-center gap-2 bg-muted border border-border rounded-lg px-3 py-2 w-60 text-muted-foreground">
                    <Search :size="16" class="shrink-0" />

                    <input
                        v-model="search"
                        placeholder="Search movies..."
                        class="bg-transparent text-sm text-foreground placeholder:text-muted-foreground outline-none w-full"
                    />
                </div>

                <!-- SORT -->
                <div class="flex items-center gap-2">
                    <span class="text-sm font-medium text-muted-foreground">
                        Sort:
                    </span>

                    <select
                        v-model="sort"
                        class="border border-border bg-background rounded-lg px-3 py-2 text-sm"
                    >
                        <option value="latest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                        <option value="rating">Highest Rated</option>
                    </select>
                </div>

                <a
                    :href="OTHER_API"
                    target="_blank"
                    class="ml-auto text-sm text-muted-foreground hover:text-foreground"
                >
                    Open API
                </a>
            </div>

            <!-- LOADING -->
            <div
                v-if="loading"
                class="text-muted-foreground"
            >
                Loading movies...
            </div>

            <!-- ERROR -->
            <div
                v-else-if="error"
                class="text-red-500"
            >
                {{ error }}
            </div>

            <!-- GRID -->
            <div
                v-else
                class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
            >
                <div
                    v-for="movie in filteredMovies"
                    :key="movie.id"
                    class="bg-background border border-border rounded-xl overflow-hidden shadow-sm flex flex-col group hover:shadow-md transition-shadow"
                >
                    <div class="h-44 overflow-hidden bg-muted">
                        <img
                            :src="movie.image || `https://picsum.photos/seed/${movie.id}/400/300`"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                        />
                    </div>

                    <div class="p-4 flex flex-col flex-1">

                        <h3 class="font-bold text-base text-foreground">
                            {{ movie.title }}
                        </h3>

                        <p class="text-sm text-muted-foreground mt-1 flex-1">
                            {{ movie.description }}
                        </p>

                        <div class="pt-4 mt-4 border-t border-border text-sm space-y-1">
                            <p>
                                <span class="font-medium text-foreground">
                                    Director:
                                </span>
                                {{ movie.director }}
                            </p>

                            <p>
                                <span class="font-medium text-foreground">
                                    Genre:
                                </span>
                                {{ movie.genre }}
                            </p>

                            <p>
                                <span class="font-medium text-foreground">
                                    Year:
                                </span>
                                {{ movie.release_year }}
                            </p>

                            <p>
                                <span class="font-medium text-foreground">
                                    Rating:
                                </span>
                                ⭐ {{ movie.rating }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>