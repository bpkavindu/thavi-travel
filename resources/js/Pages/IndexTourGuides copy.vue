<script setup>
import { ref, watch, computed } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import draggable from 'vuedraggable'  // npm install vuedraggable@next

const props = defineProps({
    guides: Array
})

const page = usePage()
const user = computed(() => page.props.auth.user)

const showModal = ref(false)
const showTourPlanModal = ref(false)
const previewImage = ref(null)

// Edit Guide Profile Form
const form = useForm({
    id: props.guides[0].id,
    name: props.guides[0].name,
    bio: props.guides[0].bio,
    experience: props.guides[0].experience,
    rating: props.guides[0].rating,
    languages: Array.isArray(props.guides[0].languages) ? props.guides[0].languages.join(', ') : props.guides[0].languages,
    locations: Array.isArray(props.guides[0].locations) ? props.guides[0].locations.join(', ') : props.guides[0].locations,
    specialties: Array.isArray(props.guides[0].specialties) ? props.guides[0].specialties.join(', ') : props.guides[0].specialties,
    photo: props.guides[0].photo,
  
})

// Tour Plan Form with multi-image support
const tourPlanForm = useForm({
    title: '',
    dayCount: '',
    days: [{
        title: '',
        description: '',
     
    }],
    images: [],       // File objects
    imagePreviews: [], // Preview URLs
    special: '',
    price: ''
})

// Sync days array with dayCount
watch(() => tourPlanForm.dayCount, (newVal) => {
    const count = parseInt(newVal) || 0
    if (count > 0) {
        const newDays = []
        for (let i = 0; i < count; i++) {
            newDays.push(tourPlanForm.days[i] || {
                title: '',
                description: '',
                special: '',
                price: ''
            })
        }
        tourPlanForm.days = newDays
    }
})

// Edit Profile modal open
function openEditModal() {
    previewImage.value = props.guides[0].photo || null
    showModal.value = true
}

// Profile photo change handler
function onFileChange(e) {
    const file = e.target.files[0]
    if (file) {
        form.photo = file
    }
}

// Tour Plan modal open
function openTourPlanModal() {
    showTourPlanModal.value = true
}

// Handle multiple images selection and preview
function onImagesChange(e) {
    const files = e.target.files
    tourPlanForm.images = []
    tourPlanForm.imagePreviews.forEach(url => URL.revokeObjectURL(url)) // revoke old previews
    tourPlanForm.imagePreviews = []

    if (files.length) {
        for (let i = 0; i < files.length; i++) {
            const file = files[i]
            tourPlanForm.images.push(file)
            const url = URL.createObjectURL(file)
            tourPlanForm.imagePreviews.push(url)
        }
    }
}

// Helper: Sync images array order after drag-drop reorder
function onDragEnd() {
    // Reorder images to match imagePreviews order
    const newImagesOrder = []
    for (const previewUrl of tourPlanForm.imagePreviews) {
        // Find index in old previews (should be same index)
        const idx = tourPlanForm.imagePreviews.indexOf(previewUrl)
        if (idx !== -1 && tourPlanForm.images[idx]) {
            newImagesOrder.push(tourPlanForm.images[idx])
        }
    }
    tourPlanForm.images = newImagesOrder
}

// Save Tour Plan with images
function saveTourPlan() {
    tourPlanForm.post('/tour-plans', {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            showTourPlanModal.value = false
            tourPlanForm.reset()
            tourPlanForm.dayCount = 1
            tourPlanForm.days = [{ title: '', description: '', special: '', price: '' }]
            tourPlanForm.images = []
            tourPlanForm.imagePreviews.forEach(url => URL.revokeObjectURL(url))
            tourPlanForm.imagePreviews = []
        },
    })
}

// Update guide info
function updateGuide() {
    form.post(`/guides/${form.id}`, {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            showModal.value = false
        },
    })
}
</script>

