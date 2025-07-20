<template>
  <div>
    <p>Counter: {{ count }}</p>
    <button @click="count++">Increment</button>
  </div>
</template>

<script setup>
import { ref } from 'vue'
const count = ref(0)
</script>

<style scoped>
button {
  margin-top: 10px;
  padding: 6px 12px;
  font-size: 16px;
}
</style>