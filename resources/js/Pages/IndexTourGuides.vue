<script setup>
import { ref, watch, computed } from 'vue'
import { useForm, usePage, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import draggable from 'vuedraggable'  // npm install vuedraggable@next
const props = defineProps({
    guides: Array,
    tourPlans: Array,
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

const currentImageIndex = ref(0)     // Index of currently previewed image

function openPreview(images, clickedImage) {
    previewImages.value = images.map(img => '/storage/' + img.path)
    currentImageIndex.value = previewImages.value.indexOf(clickedImage)
}
const previewImages = ref([])
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

const showEditModal = ref(false)
const editingPlan = ref(null)

const editForm = useForm({
    id: null,
    title: '',
    price: '',
    special: '',
    days: [],
    newImages: [], // for uploading new files
    removeImageIds: [] // track deleted image ids
})

function editTour(plan) {
    editingPlan.value = plan
    editForm.id = plan.id
    editForm.title = plan.title
    editForm.price = plan.price
    editForm.special = plan.special || ''
    editForm.days = plan.days.map(d => ({ ...d })) // deep clone
    editForm.newImages = []
    editForm.removeImageIds = []
    showEditModal.value = true
}

if (!editForm.removeImageIds) editForm.removeImageIds = []

function removeExistingImage(imageId) {
    if (!editForm.removeImageIds.includes(imageId)) {
        editForm.removeImageIds.push(imageId)
    }
}

// Computed property to filter visible images
const visibleImages = computed(() => {
    return editingPlan.value.images.filter(img => !editForm.removeImageIds.includes(img.id))
})

function updateTour() {
    editForm.post(`/tour-plans/${editForm.id}/update`, {
        preserveScroll: true,
        onSuccess: () => {
            showEditModal.value = false
        },
    })
}

const showConfirmModal = ref(false)
const tourIdToDelete = ref(null)

function askDeleteTour(id) {
    tourIdToDelete.value = id
    showConfirmModal.value = true
}

function confirmDeleteTour() {
    router.delete(`/tour-plans/${tourIdToDelete.value}`)
    showConfirmModal.value = false
    tourIdToDelete.value = null
}

function cancelDelete() {
    showConfirmModal.value = false
    tourIdToDelete.value = null
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
            <div v-if="user?.user_type_id !== 4" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
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
            <div v-if="user?.user_type_id === 4" class="flex">
                <div class="w-1/3 p-4">
                    <h2 class="text-xl font-bold mb-4">👤 Profile</h2>
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
                <div class="w-2/3 p-4">
                    <h2 class="text-xl font-bold mb-4">🗺️ Tour Plans</h2>

                    <div v-if="!props.tourPlans.length" class="text-gray-500">No tour plans available.</div>
                    <div v-else class="flex flex-col gap-4 h-96 overflow-y-auto">
                        <div v-for="plan in props.tourPlans" :key="plan.id"
                            class="bg-white shadow rounded p-4 w-full relative">
                            <!-- Top Section -->
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h3 class="text-lg font-semibold text-green-700">
                                        {{ plan.title }}
                                    </h3>
                                    <p class="text-sm text-gray-600">
                                        Price: ${{ plan.price }}
                                    </p>
                                    <p v-if="plan.special" class="text-sm italic text-gray-500">
                                        🌟 {{ plan.special }}
                                    </p>
                                </div>

                                <!-- Edit/Delete Buttons -->
                                <div class="flex gap-2">
                                    <button @click="editTour(plan)"
                                        class="text-sm px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded">
                                        ✏️ Edit
                                    </button>
                                    <button @click="askDeleteTour(plan.id)"
                                        class="text-sm px-3 py-1 bg-red-500 hover:bg-red-600 text-white rounded">
                                        🗑️ Delete
                                    </button>
                                </div>
                            </div>

                            <!-- Scrollable inner content -->
                            <div class="h-[200px] overflow-y-auto pr-2">
                                <ul class="list-disc list-inside text-gray-700 mb-2">
                                    <li v-for="(day, i) in plan.days" :key="day.id">
                                        <b><span>Day {{ i + 1 }} - {{ day.title }}</span></b>
                                        <p class="text-sm">{{ day.description }}</p>
                                    </li>
                                </ul>

                                <div class="flex gap-2 mt-2 flex-wrap">
                                    <img v-for="img in plan.images" :key="img.id" :src="'/storage/' + img.path"
                                        class="w-20 h-20 object-cover rounded border cursor-pointer" alt="Tour image"
                                        @click="openPreview(plan.images, '/storage/' + img.path)" />
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
            <!-- Guide Cards -->

            <!-- Edit Modal -->
            <div v-if="showEditModal"
                class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center">
                <div class="bg-white p-6 rounded w-[95%] max-w-3xl shadow relative overflow-y-auto max-h-[95vh]">
                    <button @click="showEditModal = false"
                        class="absolute top-2 right-2 text-gray-500 hover:text-black">
                        ✖
                    </button>

                    <h2 class="text-lg font-semibold mb-4">Edit Tour Plan</h2>

                    <form @submit.prevent="updateTour" class="space-y-4">
                        <!-- Title -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Title</label>
                            <input v-model="editForm.title" type="text" class="w-full border rounded px-3 py-2" />
                        </div>

                        <!-- Price -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Price</label>
                            <input v-model="editForm.price" type="number" class="w-full border rounded px-3 py-2" />
                        </div>

                        <!-- Special -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Special</label>
                            <input v-model="editForm.special" type="text" class="w-full border rounded px-3 py-2" />
                        </div>

                        <!-- Days Section -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Days</label>
                            <div v-for="(day, index) in editForm.days" :key="index"
                                class="border rounded p-3 mb-2 space-y-2">
                                <div>
                                    <label class="text-xs text-gray-600">Title</label>
                                    <input v-model="day.title" type="text" class="w-full border rounded px-2 py-1" />
                                </div>
                                <div>
                                    <label class="text-xs text-gray-600">Description</label>
                                    <textarea v-model="day.description"
                                        class="w-full border rounded px-2 py-1"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Existing Images -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Existing Images</label>
                            <div class="flex flex-wrap gap-2">
                                <div v-for="img in visibleImages" :key="img.id"
                                    class="relative w-24 h-24 border rounded overflow-hidden">
                                    <img :src="'/storage/' + img.path" class="w-full h-full object-cover" />
                                    <button @click.prevent="removeExistingImage(img.id)"
                                        class="absolute top-0 right-0 bg-red-600 text-white px-1 text-xs">
                                        ✖
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Upload New Images -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Add New Images</label>
                            <input type="file" multiple @change="e => editForm.newImages = Array.from(e.target.files)"
                                class="w-full border rounded px-3 py-2" />
                        </div>

                        <!-- Buttons -->
                        <div class="flex justify-end gap-2 mt-4">
                            <button type="button" @click="showEditModal = false" class="bg-gray-300 px-4 py-2 rounded">
                                Cancel
                            </button>
                            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>


            <!-- Image Preview Modal -->
            <div v-if="previewImages.length"
                class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50">
                <!-- Close area -->
                <div class="absolute inset-0" @click="closePreview"></div>

                <!-- Image and controls -->
                <div class="relative z-10 flex flex-col items-center">
                    <img :src="previewImages[currentImageIndex]"
                        class="max-w-full max-h-[80vh] rounded shadow-lg border-4 border-white" />

                    <!-- Navigation buttons -->
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
                        <input v-model="tourPlanForm.price" type="number" min="0" step="0.01"
                            class="w-full border p-2 mb-3" placeholder="Price Per Person" />

                        <!-- Multi-image upload -->
                        <label class="block mb-2 font-semibold">Upload Images</label>
                        <input type="file" multiple accept="image/*" @change="onImagesChange" class="mb-4" />

                        <!-- Draggable Previews -->
                        <draggable v-model="tourPlanForm.imagePreviews" item-key="src" @end="onDragEnd"
                            class="flex flex-wrap gap-2">
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

            <!-- Custom confirmation modal -->
            <div v-if="showConfirmModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                <div class="bg-white p-6 rounded shadow-lg max-w-sm text-center">
                    <p class="mb-4">Are you sure you want to delete this tour plan?</p>
                    <button @click="confirmDeleteTour" class="bg-red-600 text-white px-4 py-2 rounded mr-2">Yes,
                        Delete</button>
                    <button @click="cancelDelete" class="bg-gray-300 px-4 py-2 rounded">Cancel</button>
                </div>
            </div>

        </div>
    </AppLayout>
</template>
