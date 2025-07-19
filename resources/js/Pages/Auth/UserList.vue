<script setup>
import { defineProps, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    users: Array
})

// View Modal
const showViewModal = ref(false)
const selectedUser = ref({})

// Edit Modal
const showEditModal = ref(false)
const editedUser = ref({})

// View User
function viewUser(id) {
    const user = props.users.find(u => u.id === id)
    selectedUser.value = user
    showViewModal.value = true
}

// Edit User
function editUser(id) {
    const user = props.users.find(u => u.id === id)
    editedUser.value = {
        ...user,
        password: '',
        status: user.status ?? 'active',
    }
    showEditModal.value = true
}

// Close Modals
function closeViewModal() {
    showViewModal.value = false
}
function closeEditModal() {
    showEditModal.value = false
}
const showPassword = ref(false)

const statusOptions = [
    { id: '1', label: 'Active' },
    { id: '2', label: 'Inactive' },
    { id: '3', label: 'Temporary Inactive' },
]

// Update User
function updateUser() {
    const payload = {
        name: editedUser.value.name,
        email: editedUser.value.email,
        status: editedUser.value.status,
    }

    if (editedUser.value.password) {
        payload.password = editedUser.value.password
    }

    router.patch(`/users/${editedUser.value.id}`, payload, {
        onSuccess: () => {
            showEditModal.value = false
            window.location.reload()
        }
    })

}
</script>

<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h1 class="text-2xl font-semibold text-gray-800">👥 User List</h1>
                    <p class="text-sm text-gray-500 mt-1">Manage all registered users and their roles.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full w-full divide-y divide-gray-200">
                        <thead class="bg-indigo-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-700 uppercase">Name</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-700 uppercase">Email</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-700 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-700 uppercase">User Type
                                </th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-700 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-4 text-gray-800 whitespace-nowrap">{{ user.name }}</td>
                                <td class="px-6 py-4 text-gray-600 whitespace-nowrap">{{ user.email }}</td>
                                <td class="px-6 py-4">
                                    <span :class="{
                                        'text-green-600': user.status === 1,
                                        'text-red-500': user.status === 2,
                                        'text-yellow-500': user.status === 3
                                    }">
                                        {{
                                            user.status === 1
                                                ? 'Active'
                                                : user.status === 2
                                                    ? 'Inactive'
                                                    : user.status === 3
                                                        ? 'Temporary Inactive'
                                                        : 'Unknown'
                                        }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">{{ user.user_type?.name ?? '—' }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex gap-2">
                                        <button @click="viewUser(user.id)"
                                            class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm">
                                            View
                                        </button>
                                        <button @click="editUser(user.id)"
                                            class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm">
                                            Edit
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- View Modal -->
        <div v-if="showViewModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-md">
                <div class="flex justify-between items-center px-6 py-4 border-b">
                    <h2 class="text-xl font-semibold text-gray-800">👤 User Details</h2>
                    <button @click="closeViewModal" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
                </div>
                <div class="px-6 py-4 space-y-2">
                    <p><strong>Name:</strong> {{ selectedUser.name }}</p>
                    <p><strong>Email:</strong> {{ selectedUser.email }}</p>
                    <p><strong>Status:</strong> <span :class="{
                                        'text-green-600': selectedUser.status === 1,
                                        'text-red-500': selectedUser.status === 2,
                                        'text-yellow-500': selectedUser.status === 3
                                    }">
                                        {{
                                            selectedUser.status === 1
                                                ? 'Active'
                                                : selectedUser.status === 2
                                                    ? 'Inactive'
                                                    : selectedUser.status === 3
                                                        ? 'Temporary Inactive'
                                                        : 'Unknown'
                                        }}
                                    </span></p>
                    
                    <p><strong>User Type:</strong> {{ selectedUser.user_type?.name ?? '—' }}</p>
                </div>
                <div class="px-6 py-3 border-t text-right">
                    <button @click="closeViewModal" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                        Close
                    </button>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-md">
                <div class="flex justify-between items-center px-6 py-4 border-b">
                    <h2 class="text-xl font-semibold text-gray-800">✏️ Edit User</h2>
                    <button @click="closeEditModal" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Name</label>
                        <input v-model="editedUser.name" class="mt-1 block w-full rounded border-gray-300 shadow-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input v-model="editedUser.email" type="email"
                            class="mt-1 block w-full rounded border-gray-300 shadow-sm" />
                    </div>
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>

                        <input :type="showPassword ? 'text' : 'password'" v-model="editedUser.password"
                            placeholder="Leave blank to keep current password"
                            class="mt-1 block w-full rounded border-gray-300 shadow-sm pr-10" />

                        <!-- Eye Icon Button -->
                        <button type="button" class="absolute right-2 top-9 text-gray-500 hover:text-gray-700"
                            @click="showPassword = !showPassword">
                            <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>

                            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.978 9.978 0 013.163-4.568M3 3l18 18M17.94 17.94A9.956 9.956 0 0012 19c-4.477 0-8.268-2.943-9.542-7a9.978 9.978 0 011.63-2.659" />
                            </svg>
                        </button>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select v-model="editedUser.status" class="mt-1 block w-full rounded border-gray-300 shadow-sm">
                            <option v-for="option in statusOptions" :key="option.id" :value="option.id">
                                {{ option.label }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="px-6 py-3 border-t text-right">
                    <button @click="updateUser"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded mr-2">
                        Save
                    </button>
                    <button @click="closeEditModal" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
