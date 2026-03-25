<script setup lang="ts">
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle,} from '@/components/ui/dialog';
import { Input }    from '@/components/ui/input';
import { Label }    from '@/components/ui/label';
import { Button }   from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';
 
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import axios from 'axios';
import { onMounted, ref } from 'vue';
import type { MarkerData } from '@/types';
 
import markerIcon2x   from 'leaflet/dist/images/marker-icon-2x.png';
import markerIcon     from 'leaflet/dist/images/marker-icon.png';
import markerShadow   from 'leaflet/dist/images/marker-shadow.png';
 
delete (L.Icon.Default.prototype as any)._getIconUrl;
L.Icon.Default.mergeOptions({
    iconUrl:       markerIcon,
    iconRetinaUrl: markerIcon2x,
    shadowUrl:     markerShadow,
});
 
const mapEl          = ref<HTMLElement | null>(null);
const selectedLocation = ref<{ lat: number; lng: number } | null>(null);
const formName       = ref('');
const formDescription = ref('');
const saving         = ref(false);
const errorMsg       = ref('');
 
let leafletMap: L.Map | null = null;
const leafletMarkers = new Map<number, L.Marker>();
 
function addMarkerToMap(m: MarkerData) {
    if (!leafletMap || m.id === undefined) return;
 
    const lm = L.marker([m.latitude, m.longitude])
        .addTo(leafletMap)
        .bindPopup(`
            <strong>${m.name}</strong>
            ${m.description ? `<br><span style="font-size:0.85em">${m.description}</span>` : ''}
            <br>
            <button
                onclick="window.dispatchEvent(new CustomEvent('delete-marker', { detail: ${m.id} }))"
                style="margin-top:6px;color:red;font-size:0.8em;cursor:pointer;background:none;border:none;padding:0"
            >🗑 Kustuta</button>
        `);
    leafletMarkers.set(m.id, lm);
}
 
onMounted(async () => {
    leafletMap = L.map(mapEl.value!, { zoomControl: true }).setView([59.4, 24.7], 8);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
    }).addTo(leafletMap);
 
    leafletMap.on('click', (e: L.LeafletMouseEvent) => {
        selectedLocation.value = { lat: e.latlng.lat, lng: e.latlng.lng };
        formName.value        = '';
        formDescription.value = '';
        errorMsg.value        = '';
    });
 
const loadingMarkers = ref(true);
const loadError = ref('');

try {
    const { data } = await axios.get('/markers');
    data.forEach(addMarkerToMap);
} catch {
    loadError.value = 'Markerite laadimine ebaõnnestus';
} finally {
    loadingMarkers.value = false;
}
 
    window.addEventListener('delete-marker', async (e: Event) => {
        const id = (e as CustomEvent<number>).detail;
        try {
            await axios.delete(`/markers/${id}`);
            const lm = leafletMarkers.get(id);
            if (lm && leafletMap) {
                leafletMap.removeLayer(lm);
                leafletMarkers.delete(id);
            }
        } catch {
            console.error('Failed to delete marker', id);
        }
    });
});
 
async function saveMarker() {
    if (!formName.value.trim()) {
        errorMsg.value = 'Nimi on kohustuslik.';
        return;
    }
    if (!selectedLocation.value) return;
 
    saving.value   = true;
    errorMsg.value = '';
 
    try {
        const payload: Omit<MarkerData, 'id'> = {
            name:        formName.value.trim(),
            latitude:    selectedLocation.value.lat,
            longitude:   selectedLocation.value.lng,
            description: formDescription.value.trim(),
        };
 
        const { data } = await axios.post<MarkerData>('/markers', payload);
        addMarkerToMap(data);
        selectedLocation.value = null;
    } catch {
        errorMsg.value = 'Salvestamine ebaõnnestus. Proovi uuesti.';
    } finally {
        saving.value = false;
    }
}
 
function closeDialog() {
    selectedLocation.value = null;
    errorMsg.value         = '';
}
</script>
 
<template>
    <div ref="mapEl" class="map z-10 h-full min-h-[400px]" />
 
    <Dialog :open="!!selectedLocation" @update:open="closeDialog">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Salvesta uus marker</DialogTitle>
                <DialogDescription>Lisa nimi ja kirjeldus kaardi punktile.</DialogDescription>
            </DialogHeader>
 
            <div class="grid grid-cols-2 gap-4 mt-2">
                <div class="col-span-2">
                    <Label class="mb-1.5" for="marker-name">Nimi *</Label>
                    <Input
                        id="marker-name"
                        v-model="formName"
                        placeholder="Markeri nimi"
                    />
                </div>
 
                <div>
                    <Label class="mb-1.5" for="marker-lat">Laius (lat)</Label>
                    <Input
                        id="marker-lat"
                        :model-value="selectedLocation?.lat?.toFixed(6) ?? ''"
                        disabled
                    />
                </div>
                <div>
                    <Label class="mb-1.5" for="marker-lng">Pikkus (lng)</Label>
                    <Input
                        id="marker-lng"
                        :model-value="selectedLocation?.lng?.toFixed(6) ?? ''"
                        disabled
                    />
                </div>
 
                <div class="col-span-2">
                    <Label class="mb-1.5" for="marker-desc">Kirjeldus</Label>
                    <Textarea
                        id="marker-desc"
                        v-model="formDescription"
                        placeholder="Valikuline kirjeldus…"
                        class="resize-none"
                        rows="3"
                    />
                </div>
 
                <p v-if="errorMsg" class="col-span-2 text-sm text-destructive -mt-1">
                    {{ errorMsg }}
                </p>
 
                <div class="col-span-2 flex justify-end gap-2">
                    <Button variant="outline" @click="closeDialog">Tühista</Button>
                    <Button :disabled="saving" @click="saveMarker">
                        {{ saving ? 'Salvestab…' : 'Salvesta' }}
                    </Button>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>