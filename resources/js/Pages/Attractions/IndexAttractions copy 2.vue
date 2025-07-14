<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const latitude = ref(null)
const longitude = ref(null)
const province = ref(null)

const getProvinceFromCoords = async (lat, lng) => {
  const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=10&addressdetails=1`

  try {
    const res = await fetch(url, {
      headers: {
        'Accept-Language': 'en', // get result in English
        'User-Agent': 'YourAppName/1.0' // Required by Nominatim usage policy
      }
    })
    const data = await res.json()

    // province can be under 'state' or 'region' in the address object
    return data.address.state || data.address.region || null
  } catch (error) {
    console.error('Reverse geocoding error:', error)
    return null
  }
}

const getLocation = () => {
  if (!navigator.geolocation) {
    alert('Geolocation is not supported by your browser')
    return
  }

  navigator.geolocation.getCurrentPosition(async (pos) => {
    latitude.value = pos.coords.latitude
    longitude.value = pos.coords.longitude

    province.value = await getProvinceFromCoords(latitude.value, longitude.value)

    // Send data to backend if needed
    router.post('/location/store', {
      latitude: latitude.value,
      longitude: longitude.value,
      province: province.value,
    })
  }, (err) => {
    alert('Failed to get location')
    console.error(err)
  })
}
</script>

<template>
  <button @click="getLocation">Get Location & Province</button>

  <div v-if="province">
    Province: {{ province }}
  </div>
</template>