<template>
    <AppLayout>
        <div class="p-6 max-w-7xl mx-auto">
            <h1 class="text-2xl font-bold mb-6">Tour Guides</h1>

            <!-- Action Buttons -->
            <div class="flex gap-4 mb-6" v-if="user?.user_type_id === 4">
                <button @click="openEditModal" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                    ✏️ Update My Profile
                </button>
                <button @click="openTourPlanModal"
                    class="bg-indigo-500 text-white px-4 py-2 rounded hover:bg-indigo-600">
                    🗺️ Create Tour Plan
                </button>
            </div>

            <!-- Guide Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="guide in props.guides" :key="guide.id" class="bg-white rounded shadow p-4">
                    <img v-if="guide.photo" :src="'storage/' + guide.photo" alt="Guide Photo"
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

            <!-- Edit Guide Profile Modal -->
            <div v-if="showModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                <div class="bg-white p-6 rounded shadow w-full max-w-md max-h-[90vh] overflow-auto">
                    <h2 class="text-xl font-bold mb-4">Edit Profile</h2>

                    <div class="mb-3">
                        <img v-if="previewImage" :src="previewImage" alt="Guide Photo Preview"
                            class="w-full h-48 object-cover rounded mb-2" />
                    </div>

                    <input type="file" accept="image/*" @change="onFileChange" class="mb-4" />

                    <input v-model="form.name" class="w-full border p-2 mb-3" placeholder="Name" />
                    <textarea v-model="form.bio" class="w-full border p-2 mb-3" placeholder="Bio" />
                    <input v-model.number="form.experience" type="number" class="w-full border p-2 mb-3"
                        placeholder="Experience" />
                    <input v-model.number="form.rating" type="number" step="0.1" class="w-full border p-2 mb-3"
                        placeholder="Rating" min="0" max="5" />
                    <input v-model="form.languages" class="w-full border p-2 mb-3"
                        placeholder="Languages (comma separated)" />
                    <input v-model="form.locations" class="w-full border p-2 mb-3"
                        placeholder="Locations (comma separated)" />
                    <input v-model="form.specialties" class="w-full border p-2 mb-3"
                        placeholder="Specialties (comma separated)" />

                    <div class="flex justify-end gap-4 mt-4">
                        <button @click="showModal = false" class="text-gray-600 hover:underline">Cancel</button>
                        <button @click="updateGuide" :disabled="form.processing"
                            class="bg-blue-600 text-white px-4 py-1 rounded hover:bg-blue-700">
                            Save
                        </button>
                    </div>
                </div>
            </div>

            <!-- Create Tour Plan Modal -->
            <div v-if="showTourPlanModal"
                class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                <div class="bg-white rounded shadow w-full max-w-5xl max-h-[90vh] flex overflow-hidden"
                    style="min-height: 400px">
                    <!-- Left side: Basic Info + images -->
                    <div class="w-1/3 border-r p-6 overflow-auto">
                        <h2 class="text-xl font-bold mb-4">Create Tour Plan</h2>
                        <input v-model="tourPlanForm.title" class="w-full border p-2 mb-3" placeholder="Tour Title" />
                        <input v-model.number="tourPlanForm.dayCount" type="number" min="1"
                            class="w-full border p-2 mb-3" placeholder="Number of Days" />
                        <textarea v-model="tourPlanForm.special" class="w-full border p-2 mb-3"
                            placeholder="Special Things (optional)"></textarea>
                        <input v-model="tourPlanForm.price" type="number" min="0" step="0.01" class="w-full border p-2 mb-3"
                            placeholder="Price Per Person" />

                        <!-- Multi-image upload -->
                        <label class="block mb-2 font-semibold">Upload Images</label>
                        <input type="file" multiple accept="image/*" @change="onImagesChange" class="mb-4" />

                        <!-- Draggable Previews -->
                        <draggable
                            v-model="tourPlanForm.imagePreviews"
                            item-key="src"
                            @end="onDragEnd"
                            class="flex flex-wrap gap-2"
                        >
                            <template #item="{ element, index }">
                                <div class="w-20 h-20 relative rounded overflow-hidden border cursor-move">
                                    <img :src="element" alt="Preview" class="object-cover w-full h-full" />
                                </div>
                            </template>
                        </draggable>
                    </div>

                    <!-- Right side: Days details -->
                    <div class="w-2/3 p-6 overflow-auto">
                        <div v-for="(day, index) in tourPlanForm.days" :key="index" class="mb-4 border-b pb-4">
                            <h3 class="font-semibold text-gray-700 mb-2">Day {{ index + 1 }}</h3>
                            <input v-model="day.title" class="w-full border p-2 mb-2" placeholder="Day Title" />
                            <textarea v-model="day.description" class="w-full border p-2"
                                placeholder="Day Description"></textarea>
                        </div>

                        <div class="flex justify-end gap-4 mt-6">
                            <button @click="showTourPlanModal = false" class="text-gray-600 hover:underline">
                                Cancel
                            </button>
                            <button @click="saveTourPlan" :disabled="tourPlanForm.processing"
                                class="bg-green-600 text-white px-4 py-1 rounded hover:bg-green-700">
                                Save Tour Plan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
