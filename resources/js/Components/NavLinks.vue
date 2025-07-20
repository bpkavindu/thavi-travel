<template>
  <div :class="vertical ? 'flex flex-col space-y-2' : 'flex space-x-4'">
    <Link href="/dashboard" :class="linkClass('/dashboard')">Dashboard</Link>

    <Link href="/tourplanner" :class="linkClass('/tourplanner')">AI Tour Planner</Link>

    <Link href="/attraction" :class="linkClass('/attraction')">Attractions</Link>

    <Link href="/attractionsmap/map" :class="linkClass('/attractionsmap/map')">Map</Link>

    <Link href="/photo-spots" :class="linkClass('/photo-spots')">Photo Spots</Link>

    <Link href="/guides" :class="linkClass('/guides')">Tours & Tickets</Link>

    <Link v-if="user?.user_type_id === 1" href="/users" :class="linkClass('/users')">Users</Link>

    <Link href="/profile" :class="linkClass('/profile')">Profile</Link>

    <Link href="/logout" method="post" as="button" class="text-red-600 hover:text-red-800">Logout</Link>

    <Link v-if="user?.user_type_id === 1" href="/admin/chats" :class="linkClass('/admin/chats')"
      class="flex items-center">
    <ChatBubbleLeftRightIcon class="w-6 h-6 mr-1" />
    </Link>
  </div>
</template>

<script setup>
import { usePage, Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import { ChatBubbleLeftRightIcon } from '@heroicons/vue/24/solid'

const page = usePage()
const user = computed(() => page.props.auth.user)

// Get current path
const currentPath = computed(() => page.url)

// Reusable class logic for links
const linkClass = (path) => {
  return currentPath.value.startsWith(path)
    ? 'text-blue-700 font-semibold'
    : 'text-gray-700 hover:text-blue-600'
}
</script>
