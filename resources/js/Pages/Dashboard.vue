<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type WeatherData, type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import MapView from '@/components/MapView.vue';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import { ref, computed } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const props = defineProps<{
    weather?: WeatherData;
}>();

const searchQuery   = ref('');
const searchResult  = ref<WeatherData | null>(null);
const searchError   = ref('');
const searchLoading = ref(false);

const displayedWeather = computed<WeatherData | null>(() => {
    const w = searchResult.value ?? props.weather;

    if (!w?.main || !w?.weather) return null;

    return w;
});

async function searchCity() {
    const city = searchQuery.value.trim();
    if (!city) return;

    searchLoading.value = true;
    searchError.value   = '';
    searchResult.value  = null;

    try {
        const res  = await fetch(`/weather/search?city=${encodeURIComponent(city)}`, {
            headers: { Accept: 'application/json' },
        });

        const data = await res.json();

        if (!res.ok) {
            searchError.value = data.error ?? 'Linna ei leitud.';
        } else {
            searchResult.value = data as WeatherData;
        }
    } catch {
        searchError.value = 'Võrgu viga — proovi uuesti.';
    } finally {
        searchLoading.value = false;
    }
}

function clearSearch() {
    searchQuery.value  = '';
    searchResult.value = null;
    searchError.value  = '';
}

function formatTime(unix: number, timezoneOffset: number): string {
    const d = new Date((unix + timezoneOffset) * 1000);
    return d.toUTCString().slice(17, 22);
}

function windDirection(deg: number): string {
    const dirs = ['P', 'KI', 'I', 'KL', 'L', 'LL', 'LO', 'LP'];
    return dirs[Math.round(deg / 45) % 8];
}
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-6">

            <!-- HEADER -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold">Dashboard</h1>
                    <p class="text-sm opacity-60">Welcome back 👋</p>
                </div>

                <!-- SEARCH -->
                <div class="flex items-center gap-2 w-full md:w-[400px]">
                    <input
                        id="city-search"
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search city weather..."
                        class="flex-1 rounded-lg border border-sidebar-border/70 px-3 py-2 text-sm
                               focus:ring-2 focus:ring-primary outline-none"
                        @keyup.enter="searchCity"
                    />

                    <button
                        :disabled="searchLoading"
                        class="rounded-lg bg-green-600 px-4 py-2 text-sm text-white"
                        @click="searchCity"
                    >
                        {{ searchLoading ? '...' : 'Search' }}
                    </button>
                </div>
            </div>

            <!-- MAIN GRID -->
            <div class="grid gap-6 lg:grid-cols-3 ">

                <!-- WEATHER (BIG CARD) -->
                <div class="lg:col-span-2 rounded-2xl h-fit p-6 text-white relative overflow-hidden
                            bg-gradient-to-br from-blue-500 to-blue-400 shadow-lg">

                    <template v-if="displayedWeather">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm opacity-80">
                                    {{ displayedWeather.name }},
                                    {{ displayedWeather.sys?.country }}
                                </p>

                                <h2 class="text-6xl font-bold mt-2">
                                    {{ Math.round(displayedWeather.main.temp) }}°C
                                </h2>

                                <p class="capitalize opacity-90">
                                    {{ displayedWeather.weather[0].description }}
                                </p>

                                <p class="text-sm opacity-70">
                                    Feels like {{ Math.round(displayedWeather.main.feels_like) }}°C
                                </p>
                            </div>

                            <img
                                v-if="displayedWeather.weather?.[0]?.icon"
                                class="w-24"
                                :src="`https://openweathermap.org/img/wn/${displayedWeather.weather[0].icon}@2x.png`"
                            />
                        </div>

                        <!-- STATS -->
                        <div class="grid grid-cols-4 gap-4 mt-6 text-sm bg-white/20 p-4 rounded-xl">
                            <div>↑ {{ Math.round(displayedWeather.main.temp_max) }}°</div>
                            <div>↓ {{ Math.round(displayedWeather.main.temp_min) }}°</div>
                            <div>💧 {{ displayedWeather.main.humidity }}%</div>
                            <div>
                                🌬 {{ Math.round(displayedWeather.wind.speed) }} m/s
                            </div>
                        </div>
                    </template>

                    <p v-else class="opacity-70">No weather data</p>
                </div>

                <!-- SIDE CARDS -->
                <div class="flex flex-col gap-4">

                    <!-- BLOG -->
                    <a href="/posts"
                       class="rounded-2xl border p-5 hover:shadow-md transition bg-white dark:bg-neutral-900">
                        <p class="text-lg font-semibold">Blog</p>
                        <p class="text-sm opacity-60">Read latest posts</p>

                        <div class="mt-3 text-primary text-sm font-medium">
                            View posts →
                        </div>
                    </a>

                    <!-- STORE -->
                    <a href="/products"
                       class="rounded-2xl border p-5 hover:shadow-md transition bg-white dark:bg-neutral-900">
                        <p class="text-lg font-semibold">E-Store</p>
                        <p class="text-sm opacity-60">Browse products</p>

                        <div class="mt-3 text-primary text-sm font-medium">
                            View products →
                        </div>
                    </a>

                    <!-- API -->
                    <a href="/api"
                       class="rounded-2xl border p-5 hover:shadow-md transition bg-white dark:bg-neutral-900">
                        <p class="text-lg font-semibold">API</p>
                        <p class="text-sm opacity-60">Explore your API</p>

                        <div class="mt-3 text-primary text-sm font-medium">
                            Go to API →
                        </div>
                    </a>
                </div>
            </div>

            <!-- MAP SECTION -->
            <div class="rounded-2xl border p-4 shadow-sm bg-white dark:bg-neutral-900">
                <div class="flex justify-between items-center mb-3">
                    <h2 class="font-semibold">Map</h2>
                    <button class="text-sm opacity-60">Filter</button>
                </div>

                <div class="h-[400px] rounded-xl overflow-hidden">
                    <MapView />
                </div>
            </div>

        </div>
    </AppLayout>
</template>