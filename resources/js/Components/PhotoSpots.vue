<template>
  <div class="p-6">
    <h1 class="text-3xl font-bold mb-2">Sri Lankan Photo Spots</h1>
    <p class="text-gray-600 mb-6">Capture the beauty of the Pearl of the Indian Ocean</p>

    <div class="grid md:grid-cols-2 gap-6">
      <div
        v-for="spot in spots"
        :key="spot.id"
        class="bg-white shadow rounded-xl overflow-hidden"
      >
        <!-- Image placeholder -->
        <div class="relative bg-gray-100 h-48 flex items-center justify-center">
          <span class="text-gray-400 text-xl">📷 Photo Spot</span>
          <div
            class="absolute top-2 left-2 bg-white text-xs px-2 py-1 rounded-full shadow"
          >
            Best: {{ spot.best_time }}
          </div>
          <div
            class="absolute top-2 right-2 text-white text-xs font-bold px-2 py-1 rounded-full"
            :class="spot.difficulty === 'Hard' ? 'bg-red-500' : spot.difficulty === 'Easy' ? 'bg-green-500' : 'bg-yellow-500'"
          >
            {{ spot.difficulty }}
          </div>
        </div>

        <div class="p-4">
          <div class="flex justify-between items-center mb-1">
            <h2 class="text-lg font-semibold">{{ spot.name }}</h2>
            <div class="text-yellow-500 font-semibold text-sm">★ {{ spot.rating }}</div>
          </div>
          <div class="text-sm text-gray-500 mb-2">
            📍 {{ spot.location }}, {{ spot.province }}
          </div>
          <p class="text-sm text-gray-700 mb-3">{{ spot.description }}</p>

          <div class="flex flex-wrap gap-2 mb-3">
            <span
              v-for="tag in spot.tags"
              :key="tag"
              class="bg-gray-200 text-xs px-2 py-1 rounded-full"
            >
              {{ tag }}
            </span>
          </div>

          <div class="flex justify-between items-center">
            <div class="flex items-center gap-3 text-sm text-gray-600">
              ❤️ {{ spot.likes }}
              <button>🔗 Share</button>
            </div>
            <button class="bg-blue-600 text-white px-3 py-1 rounded">Get Directions</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const spots = ref([])

onMounted(async () => {
  const response = await axios.get('/api/photo-spots')
  spots.value = response.data
})
</script>
