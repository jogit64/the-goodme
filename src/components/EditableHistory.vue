<template>
  <div class="bouton-style">
    <div class="toggle" @click="show = !show">
      <span class="label">
        {{ show ? 'Fermer' : 'Éditer les actions' }}
      </span>
    </div>

    <transition name="fade">
      <div v-if="show" class="history-content">
        <ActionListGroup :actions-by-day="actionsByDay" @remove="(d, i) => $emit('remove', d, i)" />
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import ActionListGroup from './ActionListGroup.vue'

defineProps({
  actionsByDay: Object,
})

defineEmits(['remove'])

const show = ref(false)
</script>

<style scoped>
/* Wrapper global du composant */
.bouton-style {
  display: block;
  margin-bottom: 1rem;
  background: #f2fbff;
  border-radius: 1.8rem;
  padding: 0.5rem 1rem;
}

/* Bouton toggle */
.toggle {
  display: flex;
  justify-content: center;
  align-items: center;
  background: #ffffff;
  border-radius: 1.5rem;
  padding: 0.6rem 1.4rem;
  font-family: 'Fredoka', sans-serif;
  font-weight: 600;
  font-size: 0.95rem;
  color: #444;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
  cursor: pointer;
  transition: background 0.2s ease;
}

.toggle:hover {
  background: #ffe082;
}

/* Texte dans le bouton */
.label {
  text-align: center;
  font-size: 1rem;
  font-weight: bold;
  color: #444;
}

/* Contenu déroulé */
.history-content {
  padding: 0.5rem 0;
}
</style>
