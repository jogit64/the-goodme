<template>
  <!-- La transition permet une entrée/sortie en fondu -->
  <transition name="fade">
    <!-- On affiche la notification uniquement si visible est true -->
    <div v-if="visible" class="notification">
      {{ message }}
    </div>
  </transition>
</template>

<script setup>
import { ref, watch } from 'vue'

// Props
const props = defineProps({
  message: String,
  duration: { type: Number, default: 3000 },
  trigger: Number, // 🔑 clé de changement pour relancer l'affichage
})

// Contrôle de visibilité
const visible = ref(false)

// Watch sur trigger → chaque changement relance l'affichage
watch(
  () => props.trigger,
  () => {
    if (props.message) {
      visible.value = true
      setTimeout(() => {
        visible.value = false
      }, props.duration)
    }
  },
)
</script>

<style scoped>
/* Style de la notification */
.notification {
  position: fixed;
  bottom: 20px;
  left: 50%;
  transform: translateX(-50%);
  background: #4caf50;
  color: white;
  padding: 1rem 1.5rem;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
  z-index: 1000;
  font-weight: bold;
}

/* Animation de fondu */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
