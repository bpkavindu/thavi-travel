<script setup>
import { ref, watch, computed } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  guides: Array
})

const page = usePage()
const user = computed(() => page.props.auth.user)

const showModal = ref(false)
const previewImage = ref(null)

// Initialize form with first guide data
const form = useForm({
  id: props.guides[0].id,
  name: props.guides[0].name,
  bio: props.guides[0].bio,
  experience: props.guides[0].experience,
  rating: props.guides[0].rating,
  languages: Array.isArray(props.guides[0].languages) ? props.guides[0].languages.join(', ') : props.guides[0].languages,
  locations: Array.isArray(props.guides[0].locations) ? props.guides[0].locations.join(', ') : props.guides[0].locations,
  specialties: Array.isArray(props.guides[0].specialties) ? props.guides[0].specialties.join(', ') : props.guides[0].specialties,
  photo: props.guides[0].photo, // uploaded file
})

watch(() => form.photo, (newPhoto) => {
  if (newPhoto && typeof newPhoto !== 'string') {
    previewImage.value = URL.createObjectURL(newPhoto)
  } else if (typeof newPhoto === 'string') {
    previewImage.value = newPhoto
  }
})

function openEditModal() {
  previewImage.value = props.guides[0].photo || null
  showModal.value = true
}

function onFileChange(e) {
  const file = e.target.files[0]
  if (file) {
    form.photo = file
  }
}

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

    <button
      v-if="user?.user_type_id === 4"
      @click="openEditModal"
      class="mb-6 bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600"
    >
      ✏️ Update My Profile
    </button>

        <button
      v-if="user?.user_type_id === 4"
      @click="openEditModal"
      class="mb-6 bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600"
    >
      ✏️ crete Tour Planne
    </button>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="guide in props.guides"
        :key="guide.id"
        class="bg-white rounded shadow p-4"
      >
        <img
          v-if="guide.photo"
          :src="'storage/'+guide.photo"
          alt="Guide Photo"
          class="w-full h-48 object-cover rounded mb-3"
        />
        <h2 class="text-lg font-bold">{{ guide.name }}</h2>
        <p class="text-sm text-gray-700">{{ guide.bio }}</p>
        <p class="text-sm text-gray-600 mt-1">Experience: {{ guide.experience }} years</p>
        <p class="text-sm text-gray-600">Languages: {{ guide.languages }}</p>
        <p class="text-sm text-gray-600">Locations: {{ guide.locations }}</p>
        <p class="text-sm text-gray-600">Specialties: {{ guide.specialties }}</p>
        <p class="text-yellow-500 font-bold mt-1">⭐ {{ guide.rating }}</p>
      </div>
    </div>

    <!-- Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
    >
      <div class="bg-white p-6 rounded shadow w-full max-w-md max-h-[90vh] overflow-auto">
        <h2 class="text-xl font-bold mb-4">Edit Profile</h2>

        <!-- Image Preview -->
        <div class="mb-3">
          <img
            v-if="previewImage"
            :src="previewImage"
            alt="Guide Photo Preview"
            class="w-full h-48 object-cover rounded mb-2"
          />
        </div>

        <!-- Image Upload -->
        <input
          type="file"
          accept="image/*"
          @change="onFileChange"
          class="mb-4"
        />

        <input v-model="form.name" class="w-full border p-2 mb-3" placeholder="Name" />
        <textarea v-model="form.bio" class="w-full border p-2 mb-3" placeholder="Bio" />
        <input
          v-model.number="form.experience"
          type="number"
          class="w-full border p-2 mb-3"
          placeholder="Experience"
          min="0"
        />
        <input
          v-model.number="form.rating"
          type="number"
          step="0.1"
          class="w-full border p-2 mb-3"
          placeholder="Rating"
          min="0"
          max="5"
        />

        <input
          v-model="form.languages"
          class="w-full border p-2 mb-3"
          placeholder="Languages (comma separated)"
        />
        <input
          v-model="form.locations"
          class="w-full border p-2 mb-3"
          placeholder="Locations (comma separated)"
        />
        <input
          v-model="form.specialties"
          class="w-full border p-2 mb-3"
          placeholder="Specialties (comma separated)"
        />

        <div class="flex justify-end gap-4 mt-4">
          <button @click="showModal = false" class="text-gray-600 hover:underline">
            Cancel
          </button>
          <button
            @click="updateGuide"
            :disabled="form.processing"
            class="bg-blue-600 text-white px-4 py-1 rounded hover:bg-blue-700"
          >
            Save
          </button>
        </div>
      </div>
    </div>
  </div>
  </AppLayout>
</template>
