<template>
  <AppLayout>
    <div class="bg-white shadow-md rounded-lg p-6">
      <h2 class="text-2xl font-bold mb-1 flex items-center">
        💬 Admin Chats
      </h2>
      <p class="text-gray-600 mb-4">View and manage all user conversations.</p>

      <div class="overflow-x-auto">
        <table class="min-w-full table-auto border-collapse">
          <thead class="bg-blue-100 text-left">
            <tr>
              <th class="py-3 px-4 font-semibold text-sm text-gray-700">NAME</th>
              <th class="py-3 px-4 font-semibold text-sm text-gray-700">EMAIL</th>
              <th class="py-3 px-4 font-semibold text-sm text-gray-700">LAST MESSAGE</th>
              <th class="py-3 px-4 font-semibold text-sm text-gray-700">CHAT</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="user in users"
              :key="user.id"
              :class="user.hasUnread ? 'bg-yellow-50' : ''"
              class="border-t"
            >
              <!-- Name -->
              <td class="py-3 px-4 text-sm font-medium text-gray-800 flex items-center">
                {{ user.name }}
                <span
                  v-if="user.hasUnread"
                  class="ml-2 bg-red-500 text-white text-xs px-2 py-0.5 rounded-full"
                >
                  New
                </span>
              </td>

              <!-- Email -->
              <td class="py-3 px-4 text-sm text-gray-600">{{ user.email }}</td>

              <!-- Last Message Time -->
              <td class="py-3 px-4 text-sm">
                <span
                  :class="isToday(user.latestMessage?.created_at) ? 'text-blue-600 font-semibold' : 'text-gray-500'"
                >
                  {{ formatDateTime(user.latestMessage?.created_at) }}
                </span>
                <span
                  v-if="isToday(user.latestMessage?.created_at)"
                  class="ml-2 bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full"
                >
                  Today
                </span>
              </td>

              <!-- Action -->
              <td class="py-3 px-4">
                <Link
                  :href="`/admin/chats/${user.id}`"
                  class="bg-blue-500 hover:bg-blue-600 text-white text-sm px-3 py-1 rounded shadow"
                >
                  View Chat
                </Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

defineProps({
  users: Array
})

// Format datetime nicely
function formatDateTime(datetime) {
  if (!datetime) return '—'
  return new Date(datetime).toLocaleString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Check if date is today
function isToday(datetime) {
  if (!datetime) return false
  const date = new Date(datetime)
  const today = new Date()

  return (
    date.getDate() === today.getDate() &&
    date.getMonth() === today.getMonth() &&
    date.getFullYear() === today.getFullYear()
  )
}
</script>
