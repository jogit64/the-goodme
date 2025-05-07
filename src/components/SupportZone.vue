<template>
  <div class="support-zone">
    <!-- Bouton principal -->
    <button class="doubt-button" @click="showSupport = !showSupport">🧠</button>
    <p class="doubt-label">Je doute ?</p>

    <!-- Affichage des actions positives récentes -->
    <div v-if="showSupport && supportMessages.length" class="support-box">
      <p>Voici des moments positifs récents :</p>
      <ul>
        <li v-for="(msg, index) in supportMessages" :key="index">
          <img :src="emojiToIcon[msg.emoji]" class="icon" :alt="msg.emoji" />
          {{ msg.text }}
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  actionsByDay: Object,
})

const showSupport = ref(false)

const emojiToIcon = {
  generosite: new URL('@/assets/heart1.png', import.meta.url).href,
  efficacite: new URL('@/assets/efficace1.png', import.meta.url).href,
  bienetre: new URL('@/assets/bien1.png', import.meta.url).href,
}

// 🔁 Récupère les 10 actions les plus récentes (3 jours max)
const supportMessages = computed(() => {
  const allDates = Object.keys(props.actionsByDay).sort().reverse()
  const recentDates = allDates.slice(0, 3)
  const recentActions = recentDates.flatMap((date) => props.actionsByDay[date])
  return recentActions.sort(() => 0.5 - Math.random()).slice(0, 30)
})
</script>

<style scoped>
.support-zone {
  margin-top: 2rem;
  margin-bottom: 5rem;
  text-align: center;
  background-color: aqua;
}

.doubt-button {
  font-size: 2.2rem;
  background: #e1f5fe;
  border: none;
  border-radius: 50%;
  width: 64px;
  height: 64px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  cursor: pointer;
  transition: transform 0.2s ease;
}

.doubt-button:hover {
  transform: scale(1.05);
}

.doubt-label {
  margin-top: 0.5rem;
  font-size: 0.95rem;
  color: #555;
  font-weight: 500;
}

.support-box {
  margin-top: 1rem;
  background: #e3f2fd;
  padding: 1rem;
  border-radius: 1rem;
  text-align: left;
  max-width: 90%;
  margin-inline: auto;
}

.support-box ul {
  list-style: none;
  padding-left: 0;
  margin: 0;
}

.support-box li {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 0.75rem;
}

.icon {
  width: 19px;
  height: 19px;
  border-radius: 0.5rem;
  object-fit: cover;
}
</style>
