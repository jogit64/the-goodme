<template>
  <div class="score-days-scroll">
    <div class="score-slide" v-for="(day, i) in limitedDays" :key="day.date">
      <div class="score-content">
        <!-- Icône ou image à gauche -->
        <img src="@/assets/score1.png" alt="Score" class="score-icon" />

        <!-- Texte date + score -->
        <div class="score-text">
          <span class="score-date">{{ formatDate(day.date) }}</span>
          <span class="score-points">{{ computeScore(day.actions) }} pts</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  actionsByDay: Object,
})

// Limiter aux 10 derniers jours
const limitedDays = computed(() => {
  return Object.entries(props.actionsByDay)
    .sort((a, b) => b[0].localeCompare(a[0]))
    .slice(0, 30)
    .map(([date, actions]) => ({ date, actions }))
})

// Formater la date
function formatDate(date) {
  const today = new Date().toISOString().split('T')[0]
  const yesterday = new Date()
  yesterday.setDate(yesterday.getDate() - 1)
  if (date === today) return 'Auj.'
  if (date === yesterday.toISOString().split('T')[0]) return 'Hier'
  return new Date(date).toLocaleDateString('fr-FR', {
    weekday: 'short',
  })
}

// Nouveau calcul pondéré : + d’actions = plus de points par action
function computeScore(actions) {
  const base = 10
  const bonusFactor = 1 + (actions.length - 1) * 0.2 // chaque action ajoute 20% de bonus
  return Math.round(actions.length * base * bonusFactor)
}
</script>

<style scoped>
.score-days-scroll {
  display: flex;
  overflow-x: auto;
  scroll-snap-type: x mandatory;
  gap: 0.75rem;

  font-family: 'Poppins', sans-serif;
  color: #202020;
}

.score-slide {
  flex: 0 0 auto;
  width: 180px;
  height: 55px;
  background: #cfedfd;
  box-shadow:
    0 4px 8px rgba(0, 0, 0, 0.12),
    0 8px 16px rgba(0, 0, 0, 0.18);

  border-radius: 1rem;
  scroll-snap-align: start;
  box-shadow: var(--shadow);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0.5rem 1rem;
}

.score-content {
  display: flex;
  align-items: center;
  justify-content: start;
  flex-direction: row; /* assure l’affichage en ligne */
  flex-wrap: nowrap; /* ⚠️ évite que ça passe à la ligne */
  gap: 0.5rem;
  width: 100%;
}

.score-icon {
  width: 25px;
  height: 25px;
  border: 2px;
  border-color: red;
}

.score-text {
  display: flex;
  flex-direction: row; /* affiche date + score côte à côte */
  gap: 0.5rem;
  align-items: center;
}

.score-date {
  font-size: 0.9rem;
  color: #555;
  font-weight: 500;
  font-family: 'Poppins', sans-serif;
  color: var(--textcolor);
}

.score-points {
  font-size: 1rem;
  font-weight: bold;
  color: var(--textcolor);
  font-family: 'Poppins', sans-serif;
}
</style>
