<template>
    <AppLayout>
  <div class="w-full h-screen">
    <div id="map" class="w-full h-full"></div>
  </div>
  </AppLayout>
</template>

<script setup>
import { onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'
import L from 'leaflet'
import 'leaflet.markercluster'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  locations: Array
})

onMounted(() => {
  const map = L.map('map').setView([7.8731, 80.7718], 7) // Sri Lanka center

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution:
      '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
  }).addTo(map)

  const markers = L.markerClusterGroup()

  props.locations.forEach((place) => {
    if (place.latitude && place.longitude) {
      const marker = L.marker([place.latitude, place.longitude])
        .bindPopup(`
          <strong>${place.name}</strong><br>
          📍 District: ${place.city || place.district}<br>
          ⭐ Rating: ${place.rating}<br>
        `)
      markers.addLayer(marker)
    }
  })

  map.addLayer(markers)
})
</script>

<style scoped>
#map {
  height: 100vh;
}
</style>
