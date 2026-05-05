<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { onMounted, onBeforeUnmount, ref, watch } from 'vue';
import { X, Check } from 'lucide-vue-next';

type Marker = {
    id: number;
    name: string;
    lat: number;
    lng: number;
    description: string | null;
    added: string | null;
    edited: string | null;
};

const props = defineProps<{ markers: Marker[] }>();

const markers = ref<Marker[]>([...props.markers]);

type Modal = { mode: 'add'; lat: number; lng: number }
           | { mode: 'edit'; marker: Marker }
           | null;

const modal   = ref<Modal>(null);
const mapEl   = ref<HTMLElement | null>(null);

const form = ref({ name: '', lat: 0, lng: 0, description: '' });

function openAdd(lat: number, lng: number) {
    form.value = { name: '', lat, lng, description: '' };
    modal.value = { mode: 'add', lat, lng };
}

function openEdit(marker: Marker) {
    form.value = { name: marker.name, lat: marker.lat, lng: marker.lng, description: marker.description ?? '' };
    modal.value = { mode: 'edit', marker };
}

function closeModal() { modal.value = null; }

function submit() {
    if (!form.value.name.trim()) return;
    if (modal.value?.mode === 'add') {
        router.post('/markers', form.value, {
            preserveScroll: true,
            onSuccess: (page) => {
                closeModal();
                markers.value = page.props.markers;
                renderMarkers();
            },
        });
    }
    if (modal.value?.mode === 'edit') {
        router.put(`/markers/${modal.value.marker.id}`, form.value, {
            preserveScroll: true,
            onSuccess: (page) => {
                closeModal();
                markers.value = page.props.markers;
                renderMarkers();
            },
        });
    }
}

function destroy(id: number) {
    if (!confirm('Delete this marker?')) return;

    router.delete(`/markers/${id}`, {
        preserveScroll: true,
        onSuccess: (page) => {
            markers.value = page.props.markers;
            renderMarkers();
        },
    });
}

let L: typeof import('leaflet') | null = null;
let map: import('leaflet').Map | null = null;
const leafletMarkers = new Map<number, import('leaflet').Marker>();

async function initMap() {
    if (!mapEl.value) return;
    L = (await import('leaflet')).default;

    delete (L.Icon.Default.prototype as any)._getIconUrl;
    L.Icon.Default.mergeOptions({
        iconUrl:       'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
        iconRetinaUrl:'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
        shadowUrl:     'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
    });

    map = L.map(mapEl.value, { zoomControl: true }).setView([58.5953, 25.0136], 7);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
    }).addTo(map);

    map.on('click', (e: import('leaflet').LeafletMouseEvent) => {
        openAdd(e.latlng.lat, e.latlng.lng);
    });

    renderMarkers();
}

function renderMarkers() {
    if (!L || !map) return;

    for (const lm of leafletMarkers.values()) {
        lm.remove();
    }
    leafletMarkers.clear();

    for (const m of markers.value) {
        const lm = L!.marker([m.lat, m.lng])
            .addTo(map!)
            .bindPopup(buildPopup(m), { minWidth: 200 });

        lm.on('popupopen', () => {
            setTimeout(() => {
                document
                    .getElementById(`edit-${m.id}`)
                    ?.addEventListener('click', () => {
                        lm.closePopup();
                        openEdit(m);
                    });
                document
                    .getElementById(`del-${m.id}`)
                    ?.addEventListener('click', () => {
                        lm.closePopup();
                        destroy(m.id);
                    });
            }, 50);
        });
        leafletMarkers.set(m.id, lm);
    }
}

function buildPopup(m: Marker): string {
    return `
        <div style="font-family:inherit;min-width:180px">
            <p style="font-weight:700;font-size:14px;margin:0 0 4px">${m.name}</p>
            ${m.description ? `<p style="font-size:12px;color:#6b7280;margin:0 0 8px">${m.description}</p>` : ''}
            <p style="font-size:11px;color:#9ca3af;margin:0 0 10px">${Number(m.lat).toFixed(5)}, ${Number(m.lng).toFixed(5)}</p>
            <div style="display:flex;gap:6px">
                <button id="edit-${m.id}" style="flex:1;padding:4px 8px;background:#6366f1;color:#fff;border:none;border-radius:6px;font-size:12px;cursor:pointer">Edit</button>
                <button id="del-${m.id}"  style="flex:1;padding:4px 8px;background:#ef4444;color:#fff;border:none;border-radius:6px;font-size:12px;cursor:pointer">Delete</button>
            </div>
        </div>`;
}

watch(
    () => props.markers,
    (newMarkers) => {
        markers.value = [...newMarkers];
        renderMarkers();
    },
    { deep: true }
);

onMounted(async () => {
    if (!document.getElementById('leaflet-css')) {
        const link = document.createElement('link');
        link.id   = 'leaflet-css';
        link.rel  = 'stylesheet';
        link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
        document.head.appendChild(link);
    }
    await initMap();
});

onBeforeUnmount(() => { map?.remove(); });
</script>

<template>
    <div class="relative w-full h-full">
        <div ref="mapEl" class="w-full h-full rounded-xl z-0" />

        <div class="absolute bottom-3 right-3 z-[500] bg-background/90 backdrop-blur-sm border border-border rounded-lg px-3 py-1.5 text-xs text-muted-foreground shadow-sm pointer-events-none">
            Click map to add a marker
        </div>
    </div>
    <Teleport to="body">
        <Transition name="fade">
            <div v-if="modal" class="fixed inset-0 z-[9999] flex items-center justify-center p-4" @click.self="closeModal">
                <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" @click="closeModal" />
                <div class="relative z-10 bg-background border border-border rounded-2xl shadow-lg w-full max-w-sm p-6 flex flex-col gap-4">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-foreground text-base">
                            {{ modal.mode === 'add' ? 'Add Marker' : 'Edit Marker' }}
                        </h3>
                        <button @click="closeModal" class="text-muted-foreground hover:text-foreground transition-colors">
                            <X :size="18" />
                        </button>
                    </div>
                    <p class="text-xs text-muted-foreground -mt-2">
                        {{ Number(form.lat).toFixed(5) }}, {{ Number(form.lng).toFixed(5) }}
                    </p>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-semibold text-foreground">Name <span class="text-red-500">*</span></label>
                        <input
                            v-model="form.name"
                            placeholder="e.g. Tallinn Old Town"
                            class="border border-border rounded-lg px-3 py-2 text-sm bg-muted placeholder:text-muted-foreground outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                            @keyup.enter="submit"
                        />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-semibold text-foreground">Description</label>
                        <textarea
                            v-model="form.description"
                            placeholder="Optional note..."
                            rows="3"
                            class="border border-border rounded-lg px-3 py-2 text-sm bg-muted placeholder:text-muted-foreground outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition resize-none"
                        />
                    </div>
                    <div class="flex gap-3 pt-1">
                        <button @click="closeModal" class="flex-1 px-4 py-2 border border-border rounded-lg text-sm font-medium hover:bg-muted transition-colors">
                            Cancel
                        </button>
                        <button
                            @click="submit"
                            :disabled="!form.name.trim()"
                            class="flex-1 flex items-center justify-center gap-2 bg-primary text-primary-foreground px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors disabled:opacity-50"
                        >
                            <Check :size="15" />
                            {{ modal.mode === 'add' ? 'Add' : 'Save' }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 150ms ease; }
.fade-enter-from, .fade-leave-to       { opacity: 0; }
</style>