<template>
  <div class="p-6 max-w-7xl mx-auto font-sans bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="text-center mb-6">
      <h1 class="text-4xl font-bold text-gray-800">Sri Lankan Attractions</h1>
      <p class="text-gray-500 mt-1">Discover the pearl of the Indian Ocean's hidden gems</p>

      <!-- Controls -->
      <div class="mt-4 flex justify-center gap-3 flex-wrap">
        <button class="bg-white border px-4 py-2 rounded-lg shadow-sm hover:bg-gray-100" @click="showLocationSelector = true">
          📍 Update Location
        </button>
        <button class="bg-pink-500 text-white px-4 py-2 rounded-lg shadow hover:bg-pink-600" @click="getLocation" :disabled="loadingLocation">
          🎲 Get Current Location!
          <span v-if="loadingLocation" class="ml-2 animate-spin">⏳</span>
        </button>
        <button class="bg-green-600 text-white px-4 py-2 rounded-lg shadow hover:bg-green-700" @click="showAddForm = true">
          ➕ Add Attraction
        </button>
      </div>

      <!-- Location Info -->
      <div class="mt-3 text-sm text-gray-600">
        <span class="inline-flex items-center gap-1">
          <span class="text-blue-500">📌</span>
          {{ selectedCity }}, {{ selectedCountry }}
          • Showing attractions near you
        </span>
      </div>
    </div>

    <!-- Category Filters -->
    <div class="flex justify-center gap-2 mb-6 flex-wrap">
      <button v-for="cat in categories" :key="cat.name" @click="selectedCategory = cat.name"
              :class="[
                'px-4 py-2 rounded-full font-medium text-sm flex items-center gap-1',
                selectedCategory === cat.name ? cat.activeClass : 'bg-white border text-gray-600 hover:bg-gray-100'
              ]">
        <span v-html="cat.icon" /> {{ cat.name }}
      </button>
    </div>

    <!-- Attraction Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
      <div v-for="place in filteredAttractions" :key="place.name" class="bg-white rounded-xl shadow p-4 relative">
        <div class="absolute top-2 right-2 bg-white border text-xs px-2 py-1 rounded shadow-sm">{{ place.distance }} km</div>
        <div class="h-36 bg-gray-100 mb-4 rounded overflow-hidden">
          <img v-if="place.image" :src="place.image" alt="Attraction Image" class="w-full h-full object-cover" />
          <div v-else class="flex items-center justify-center h-full text-gray-500 text-sm">Image not available</div>
        </div>
        <h2 class="text-lg font-semibold text-gray-800 mb-1">{{ place.name }}</h2>
        <div class="text-yellow-500 flex items-center text-sm mb-1">
          <span>⭐ {{ place.rating }}</span>
          <span class="text-gray-500 ml-1">rating</span>
        </div>
        <div class="text-sm text-gray-600">
          <span class="bg-gray-100 px-2 py-1 rounded">{{ place.price }}</span>
        </div>
        <!-- Get Directions Button -->
        <button
          class="mt-2 text-blue-600 text-sm underline hover:text-blue-800"
          @click="openDirections(place)">
          📍 Get Directions
        </button>
      </div>
    </div>

    <!-- Location Modal -->
    <div v-if="showLocationSelector" class="fixed inset-0 bg-black bg-opacity-30 flex justify-center items-center z-50">
      <div class="bg-white p-6 rounded shadow-lg w-80">
        <h2 class="text-lg font-semibold mb-4">Select Location</h2>
        <select v-model="selectedCountry" class="w-full border px-3 py-2 rounded mb-3">
          <option v-for="c in countries" :key="c.name" :value="c.name">{{ c.name }}</option>
        </select>
        <select v-model="selectedCity" class="w-full border px-3 py-2 rounded mb-4">
          <option v-for="city in countries.find(c => c.name === selectedCountry)?.cities" :key="city" :value="city">
            {{ city }}
          </option>
        </select>
        <div class="flex justify-end gap-2">
          <button class="text-sm px-3 py-1 border rounded" @click="showLocationSelector = false">Cancel</button>
          <button class="bg-blue-600 text-white text-sm px-3 py-1 rounded" @click="showLocationSelector = false">Save</button>
        </div>
      </div>
    </div>

    <!-- Add Attraction Modal -->
    <div v-if="showAddForm" class="fixed inset-0 bg-black bg-opacity-30 flex justify-center items-center z-50">
      <div class="bg-white p-6 rounded shadow-lg w-[90%] max-w-md">
        <h2 class="text-lg font-semibold mb-4">Add New Attraction</h2>
        <div class="space-y-3">
          <input v-model="newAttraction.name" placeholder="Name" class="w-full border px-3 py-2 rounded" />
          <input v-model.number="newAttraction.rating" type="number"  step="0.01" min="0" max="5" placeholder="Rating" class="w-full border px-3 py-2 rounded" />
          <select v-model="newAttraction.price" class="w-full border px-3 py-2 rounded">
            <option>$</option>
            <option>$$</option>
            <option>$$$</option>
          </select>
          <select v-model="newAttraction.category" class="w-full border px-3 py-2 rounded">
            <option v-for="cat in categories" :key="cat.name" :value="cat.name">{{ cat.name }}</option>
          </select>
          <input v-model.number="newAttraction.distance" type="number" step="0.1" placeholder="Distance (km)" class="w-full border px-3 py-2 rounded" />
          <select v-model="newAttraction.country" class="w-full border px-3 py-2 rounded">
            <option v-for="c in countries" :key="c.name" :value="c.name">{{ c.name }}</option>
          </select>
          <select v-model="newAttraction.city" class="w-full border px-3 py-2 rounded">
            <option v-for="city in countries.find(c => c.name === newAttraction.country)?.cities" :key="city" :value="city">
              {{ city }}
            </option>
          </select>
          <input type="file" accept="image/*" @change="handleImageUpload" class="w-full border px-3 py-2 rounded" />
          <div v-if="newAttraction.image" class="mt-2">
            <img :src="newAttraction.image" alt="Preview" class="rounded w-full h-40 object-cover" />
          </div>
        </div>
        <div class="flex justify-end gap-2 mt-4">
          <button class="text-sm px-3 py-1 border rounded" @click="showAddForm = false">Cancel</button>
          <button class="bg-blue-600 text-white text-sm px-3 py-1 rounded" @click="addAttraction">Add</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

