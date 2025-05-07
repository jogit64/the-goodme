<template>
  <button class="action-button danger" @click="confirmReset">
    <Trash2 class="icon" />
    <span>Réinitialiser</span>
  </button>
</template>

<script setup>
import { Trash2 } from 'lucide-vue-next'
const emit = defineEmits(['reset'])

function confirmReset() {
  if (confirm('Es-tu sûr de vouloir tout effacer ?')) {
    emit('reset')
  }
}
</script>

<style scoped>
.action-button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  width: 100%;
  max-width: 280px;
  padding: 0.6rem 1.2rem;
  font-size: 1rem;
  font-family: 'Fredoka', sans-serif;
  font-weight: 500;
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.2s ease;
  border: 1px solid #ddd;
  background: white;
}

.icon {
  width: 20px;
  height: 20px;
  stroke-width: 1.8;
}

/* Style rouge doux spécifique à reset */
.action-button.danger {
  background: #fff4f4;
  color: #d9534f;
  border: 1px solid #f5c6cb;
}

.action-button.danger:hover {
  background: #fcecec;
  color: #c9302c;
  border-color: #e6a9a9;
}
</style>
