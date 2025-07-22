<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, } from 'vue'
import { router, useForm, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

// import { router } from '@inertiajs/vue3'  // Not needed since no navigation now

const page = usePage()
const user = computed(() => page.props.auth.user)

defineProps({
    guides: Array,
    tourPlans: Array,
    user: Object
})

const form = useForm({
    name: '',
    email: '',
    phone: '',
    from_date: '',
    to_date: '',
    guests: 1,
    tour_plan_id:'',
    guide_id: null,
})

// Image Preview
const currentImageIndex = ref(0)
const previewImages = ref([])

function openPreview(images, clickedImage) {
    previewImages.value = images.map(img => '/storage/' + img.path)
    currentImageIndex.value = previewImages.value.indexOf(clickedImage)
}
function closePreview() {
    previewImages.value = []
    currentImageIndex.value = 0
}
function prevImage() {
    if (currentImageIndex.value > 0) currentImageIndex.value--
}
function nextImage() {
    if (currentImageIndex.value < previewImages.value.length - 1) currentImageIndex.value++
}

// Reservation Modal State
const showReserveModal = ref(false)
const selectedPlan = ref(null)
const reservation = ref({
    dateRange: { start: '', end: '' },
    contact: '',
    guests: 1,
})

function openReservationModal(plan) {
    selectedPlan.value = plan
    // Reset reservation form each time modal opens
    reservation.value = {
        dateRange: { start: '', end: '' },
        contact: '',
        guests: 1,
    }
    showReserveModal.value = true
}

function closeReservationModal() {
    showReserveModal.value = false
}

function submitReservation() {
    // Sync values from modal state to form
    form.name = user.name
    form.email = user.email
    form.phone = reservation.value.contact
    form.from_date = reservation.value.dateRange.start
    form.to_date = reservation.value.dateRange.end
    form.guests = reservation.value.guests
    form.tour_plan_id = selectedPlan.value.id
    form.guide_id = selectedPlan.value.user_id

    form.post('/reservations', {
        onSuccess: () => {
            closeReservationModal()
            form.reset()
        }
    })
}
</script>

<template>
    <AppLayout>
        <div v-if="user?.user_type_id !== 4" class="flex">
            <!-- Guide Profile Section -->
            <div class="w-1/3 p-4">
                <h2 class="text-xl font-bold mb-4">👤 Profile</h2>
                <div v-for="guide in guides" :key="guide.id" class="bg-white rounded shadow p-4">
                    <img v-if="guide.photo" :src="'/storage/' + guide.photo" alt="Guide Photo"
                        class="w-full h-48 object-cover rounded mb-3" />
                    <h2 class="text-lg font-bold">{{ guide.name }}</h2>
                    <p class="text-sm text-gray-700">{{ guide.bio }}</p>
                    <p class="text-sm text-gray-600 mt-1">Experience: {{ guide.experience }} years</p>
                    <p class="text-sm text-gray-600">Languages: {{ guide.languages }}</p>
                    <p class="text-sm text-gray-600">Locations: {{ guide.locations }}</p>
                    <p class="text-sm text-gray-600">Specialties: {{ guide.specialties }}</p>
                    <p class="text-yellow-500 font-bold mt-1">⭐ {{ guide.rating }}</p>
                </div>
            </div>

            <!-- Tour Plans Section -->
            <div class="w-2/3 p-4">
                <h2 class="text-xl font-bold mb-4">🗺️ Tour Plans</h2>

                <div v-if="!tourPlans.length" class="text-gray-500">No tour plans available.</div>

                <div v-else class="flex flex-col gap-4 h-96 overflow-y-auto">
                    <div v-for="plan in tourPlans" :key="plan.id" class="bg-white shadow rounded p-4 w-full relative">
                        <!-- Top Section -->
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="text-lg font-semibold text-green-700">{{ plan.title }}</h3>
                                <p class="text-sm text-gray-600">Price: ${{ plan.price }}</p>
                                <p v-if="plan.special" class="text-sm italic text-gray-500">🌟 {{ plan.special }}</p>
                            </div>
                        </div>

                        <!-- Scrollable inner content -->
                        <div class="h-[200px] overflow-y-auto pr-2">
                            <!-- Images -->
                            <div class="flex gap-2 mt-2 flex-wrap">
                                <img v-for="img in plan.images" :key="img.id" :src="'/storage/' + img.path"
                                    class="w-20 h-20 object-cover rounded border cursor-pointer" alt="Tour image"
                                    @click="openPreview(plan.images, '/storage/' + img.path)" />
                            </div>

                            <!-- Days -->
                            <ul class="list-disc list-inside text-gray-700 mb-2">
                                <li v-for="(day, i) in plan.days" :key="day.id">
                                    <b>Day {{ i + 1 }} - {{ day.title }}</b>
                                    <p class="text-sm">{{ day.description }}</p>
                                </li>
                            </ul>
                        </div>

                        <!-- Reserve Button -->
                        <div class="mt-4 text-right">
                            <button @click="openReservationModal(plan)"
                                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                                Reserve
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Image Preview Modal -->
        <div v-if="previewImages.length"
            class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50">
            <!-- Click to close -->
            <div class="absolute inset-0" @click="closePreview"></div>

            <!-- Image + controls -->
            <div class="relative z-10 flex flex-col items-center">
                <img :src="previewImages[currentImageIndex]"
                    class="max-w-full max-h-[80vh] rounded shadow-lg border-4 border-white" />

                <!-- Navigation -->
                <div class="flex justify-between items-center w-full mt-4 px-6">
                    <button @click.stop="prevImage" :disabled="currentImageIndex === 0"
                        class="text-white bg-gray-700 hover:bg-gray-600 px-4 py-2 rounded disabled:opacity-50">
                        ⬅ Prev
                    </button>
                    <button @click.stop="nextImage" :disabled="currentImageIndex === previewImages.length - 1"
                        class="text-white bg-gray-700 hover:bg-gray-600 px-4 py-2 rounded disabled:opacity-50">
                        Next ➡
                    </button>
                </div>
            </div>
        </div>

        <!-- Reservation Modal -->
        <div v-if="showReserveModal" class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center">
            <div class="bg-white rounded-lg p-6 w-full max-w-xl space-y-4 relative" @click.stop>
                <button @click="closeReservationModal"
                    class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-2xl font-bold"
                    aria-label="Close">
                    &times;
                </button>

                <h2 class="text-xl font-bold text-blue-700 mb-4">
                    Reserve: {{ selectedPlan?.title }}
                </h2>

                <div class="grid gap-4">
                    <!-- Date Range -->
                    <div class="flex gap-4">
                        <div class="flex flex-col">
                            <label for="start-date" class="text-sm font-semibold mb-1">Start Date</label>
                            <input id="start-date" type="date" v-model="reservation.dateRange.start"
                                class="border p-2 rounded" />
                        </div>
                        <div class="flex flex-col">
                            <label for="end-date" class="text-sm font-semibold mb-1">End Date</label>
                            <input id="end-date" type="date" v-model="reservation.dateRange.end"
                                class="border p-2 rounded" />
                        </div>
                    </div>

                    <!-- Contact -->
                    <div class="flex flex-col">
                        <label for="contact" class="text-sm font-semibold mb-1">Contact Details</label>
                        <input id="contact" type="text" v-model="reservation.contact" placeholder="Phone or Email"
                            class="border p-2 rounded" />
                    </div>

                    <!-- Guests -->
                    <div class="flex flex-col">
                        <label for="guests" class="text-sm font-semibold mb-1">Number of Guests</label>
                        <input id="guests" type="number" min="1" v-model.number="reservation.guests"
                            class="border p-2 rounded" />
                    </div>
                </div>

                <div class="flex justify-end gap-4 mt-4">
                    <button @click="closeReservationModal" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">
                        Cancel
                    </button>
                    <button @click="submitReservation"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Confirm
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
