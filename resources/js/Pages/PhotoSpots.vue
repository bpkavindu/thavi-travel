<script setup>
import { ref,computed } from 'vue'
import { router,usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const page = usePage()
const props = defineProps({
  spots: Array,
})
const user = computed(() => page.props.auth.user)
const showModal = ref(false)
const fileInput = ref(null) // ref to clear file input later
const imagePreview = ref(null) // for optional image preview

const form = ref({
  name: '',
  location: '',
  province: '',
  description: '',
  best_time: '',
  difficulty: 'Easy',
  rating: 1,
  likes: 0,
  image: null,
})

function onFileChange(e) {
  const file = e.target.files[0]
  form.value.image = file || null

  if (file) {
    imagePreview.value = URL.createObjectURL(file)
  } else {
    imagePreview.value = null
  }
}

function resetForm() {
  form.value = {
    name: '',
    location: '',
    province: '',
    description: '',
    best_time: '',
    difficulty: 'Easy',
    rating: 1,
    likes: 0,
    image: null,
  }
  imagePreview.value = null
  if (fileInput.value) fileInput.value.value = null // clear file input element
}

function submitForm() {
  const data = new FormData()
  data.append('name', form.value.name)
  data.append('location', form.value.location)
  data.append('province', form.value.province)
  data.append('description', form.value.description)
  data.append('best_time', form.value.best_time)
  data.append('difficulty', form.value.difficulty)
  data.append('rating', form.value.rating)
  data.append('likes', form.value.likes)
  if (form.value.image) {
    data.append('image', form.value.image)
  }

  router.post('/photo-spots', data, {
    forceFormData: true,
    onSuccess: () => {
      showModal.value = false
      resetForm()
    },
  })
}
</script>

<template>
   <AppLayout>
  <div class="p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
      <div>
        <h1 class="text-3xl font-bold">Sri Lankan Photo Spots</h1>
        <p class="text-gray-600">Capture the beauty of the Pearl of the Indian Ocean</p>
      </div>

      <!-- Trigger Modal -->
      <button v-if="user?.user_type_id === 1"
        @click="showModal = true"
        class="inline-block bg-gradient-to-r from-green-400 to-blue-500 text-white px-4 py-2 rounded-lg shadow hover:from-green-500 hover:to-blue-600 transition"
      >
        ➕ Add Photo Spot
      </button>
    </div>

    <!-- Grid -->
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="spot in props.spots"
        :key="spot.id"
        class="bg-white shadow rounded-xl overflow-hidden"
      >
        <div class="relative h-48">
          <img
            v-if="spot.image"
            :src="`/storage/${spot.image}`"
            alt="Photo"
            class="object-cover w-full h-full"
          />
          <div
            v-else
            class="bg-gray-100 h-full flex items-center justify-center text-gray-400 text-xl"
          >
            📷 {{ spot.name }}
          </div>
          <div class="absolute top-2 left-2 bg-white text-xs px-2 py-1 rounded-full shadow">
            Best: {{ spot.best_time }}
          </div>
          <div
            class="absolute top-2 right-2 text-white text-xs font-bold px-2 py-1 rounded-full"
            :class="spot.difficulty === 'Hard' ? 'bg-red-500' : spot.difficulty === 'Easy' ? 'bg-green-500' : 'bg-yellow-500'"
          >
            {{ spot.difficulty }}
          </div>
        </div>

        <div class="p-4">
          <div class="flex justify-between items-center mb-1">
            <h2 class="text-lg font-semibold">{{ spot.name }}</h2>
            <div class="text-yellow-500 font-semibold text-sm">★ {{ spot.rating }}</div>
          </div>
          <div class="text-sm text-gray-500 mb-2">
            📍 {{ spot.location }}, {{ spot.province }}
          </div>
          <p class="text-sm text-gray-700 mb-3">{{ spot.description }}</p>

          <div class="flex flex-wrap gap-2 mb-3">
            <span
              v-for="tag in spot.tags"
              :key="tag"
              class="bg-gray-200 text-xs px-2 py-1 rounded-full"
            >
              {{ tag }}
            </span>
          </div>

          <div class="text-sm text-gray-600 flex items-center gap-2">
            ❤️ {{ spot.likes }}
          </div>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50"
    >
      <div class="bg-white rounded-xl p-6 w-full max-w-xl shadow-lg relative">
        <h2 class="text-2xl font-bold mb-4">Add New Photo Spot</h2>

        <!-- Close Button -->
        <button
          class="absolute top-2 right-2 text-gray-500 hover:text-black"
          @click="showModal = false"
        >
          ✖
        </button>

        <form @submit.prevent="submitForm" enctype="multipart/form-data" class="space-y-4">
          <input
            v-model="form.name"
            type="text"
            placeholder="Name"
            class="w-full border rounded px-3 py-2"
            required
          />
          <input
            v-model="form.location"
            type="text"
            placeholder="Location"
            class="w-full border rounded px-3 py-2"
            required
          />
          <input
            v-model="form.province"
            type="text"
            placeholder="Province"
            class="w-full border rounded px-3 py-2"
          />
          <input
            v-model="form.best_time"
            type="text"
            placeholder="Best Time to Visit"
            class="w-full border rounded px-3 py-2"
          />
          <input
            v-model.number="form.rating"
            type="number"
            step="0.1"
            min="0"
            max="5"
            placeholder="Rating"
            class="w-full border rounded px-3 py-2"
          />
          <textarea
            v-model="form.description"
            placeholder="Description"
            class="w-full border rounded px-3 py-2"
          ></textarea>
          <select
            v-model="form.difficulty"
            class="w-full border rounded px-3 py-2"
          >
            <option value="Easy">Easy</option>
            <option value="Moderate">Moderate</option>
            <option value="Hard">Hard</option>
          </select>

          <input
            ref="fileInput"
            type="file"
            @change="onFileChange"
            accept="image/*"
            class="w-full border rounded px-3 py-2"
          />

          <div v-if="imagePreview" class="mt-2">
            <img :src="imagePreview" alt="Image Preview" class="max-h-40 rounded" />
          </div>

          <button
            type="submit"
            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition"
          >
            Save Spot
          </button>
        </form>
      </div>
    </div>
  </div>
  </AppLayout>
</template>