const props = defineProps({
  attractions: Array
})

// Categories
const categories = [
  { name: 'All', activeClass: 'bg-blue-600 text-black', icon: '🏠' },
  { name: 'Cultural', activeClass: 'bg-indigo-600 text-black', icon: '🏛️' },
  { name: 'Adventure', activeClass: 'bg-yellow-500 text-black', icon: '⛰️' },
  { name: 'Food', activeClass: 'bg-red-500 text-black', icon: '🍽️' },
  { name: 'Religious', activeClass: 'bg-green-600 text-black', icon: '🛕' },
  { name: 'Nature', activeClass: 'bg-emerald-500 text-black', icon: '🌿' },
]

// Refs
const selectedCategory = ref('All')
const selectedCountry = ref('Sri Lanka')
const selectedCity = ref('Colombo')
const showLocationSelector = ref(false)
const showAddForm = ref(false)
const province = ref(null)
const loadingLocation = ref(false)

// Countries and cities
const countries = ref([
  { name: 'Sri Lanka', cities: ['Colombo', 'Kandy', 'Galle'] },
  { name: 'India', cities: ['Delhi', 'Mumbai', 'Chennai'] },
  { name: 'Thailand', cities: ['Bangkok', 'Phuket', 'Chiang Mai'] },
])

// Attractions
const attractions = ref([...props.attractions])

// New attraction form
const newAttraction = ref({
  name: '',
  rating: null,
  price: '$',
  category: 'Adventure',
  distance: null,
  country: selectedCountry.value,
  city: selectedCity.value,
  image: null,
})

