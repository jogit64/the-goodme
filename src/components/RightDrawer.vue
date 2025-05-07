<template>
  <transition name="slide-in-right">
    <div v-if="show" class="drawer-wrapper" @click.self="close">
      <div class="drawer">
        <button class="close" @click="close" aria-label="Fermer le panneau">✖</button>
        <slot />
      </div>
    </div>
  </transition>
</template>

<script setup>
defineProps({ show: Boolean })
const emit = defineEmits(['close'])

function close() {
  emit('close')
}
</script>

<style scoped>
/* --- Overlay sombre derrière le panneau --- */
.drawer-wrapper {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.3);
  display: flex;
  justify-content: flex-end;
  z-index: 1000;
}

/* --- Panneau latéral --- */
.drawer {
  background: white;
  width: 85%;
  max-width: 320px;
  height: 100%;
  padding: 1.5rem;
  box-shadow: -4px 0 12px rgba(0, 0, 0, 0.2);
  overflow-y: auto;
  position: relative;
}

/* --- Bouton fermer --- */
.close {
  position: absolute;
  top: 1rem;
  right: 1rem;
  border: none;
  background: none;
  font-size: 1.2rem;
  cursor: pointer;
  color: #555;
}

/* --- Transition fluide depuis la droite --- */
.slide-in-right-enter-active,
.slide-in-right-leave-active {
  transition: all 0.3s ease;
}

.slide-in-right-enter-from,
.slide-in-right-leave-to {
  opacity: 0;
  transform: translateX(100%);
}

.slide-in-right-enter-to,
.slide-in-right-leave-from {
  opacity: 1;
  transform: translateX(0);
}
</style>
