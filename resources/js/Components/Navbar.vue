<!-- resources/js/Components/Navbar.vue -->
<template>
  <nav class="bg-white shadow sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <!-- Logo -->
        <div class="flex-shrink-0 flex items-center">
          <span class="font-bold text-xl">🌍 eTraveller</span>
        </div>

        <!-- Hamburger button (Mobile) -->
        <div class="flex items-center md:hidden">
          <button @click="toggleMobileMenu" type="button" class="text-gray-700 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                :d="mobileMenuOpen ? 'M6 18L18 6M6 6l12 12' : 'M4 6h16M4 12h16M4 18h16'"
              />
            </svg>
          </button>
        </div>

        <!-- Desktop menu -->
        <div class="hidden md:flex space-x-6 items-center">
          <NavLinks />
        </div>
      </div>
    </div>

    <!-- Mobile menu -->
    <div v-if="mobileMenuOpen" class="md:hidden px-4 pb-4">
      <NavLinks vertical />
    </div>
  </nav>
</template>

<script setup>
import { ref } from 'vue'
import NavLinks from './NavLinks.vue'

const mobileMenuOpen = ref(false)
function toggleMobileMenu() {
  mobileMenuOpen.value = !mobileMenuOpen.value
}
</script>
