<template>
  <button :class="['base-button', variant]">
    <slot />
  </button>
</template>

<script setup>
defineProps({
  variant: {
    type: String,
    default: 'primary', // primary / secondary / danger
  },
})
</script>

<style scoped>
.base-button {
  width: 100%;
  padding: 0.75rem;
  font-size: 1rem;
  border: none;
  border-radius: 0.7rem;
  cursor: pointer;
  transition: 0.2s;
  margin-bottom: 1rem;
}

.primary {
  background: #4caf50;
  color: white;
}

.secondary {
  background: #2196f3;
  color: white;
}

.danger {
  background: #f44336;
  color: white;
}
</style>
