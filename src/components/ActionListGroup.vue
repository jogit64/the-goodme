<template>
  <div class="action-days-scroll">
    <!-- Pour chaque jour (au plus 10), on affiche une "carte" -->
    <div class="day-slide" v-for="(day, i) in limitedDays" :key="day.date">
      <h3 class="day-label">{{ formatDate(day.date) }}</h3>

      <!-- Liste verticale avec scroll si plus de 5 actions -->
      <div class="action-list-container">
        <ActionList :actions="day.actions" @remove="(i) => emit('remove', day.date, i)" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import ActionList from './ActionList.vue'

const props = defineProps({
  actionsByDay: Object,
})

const emit = defineEmits(['remove'])

const limitedDays = computed(() => {
  return Object.entries(props.actionsByDay)
    .sort((a, b) => b[0].localeCompare(a[0]))
    .slice(0, 10)
    .map(([date, actions]) => ({ date, actions }))
})

function formatDate(date) {
  const today = new Date().toISOString().split('T')[0]
  if (date === today) return 'Aujourd’hui'
  const yesterday = new Date()
  yesterday.setDate(yesterday.getDate() - 1)
  if (date === yesterday.toISOString().split('T')[0]) return 'Hier'
  return new Date(date).toLocaleDateString('fr-FR', {
    weekday: 'long',
    day: 'numeric',
    month: 'short',
  })
}
</script>

<style scoped>
/* Conteneur principal : scroll horizontal entre les jours */
.action-days-scroll {
  display: flex;
  overflow-x: auto;
  scroll-snap-type: x mandatory;
  gap: 1rem;
  padding: 1rem;
  box-sizing: border-box;
  max-width: 100vw;
}

/* Carte par jour */
.day-slide {
  flex: 0 0 auto;
  width: 100%;
  max-width: 320px;
  scroll-snap-align: start;
  background: #ffffff;
  border-radius: 1.2rem;
  padding: 1rem;
  display: flex;
  flex-direction: column;
  box-sizing: border-box;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
}

/* Titre du jour */
.day-label {
  font-weight: 600;
  font-size: 0.95rem;
  color: #444;
  background: #f2fbff;
  padding: 0.25rem 0.6rem;
  border-radius: 0.75rem;
  text-align: center;
  margin: 0 auto 0.8rem auto;
  font-family: 'Fredoka', sans-serif;
}

/* Conteneur scroll vertical interne si trop d’actions */
.action-list-container {
  max-height: 240px;
  overflow-y: auto;
  padding-right: 0.25rem;
}
</style>
