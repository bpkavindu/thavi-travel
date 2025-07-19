<template>
  <AppLayout>
    <div class="bg-white shadow-md rounded-lg p-6 max-w-3xl mx-auto">
      <h2 class="text-2xl font-bold mb-4">💬 Chat with {{ user.name }}</h2>

      <!-- Messages -->
      <div class="border rounded p-4 h-96 overflow-y-auto mb-4 bg-gray-50">
        <div
          v-for="(message, index) in messages"
          :key="index"
          class="mb-2"
        >
          <div
            :class="message.sender === 'admin'
              ? 'text-right'
              : 'text-left'"
          >
            <div
              :class="[
                'inline-block px-4 py-2 rounded-lg',
                message.sender === 'admin'
                  ? 'bg-blue-500 text-white'
                  : 'bg-gray-200 text-gray-900'
              ]"
            >
              {{ message.text }}
            </div>
            <div class="text-xs text-gray-500 mt-1">
              {{ formatDateTime(message.created_at) }}
            </div>
          </div>
        </div>
      </div>

      <!-- Reply form -->
      <form @submit.prevent="sendMessage" class="flex gap-2">
        <input
          v-model="newMessage"
          type="text"
          placeholder="Type your message..."
          class="flex-1 border rounded px-4 py-2 text-sm"
          required
        />
        <button
          type="submit"
          class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 text-sm rounded shadow"
        >
          Send
        </button>
      </form>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  user: Object,
  messages: Array
})

const newMessage = ref('')

function formatDateTime(datetime) {
  return new Date(datetime).toLocaleString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

function sendMessage() {
  router.post(`/admin/chats/${props.user.id}`, {
    message: newMessage.value
  }, {
    onSuccess: () => {
      newMessage.value = ''
    }
  })
}
</script>
