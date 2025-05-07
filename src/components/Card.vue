<template>
  <section :class="['card', variantClass]">
    <slot />
  </section>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  title: String,
  variant: {
    type: String,
    default: 'default', // "default", "pastel", "score", etc.
  },
})

const variantClass = computed(() => {
  return {
    pastel: props.variant === 'pastel',
    score: props.variant === 'score',
    warning: props.variant === 'warning',
  }
})
</script>

<style scoped>
.card {
  background: var(--card-bg);
  border-radius: var(--card-radius);
  box-shadow: var(--shadow);
  padding: 0.5rem 1.5rem;
  margin-bottom: 0.5rem;
  z-index: 999;
}

/* Variante pastel */
.pastel {
  background: #f1f8e9;
}

/* Variante score */
.score {
  background: #46aae6; /* bleu clair doux */
}

/* Variante warning */
.warning {
  background: #fff3cd;
}
</style>
