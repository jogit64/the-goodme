<template>
  <nav class="bottom-nav">
    <button @click="$emit('open', 'stats')" class="nav-btn">
      <BarChart2 class="icon" />
      <span class="label">Stats</span>
    </button>
    <button @click="$emit('open', 'reset')" class="nav-btn">
      <Settings class="icon" />
      <span class="label">Param</span>
    </button>
  </nav>
</template>

<script setup>
import { BarChart2, Settings } from 'lucide-vue-next'

defineEmits(['open'])
</script>

<style scoped>
.bottom-nav {
  position: fixed;
  bottom: 0;
  left: 0;
  width: 100%;
  background: #ffffff;
  display: flex;
  justify-content: space-evenly;
  align-items: center;
  padding: 0.6rem 1rem;
  border-top-left-radius: 1.2rem;
  border-top-right-radius: 1.2rem;
  box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.06);
  z-index: 100;
}

.nav-btn {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  background: #f9f9f9;
  border: none;
  border-radius: 1rem;
  width: 56px;
  height: 56px;
  padding: 0.3rem;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
  font-family: 'Fredoka', sans-serif;
  transition: all 0.2s ease;
  color: #444;
}

.nav-btn:hover {
  transform: scale(1.07);
  background: #ffe082;
}

.icon {
  width: 20px;
  height: 20px;
  stroke-width: 1.5;
  margin-bottom: 0.15rem;
  color: #444;
}

.label {
  font-size: 0.65rem;
  font-weight: 500;
  color: #444;
}
</style>
