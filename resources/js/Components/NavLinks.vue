<!-- resources/js/Components/NavLinks.vue -->
<template>
  <div :class="vertical ? 'flex flex-col space-y-2' : 'flex space-x-4'">
    <Link href="/dashboard" class="text-gray-700 hover:text-blue-600">Dashboard</Link>
    <Link href="/tourplanner" class="text-gray-700 hover:text-blue-600">AI Tour Planner</Link>
    <Link href="/attraction" class="text-gray-700 hover:text-blue-600">Attractions</Link>
    <Link href="/attractionsmap/map" class="text-gray-700 hover:text-blue-600">Map</Link>
    <Link v-if="user?.user_type_id === 1" href="/users" class="text-gray-700 hover:text-blue-600">Users</Link>
    
    <Link href="/profile" class="text-gray-700 hover:text-blue-600">Profile</Link>
    <Link href="/logout" method="post" as="button" class="text-red-600 hover:text-red-800">Logout</Link>
 <Link v-if="user?.user_type_id === 1" href="/admin/chats" class="flex items-center text-gray-700 hover:text-blue-600">
  <ChatBubbleLeftRightIcon class="w-6 h-6 mr-1" />
</Link>

  </div>
</template>

<script setup>
import { usePage, Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import { ChatBubbleLeftRightIcon } from '@heroicons/vue/24/solid' // ✅ Import chat icon

const page = usePage()

defineProps({
  vertical: {
    type: Boolean,
    default: false
  }
})

const user = computed(() => page.props.auth.user)
</script>
