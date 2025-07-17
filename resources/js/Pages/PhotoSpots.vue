<script setup>
defineProps({
  spots: Array
})
</script>

<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Photo Spots</h1>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
      <div
        v-for="spot in spots"
        :key="spot.id"
        class="bg-white p-4 rounded shadow hover:shadow-lg transition"
      >
        <img
          v-if="spot.image_path"
          :src="`/${spot.image_path}`"
          alt="Spot Image"
          class="w-full h-48 object-cover rounded mb-2"
        />
        <h2 class="text-xl font-semibold">{{ spot.name }}</h2>
        <p class="text-sm text-gray-600">{{ spot.location }}</p>
        <p class="mt-2 text-gray-700">{{ spot.description }}</p>
      </div>
    </div>
  </div>
</template>
