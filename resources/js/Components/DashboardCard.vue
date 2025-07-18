<template>
  <!-- POST/DELETE button -->
  <form
    v-if="as.toLowerCase() === 'button'"
    :action="link"
    method="post"
    @submit.prevent="submitAction"
    class="w-full"
  >
    <button
      type="submit"
      class="w-full rounded-2xl shadow-xl p-6 text-white transform transition hover:scale-105 cursor-pointer text-center"
      :class="color"
    >
      <div class="text-4xl mb-2">{{ icon }}</div>
      <div class="text-lg font-semibold tracking-wide">{{ title }}</div>
    </button>
  </form>

  <!-- Navigation Link -->
  <Link
    v-else
    :href="link"
    class="block rounded-2xl shadow-xl p-6 text-white transform transition hover:scale-105 cursor-pointer text-center"
    :class="color"
  >
    <div class="text-4xl mb-2">{{ icon }}</div>
    <div class="text-lg font-semibold tracking-wide">{{ title }}</div>
  </Link>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'

const props = defineProps({
  icon: String,
  title: String,
  link: String,
  color: String,
  method: {
    type: String,
    default: 'get'
  },
  as: {
    type: String,
    default: 'Link' // Can be 'Link' or 'button'
  }
})

// Handle form submission with router method (POST, DELETE, etc.)
function submitAction() {
  router.visit(props.link, { method: props.method })
}
</script>
