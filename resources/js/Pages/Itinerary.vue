<template>
  <div class="min-h-screen bg-white flex flex-col items-center justify-center p-6">
    <div class="max-w-3xl w-full bg-gray-50 shadow-md rounded-xl p-6">
      <h2 class="text-3xl font-bold text-gray-800 mb-4 text-center">
        Suggested Tour Plan
      </h2>

      <div class="text-gray-700 space-y-3">
        <p><strong>Destination:</strong> {{ itinerary.destination }}</p>
        <p><strong>Days:</strong> {{ itinerary.days }}</p>
        <p><strong>Budget:</strong> {{ itinerary.budget }}</p>
        <p><strong>Group Size:</strong> {{ itinerary.groupSize }}</p>

        <div>
          <strong>Suggested Activities:</strong>
          <ul class="list-disc list-inside mt-2">
            <li v-for="(activity, index) in itinerary.activities" :key="index">
              {{ activity }}
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3'

const itinerary = usePage().props.itinerary
</script>
