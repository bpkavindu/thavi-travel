<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'

const props = defineProps({
    reservations: Array
})

const user = computed(() => usePage().props.auth.user)

// Cancel reservation
function cancelReservation(id) {
    if (confirm('Are you sure you want to cancel this reservation?')) {
        router.put(`/reservations/${id}/cancel`, {}, {
            onSuccess: () => {
                alert('Reservation cancelled successfully')
            },
            onError: () => {
                alert('Failed to cancel reservation')
            }
        })
    }
}

// Confirm reservation
function confirmReservation(id) {
    if (confirm('Are you sure you want to confirm this reservation?')) {
        router.put(`/reservations/${id}/confirm`, {}, {
            onSuccess: () => {
                alert('Reservation confirmed successfully')
            },
            onError: () => {
                alert('Failed to confirm reservation')
            }
        })
    }
}

</script>

<template>
    <AppLayout title="Reservations">
        <h1 class="text-2xl font-bold mb-4">Reservations List</h1>

        <div class="overflow-auto">
            <table class="min-w-full bg-white border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">Tour Plan</th>
                        <th class="px-4 py-2 text-left">Traveller</th>
                        <th class="px-4 py-2 text-left">Guide</th>
                        <th class="px-4 py-2 text-left">Phone</th>
                        <th class="px-4 py-2 text-left">Date Range</th>
                        <th class="px-4 py-2 text-left">Group Size</th>
                        <th class="px-4 py-2 text-left">Status</th>
                        <th class="px-4 py-2 text-left">Action</th>

                    </tr>
                </thead>
                <tbody>
                    <tr v-for="res in reservations" :key="res.id" class="border-b">
                        <td class="px-4 py-2">{{ res.tour_plan?.title }}</td>
                        <td class="px-4 py-2">{{ res.user?.name }}</td>
                        <td class="px-4 py-2">{{ res.guide?.name }}</td>
                        <td class="px-4 py-2">{{ res.phone }}</td>
                        <td class="px-4 py-2">{{ res.start_date }} to {{ res.end_date }}</td>
                        <td class="px-4 py-2">{{ res.guest_count }}</td>
                        <td class="px-4 py-2">
                            <span v-if="res.status == 1" class="text-yellow-600">Pending</span>
                            <span v-else-if="res.status == 2" class="text-green-600">Confirmed</span>
                            <span v-else class="text-red-600">Cancelled</span>
                        </td>
                        <td>
                            <div v-if="res.status !== '3'" class="flex gap-2">
                                <button @click="cancelReservation(res.id)"
                                    class="px-3 py-1 text-sm bg-red-500 text-white rounded hover:bg-red-600">
                                    Cancel Reservation
                                </button>
                                <div v-if="res.status !== 1 && user?.user_type_id !== 2">
                                    <button v-if="res.status !== '2'" @click="confirmReservation(res.id)"
                                        class="px-3 py-1 text-sm bg-green-500 text-white rounded hover:bg-green-600">
                                        Confirm Reservation{{ res.status }}
                                    </button>

                                </div>
                            </div>
                        </td>
                        <td>
                        </td>
                    </tr>
                    <!-- Empty state row -->
                    <tr v-if="reservations.length === 0">
                        <td colspan="9" class="px-4 py-4 text-center text-gray-500">
                            No reservations found.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
