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
 
const displayedWeather = computed<WeatherData | null>(
    () => searchResult.value ?? props.weather ?? null,
);
 
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
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
 
            <div class="flex flex-col gap-1">
                <div class="flex items-center gap-2">
                    <input
                        id="search"
                        v-model="searchQuery"
                        type="text"
                        placeholder="Otsi linna ilma…"
                        class="flex-1 rounded-lg border border-sidebar-border/70 bg-transparent px-3 py-2 text-sm
                               outline-none focus:ring-2 focus:ring-primary dark:border-sidebar-border"
                        @keyup.enter="searchCity"
                    />
                    <button
                        :disabled="searchLoading"
                        class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-primary-foreground
                               transition-opacity hover:opacity-90 disabled:opacity-50"
                        @click="searchCity"
                    >
                        {{ searchLoading ? 'Otsib…' : 'Otsi' }}
                    </button>
                    <button
                        v-if="searchResult"
                        class="rounded-lg border border-sidebar-border/70 px-3 py-2 text-sm
                               transition-colors hover:bg-muted dark:border-sidebar-border"
                        @click="clearSearch"
                    >
                        ✕
                    </button>
                </div>
                <p v-if="searchError" class="text-sm text-destructive">{{ searchError }}</p>
            </div>
 
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
 
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70
                            p-4 dark:border-sidebar-border flex flex-col justify-between">
                    <template v-if="displayedWeather">
                        <div>
                            <p class="text-xs uppercase tracking-widest opacity-50 mb-1">
                                {{ displayedWeather.name }}, {{ displayedWeather.sys.country }}
                            </p>
                            <h2 class="text-5xl font-bold leading-none">
                                {{ Math.round(displayedWeather.main.temp) }}°C
                            </h2>
                            <p class="mt-1 text-sm capitalize opacity-80">
                                {{ displayedWeather.weather[0].description }}
                            </p>
                            <p class="text-xs opacity-50 mt-0.5">
                                Tundub {{ Math.round(displayedWeather.main.feels_like) }}°C
                            </p>
                        </div>
                        <div class="flex gap-4 text-sm opacity-75">
                            <span>↑ {{ Math.round(displayedWeather.main.temp_max) }}°</span>
                            <span>↓ {{ Math.round(displayedWeather.main.temp_min) }}°</span>
                            <span>💧 {{ displayedWeather.main.humidity }}%</span>
                        </div>
                        <img
                            class="size-20 absolute top-2 right-2 drop-shadow"
                            :src="`https://openweathermap.org/img/wn/${displayedWeather.weather[0].icon}@2x.png`"
                            :alt="displayedWeather.weather[0].description"
                        />
                    </template>
                    <p v-else class="p-4 text-sm opacity-60">Ilmaandmed pole saadaval</p>
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                    <a href="/posts" class="w-full h-full flex flex-col items-center justify-center gap-1 rounded-xl text-black">
                        <h2 class="text-2xl font-bold ">BLOG</h2>
                        <p class="text-lg tracking-wide capitalize opacity-90">View all posts</p>
                    </a>
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                    <a href="/products" class="w-full h-full flex flex-col items-center justify-center gap-1 rounded-xl text-black">
                        <h2 class="text-2xl font-bold ">E-STORE</h2>
                        <p class="text-lg tracking-wide capitalize opacity-90">View all products</p>
                    </a>
                </div>

            </div>
 
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70
                        md:min-h-min dark:border-sidebar-border">
                <MapView />
            </div>
 
        </div>
    </AppLayout>
</template>