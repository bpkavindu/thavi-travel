<template>
  <div class="p-8 bg-white min-h-screen">
    <h1 class="text-4xl font-bold text-center mb-2">Sri Lankan Photo Spots</h1>
    <p class="text-center text-gray-500 mb-10">Capture the beauty of the Pearl of the Indian Ocean</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
      <!-- Sigiriya Card -->
      <div class="border rounded-xl shadow-md p-4 relative">
        <div class="absolute top-2 left-2 bg-gray-800 text-white px-2 py-1 text-xs rounded">📸 Photo Spot</div>
        <div class="absolute top-2 right-2 bg-red-600 text-white px-2 py-1 text-xs rounded">Hard</div>
        <div class="text-sm text-gray-600 mt-20">Best: 6:00 AM - 8:00 AM</div>
        <h2 class="text-xl font-semibold mt-2">Sigiriya Lion Rock</h2>
        <p class="text-gray-500">📍 Dambulla, Central Province</p>
        <p class="mt-2 text-gray-700">Ancient fortress with breathtaking sunrise views</p>
        <div class="flex flex-wrap gap-2 mt-3 text-xs">
          <span class="bg-gray-200 px-2 py-1 rounded">Sunrise</span>
          <span class="bg-gray-200 px-2 py-1 rounded">Ancient</span>
          <span class="bg-gray-200 px-2 py-1 rounded">UNESCO</span>
        </div>
        <div class="flex items-center justify-between mt-4">
          <div class="flex items-center gap-2">
            <span>❤️ 3250</span>
            <button class="text-blue-500">🔗 Share</button>
          </div>
          <div class="flex items-center gap-2">
            <span class="text-yellow-500">⭐ 4.9</span>
            <button class="bg-blue-600 text-white px-4 py-1 rounded">Get Directions</button>
          </div>
        </div>
      </div>

      <!-- Nine Arch Bridge Card -->
      <div class="border rounded-xl shadow-md p-4 relative">
        <div class="absolute top-2 left-2 bg-gray-800 text-white px-2 py-1 text-xs rounded">📸 Photo Spot</div>
        <div class="absolute top-2 right-2 bg-green-600 text-white px-2 py-1 text-xs rounded">Easy</div>
        <div class="text-sm text-gray-600 mt-20">Best: 10:30 AM - 11:30 AM</div>
        <h2 class="text-xl font-semibold mt-2">Nine Arch Bridge</h2>
        <p class="text-gray-500">📍 Ella, Uva Province</p>
        <p class="mt-2 text-gray-700">Iconic railway bridge through emerald tea plantations</p>
        <div class="flex flex-wrap gap-2 mt-3 text-xs">
          <span class="bg-gray-200 px-2 py-1 rounded">Train</span>
          <span class="bg-gray-200 px-2 py-1 rounded">Tea Country</span>
          <span class="bg-gray-200 px-2 py-1 rounded">Architecture</span>
        </div>
        <div class="flex items-center justify-between mt-4">
          <div class="flex items-center gap-2">
            <span>❤️ 2890</span>
            <button class="text-blue-500">🔗 Share</button>
          </div>
          <div class="flex items-center gap-2">
            <span class="text-yellow-500">⭐ 4.8</span>
            <button class="bg-blue-600 text-white px-4 py-1 rounded">Get Directions</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'SriLankanPhotoSpots'
}
</script>

<style scoped>
/* Optional: customize further */
</style>
