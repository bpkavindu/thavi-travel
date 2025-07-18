<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center p-4 sm:p-6 lg:p-8 font-inter">
    <div class="bg-white rounded-xl shadow-lg p-6 sm:p-8 lg:p-10 w-full max-w-2xl">
      <div class="text-center mb-8">
        <h1 class="text-3xl sm:text-4xl font-bold text-gray-800 mb-2">AI Tour Planner</h1>
        <p class="text-gray-600 text-base sm:text-lg">
          Tell us about your dream trip and we'll create the perfect itinerary
        </p>
      </div>

      <!-- Step Indicator -->
      <div class="flex justify-center items-center mb-8">
        <div class="relative w-full max-w-sm">
          <div class="absolute inset-0 flex items-center" aria-hidden="true">
            <div class="w-full border-t border-gray-200"></div>
          </div>
          <div class="relative flex justify-between">
            <div class="flex items-center">
              <span class="h-8 w-8 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-sm ring-8 ring-blue-100">1</span>
              <span class="ml-3 text-sm font-medium text-blue-600 hidden sm:block">Basic Information</span>
            </div>
            <div class="flex items-center">
              <span class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 font-bold text-sm">2</span>
              <span class="ml-3 text-sm font-medium text-gray-500 hidden sm:block">Preferences</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Form -->
      <form @submit.prevent="submit">
        <div class="mb-8">
          <h2 class="text-xl sm:text-2xl font-semibold text-gray-800 mb-4 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 sm:h-7 sm:w-7 text-blue-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.727A8 8 0 016.343 7.273L17.657 16.727zm0 0L19.5 18.5M17.657 16.727a2 2 0 11-2.828 2.828L7.273 6.343a2 2 0 012.828-2.828L17.657 16.727zM5 10a2 2 0 100 4 2 2 0 000-4z"/>
            </svg>
            Basic Information
          </h2>

          <!-- Inputs -->
          <div class="mb-6">
            <label for="destination" class="block text-gray-700 text-sm font-medium mb-2">Where do you want to go?</label>
            <input v-model="form.destination" type="text" id="destination" class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm" placeholder="e.g. Paris, France" required>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
            <div>
              <label for="days" class="block text-gray-700 text-sm font-medium mb-2">How many days?</label>
              <select v-model="form.days" id="days" class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm" required>
                <option disabled value="">Select days</option>
                <option v-for="option in dayOptions" :key="option" :value="option">{{ option }}</option>
              </select>
            </div>

            <div>
              <label for="budget" class="block text-gray-700 text-sm font-medium mb-2">Budget range</label>
              <select v-model="form.budget" id="budget" class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm" required>
                <option disabled value="">Select budget</option>
                <option v-for="option in budgetOptions" :key="option" :value="option">{{ option }}</option>
              </select>
            </div>
          </div>

          <div class="mb-6">
            <label for="groupSize" class="block text-gray-700 text-sm font-medium mb-2">Group size</label>
            <select v-model="form.groupSize" id="groupSize" class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm" required>
              <option disabled value="">How many people?</option>
              <option v-for="option in groupSizeOptions" :key="option" :value="option">{{ option }}</option>
            </select>
          </div>
        </div>

        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg">
          Next
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue'
import { useForm } from '@inertiajs/vue3'

// Dropdown options
const dayOptions = ['1-3 days', '4-7 days', '8-14 days', '15+ days']
const budgetOptions = ['$100 - $500', '$501 - $1000', '$1001 - $2000', '$2000+']
const groupSizeOptions = ['1', '2', '3-5', '6-10', '10+']

// Inertia form setup
const form = useForm({
  destination: '',
  days: '',
  budget: '',
  groupSize: '',
})

function submit() {
  form.post('/tour-plan/basic-info', {
    onSuccess: () => {
      console.log("Form submitted successfully")
      // Optionally redirect or show message
    },
    onError: () => {
      console.log("Validation failed")
    },
  })
}
</script>
