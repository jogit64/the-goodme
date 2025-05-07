<template>
  <section class="list-block">
    <ul class="list">
      <li v-for="(action, reversedIndex) in reversedActions" :key="reversedIndex" class="item">
        <img :src="emojiToIcon[action.emoji]" class="icon" :alt="action.emoji" />
        <span class="text">{{ action.text }}</span>
        <button @click="removeOriginalIndex(reversedIndex)" class="delete">✖</button>
      </li>
    </ul>
  </section>
</template>

<script setup>
const props = defineProps({
  actions: Array,
})

const emit = defineEmits(['remove'])

const emojiToIcon = {
  generosite: new URL('@/assets/heart2.png', import.meta.url).href,
  bienetre: new URL('@/assets/bien2.png', import.meta.url).href,
  efficacite: new URL('@/assets/efficace2.png', import.meta.url).href,
}

import { computed } from 'vue'

const reversedActions = computed(() => [...props.actions].reverse())

function removeOriginalIndex(reversedIndex) {
  const originalIndex = props.actions.length - 1 - reversedIndex
  emit('remove', originalIndex)
}
</script>

<style scoped>
/* Conteneur principal */
.list-block {
  padding: 0;
  background: transparent;
}

/* Liste d’actions */
.list {
  list-style: none;
  padding: 0;
  margin: 0;
  width: 100%;
  box-sizing: border-box;
}

/* Élément individuel */
.item {
  background: #ffffff;
  padding: 0.6rem 0.8rem;
  border-radius: 1rem;
  margin-bottom: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.5rem;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
  transition: background 0.2s ease;
  word-break: break-word;
}

.item:hover {
  background: #fdf9ec;
}

/* Icône */
.icon {
  width: 26px;
  height: 26px;
  border-radius: 0.5rem;
  object-fit: contain;
}

/* Texte */
.text {
  flex: 1;
  font-size: 0.8rem;
  color: #333;
  font-family: 'Poppins', sans-serif;
  overflow-wrap: break-word;
}

/* Bouton suppression */
.delete {
  background: none;
  border: none;
  color: #c62828;
  font-size: 1.1rem;
  cursor: pointer;
  padding: 0.2rem;
  transition: transform 0.2s ease;
}

.delete:hover {
  transform: scale(1.15);
}
</style>
