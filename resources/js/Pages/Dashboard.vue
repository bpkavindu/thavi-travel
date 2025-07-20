<template>
    <DashboardLayout>
        <!-- Show chat warning if restricted -->
        <div v-if="userStatus === 3 || userStatus === 2" class="bg-yellow-100 text-yellow-800 p-4 rounded mb-6">
            <p>
                Your account is currently <strong>restricted</strong>. Please contact <strong>Etraveller admin</strong>
                to resolve this issue.
            </p>

            <!-- Chat Toggle Button -->
            <button @click="chatOpen = !chatOpen"
                class="mt-4 bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 text-sm">
                {{ chatOpen ? 'Close Live Chat' : 'Open Live Chat' }}
            </button>

            <!-- Chat Box -->
            <div v-if="chatOpen" class="mt-4 bg-white border border-yellow-300 rounded p-4">
                <h3 class="font-semibold mb-2 text-yellow-700">💬 Live Chat with Admin</h3>

                <div v-for="(msg, index) in chatMessages" :key="index" class="flex"
                    :class="msg.sender.toLowerCase() === 'user' || msg.sender === 'You' ? 'justify-end' : 'justify-start'">

                    <div class="max-w-xs px-3 py-2 rounded shadow text-sm" :class="msg.sender.toLowerCase() === 'user' || msg.sender === 'You'
                        ? 'bg-blue-100 text-right'
                        : 'bg-yellow-100 text-left'">

                        <div class="font-semibold text-xs text-gray-700">
                            {{ msg.sender }}
                        </div>
                        <div class="text-gray-800 break-words">
                            {{ msg.text }}
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            {{ formatDate(msg.created_at) }} {{ formatTime(msg.created_at) }}
                        </div>
                    </div>
                </div>

                <div class="flex space-x-2">
                    <input v-model="newMessage" type="text" class="flex-1 border rounded px-3 py-1 text-sm"
                        placeholder="Type your message..." />
                    <button @click="sendMessage"
                        class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 text-sm">
                        Send
                    </button>
                </div>
            </div>
        </div>

        <!-- Dashboard cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-8"
            >
            <Card :class="{ 'pointer-events-none opacity-50': userStatus === 3 || userStatus === 2 }" icon="🤖" title="AI Tour Planner" color="bg-gradient-to-r from-indigo-500 to-purple-500"
                link="/tourplanner" as="button" />
            <Card :class="{ 'pointer-events-none opacity-50': userStatus === 3 || userStatus === 2 }" icon="📍" title="Attractions" color="bg-gradient-to-r from-pink-500 to-yellow-500" link="/attraction"
                as="button" />
            <Card :class="{ 'pointer-events-none opacity-50': userStatus === 3 || userStatus === 2 }" icon="🗺️" title="Map" color="bg-gradient-to-r from-green-400 to-blue-500" link="/attractionsmap/map"
                as="button" />
            <Card :class="{ 'pointer-events-none opacity-50': userStatus === 3 || userStatus === 2 }" icon="📸" title="Photo Spots" color="bg-gradient-to-r from-pink-400 to-purple-500" link="/photo-spots"
                as="button" />

            <Card v-if="user?.user_type_id === 1" :class="{ 'pointer-events-none opacity-50': userStatus === 3 || userStatus === 2 }" icon="🧑‍🤝‍🧑" title="Users"
                color="bg-gradient-to-r from-purple-500 to-indigo-500" link="/users" as="button" />
            <Card :class="{ 'pointer-events-none opacity-50': userStatus === 3 || userStatus === 2 }" icon="👤" title="Profile" color="bg-gradient-to-r from-blue-500 to-cyan-500" link="/profile"
                as="button" />
            <Card v-if="user?.user_type_id === 1" :class="{ 'pointer-events-none opacity-50': userStatus === 3 || userStatus === 2 }" icon="💬" title="Chat"
                color="bg-gradient-to-r from-purple-500 to-pink-500" link="/admin/chats" as="button" />
            <Card icon="🚪" title="Logout" color="bg-gradient-to-r from-red-500 to-pink-500" link="/logout"
                method="post" as="button" />
        </div>
    </DashboardLayout>
</template>

<script setup>
import { computed, ref } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import Card from '@/Components/DashboardCard.vue'
import { defineProps } from 'vue'

const props = defineProps({
    chatMessages: Array,
    userId: Number,
})

const user = usePage().props.auth.user
const userStatus = computed(() => Number(user.status))
const chatOpen = ref(false)
const newMessage = ref('')
const chatMessages = ref([...props.chatMessages])

async function sendMessage() {
    if (newMessage.value.trim()) {
        const message = newMessage.value.trim()
        chatMessages.value.push({ sender: 'You', text: message, created_at: new Date().toISOString() })

        try {
            await router.post('/chat/send', { message }, {
                preserveScroll: true,
                onFinish: () => {
                    newMessage.value = ''
                }
            })
        } catch (error) {
            console.error('Sending failed:', error)
        }
    }
}

function formatDate(dateStr) {
    return new Date(dateStr).toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' })
}

function formatTime(dateStr) {
    return new Date(dateStr).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
}
</script>
