<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const form = useForm({
  name: '',
  location: '',
  description: '',
  qr_code: '',
  image: null,
});

function submit() {
  form.post('/photo-spots');
}

function onFileChange(e) {
  form.image = e.target.files[0];
}
</script>

<template>
  <div class="p-6 max-w-lg mx-auto">
    <h1 class="text-xl font-bold mb-4">Add New Photo Spot</h1>

    <form @submit.prevent="submit" enctype="multipart/form-data" class="space-y-4">

      <div>
        <label class="block mb-1 font-semibold">Name</label>
        <input type="text" v-model="form.name" class="border p-2 w-full" />
        <div v-if="form.errors.name" class="text-red-600">{{ form.errors.name }}</div>
      </div>

      <div>
        <label class="block mb-1 font-semibold">Location</label>
        <input type="text" v-model="form.location" class="border p-2 w-full" />
        <div v-if="form.errors.location" class="text-red-600">{{ form.errors.location }}</div>
      </div>

      <div>
        <label class="block mb-1 font-semibold">Description</label>
        <textarea v-model="form.description" class="border p-2 w-full"></textarea>
        <div v-if="form.errors.description" class="text-red-600">{{ form.errors.description }}</div>
      </div>

      <div>
        <label class="block mb-1 font-semibold">QR Code</label>
        <input type="text" v-model="form.qr_code" class="border p-2 w-full" />
        <div v-if="form.errors.qr_code" class="text-red-600">{{ form.errors.qr_code }}</div>
      </div>

      <div>
        <label class="block mb-1 font-semibold">Image</label>
        <input type="file" @change="onFileChange" accept="image/*" />
        <div v-if="form.errors.image" class="text-red-600">{{ form.errors.image }}</div>
      </div>

      <button type="submit" :disabled="form.processing" class="bg-blue-600 text-white px-4 py-2 rounded">
        Submit
      </button>
    </form>
  </div>
</template>