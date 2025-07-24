<script setup>
import { router } from '@inertiajs/vue3'

function goToLogin() {
  router.visit('/login')
}

function goToRegister() {
  router.visit('/register')
}
</script>

<template>
  <div
    class="min-h-screen bg-cover bg-center flex items-center justify-center"
    style="background-image: url('/images/main.jpg');"
  >
    <div class="bg-black/60 p-10 rounded-xl text-center text-white max-w-xl w-full mx-4">
      <h1 class="text-4xl md:text-5xl font-extrabold mb-4">🌍 Welcome to <br>eTraveller</h1>
      <p class="text-lg md:text-xl mb-8">Discover. Plan. Travel with ease and confidence.</p>

      <div class="flex justify-center gap-4">
        <button
          @click="goToLogin"
          class="px-6 py-3 bg-blue-600 hover:bg-blue-700 rounded-full font-semibold transition"
        >
          Login
        </button>

        <button
          @click="goToRegister"
          class="px-6 py-3 bg-green-500 hover:bg-green-600 rounded-full font-semibold transition"
        >
          Sign Up
        </button>
      </div>
    </div>
  </div>
</template>
