<script setup>
import { ref, onMounted } from "vue"
import L from "leaflet"
import "leaflet/dist/leaflet.css"

const mapEl = ref(null)

onMounted(() => {
  const test = () => console.log('test');

  const map = L.map(mapEl.value, {
    zoomControl: true
  }).setView([59.4, 24.7], 8)

  L.tileLayer(
    "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
  ).addTo(map)

  map.on('click', test);
})

</script>

<template>
  <div ref="mapEl" class="map h-full"></div>
</template>