// Upload image preview
function handleImageUpload(event) {
  const file = event.target.files[0]
  if (file) {
    const reader = new FileReader()
    reader.onload = e => {
      newAttraction.value.image = e.target.result
    }
    reader.readAsDataURL(file)
  }
}

function addAttraction() {
  router.post(route('attraction.store'), {
    name: newAttraction.value.name,
    rating: newAttraction.value.rating,
    price: newAttraction.value.price,
    category: newAttraction.value.category,
    distance: newAttraction.value.distance,
    country: newAttraction.value.country,
    city: newAttraction.value.city,
    image: newAttraction.value.image,
  }, {
    onSuccess: () => {
      newAttraction.value = {
        name: '',
        rating: null,
        price: '$',
        category: 'Adventure',
        distance: null,
        country: selectedCountry.value,
        city: selectedCity.value,
        image: null,
      }
      showAddForm.value = false
    },
    onError: (errors) => {
      alert('Validation error: ' + JSON.stringify(errors))
    }
  })
}

// Filtered attraction list
const filteredAttractions = computed(() => {
  return attractions.value.filter(p =>
    (selectedCategory.value === 'All' || p.category === selectedCategory.value) &&
    p.country === selectedCountry.value &&
    p.city === selectedCity.value
  )
})

// Province from lat/lng
async function getProvinceFromCoords(lat, lng) {
  const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=10&addressdetails=1`
  try {
    const res = await fetch(url, {
      headers: {
        'Accept-Language': 'en',
        'User-Agent': 'YourAppName/1.0',
      },
    })
    const data = await res.json()
    return {
      province: data.address.state || data.address.region || null,
      city: data.address.city || data.address.town || data.address.village || null
    }
  } catch (error) {
    console.error('Reverse geocoding error:', error)
    return { province: null, city: null }
  }
}

// Smart city finder
function findCityByProvince(prov, countryName) {
  if (!prov || !countryName) return null

  const provLower = prov.toLowerCase()
  const cities = countries.value.find(c => c.name === countryName)?.cities || []

  // First: match province to city name
  for (const city of cities) {
    if (provLower.includes(city.toLowerCase())) {
      return city
    }
  }

  // Fallback: use first available city
  return cities[0] || null
}

// Geolocation-based update
async function getLocation() {
  if (!navigator.geolocation) {
    alert('Geolocation is not supported by your browser')
    return
  }

  loadingLocation.value = true

  navigator.geolocation.getCurrentPosition(
    async pos => {
      const lat = pos.coords.latitude
      const lng = pos.coords.longitude
      const result = await getProvinceFromCoords(lat, lng)

      province.value = result.province
      selectedCountry.value = 'Sri Lanka'

      const availableCities = countries.value.find(c => c.name === selectedCountry.value)?.cities || []

      if (result.city && availableCities.includes(result.city)) {
        selectedCity.value = result.city
      } else {
        const fallbackCity = findCityByProvince(result.province, selectedCountry.value)
        if (fallbackCity && availableCities.includes(fallbackCity)) {
          selectedCity.value = fallbackCity
        }
      }

      loadingLocation.value = false
    },
    err => {
      alert('Failed to get location')
      console.error(err)
      loadingLocation.value = false
    }
  )
}

// Open Google Maps directions from current location to attraction
function openDirections(place) {
  if (!navigator.geolocation) {
    alert("Geolocation not supported");
    return;
  }

  navigator.geolocation.getCurrentPosition(
    pos => {
      const userLat = pos.coords.latitude;
      const userLng = pos.coords.longitude;

      // Use place name + city + country as destination
      const destination = encodeURIComponent(`${place.name}, ${place.city}, ${place.country}`);

      // Construct Google Maps directions URL
      const mapsUrl = `https://www.google.com/maps/dir/?api=1&origin=${userLat},${userLng}&destination=${destination}`;

      window.open(mapsUrl, '_blank');
    },
    err => {
      alert('Unable to retrieve location');
      console.error(err);
    }
  );
}
</script>
