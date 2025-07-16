<!-- resources/js/Components/NavLinks.vue -->
<template>
  <div :class="vertical ? 'flex flex-col space-y-2' : 'flex space-x-4'">
    <Link href="/dashboard" class="text-gray-700 hover:text-blue-600">Dashboard</Link>
    <Link href="/attraction" class="text-gray-700 hover:text-blue-600">Attractions</Link>
    <Link href="/attractionsmap/map" class="text-gray-700 hover:text-blue-600">Map</Link>
    <Link href="/profile" class="text-gray-700 hover:text-blue-600">Profile</Link>
    <Link href="/logout" method="post" as="button" class="text-red-600 hover:text-red-800">Logout</Link>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  vertical: {
    type: Boolean,
    default: false
  }
})
</script>
