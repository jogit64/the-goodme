<template>
  <div v-if="show" class="backdrop" @click.self="$emit('close')">
    <div class="drawer">
      <button class="close-button" @click="$emit('close')">✖</button>
      <h2>Mes stats</h2>

      <ul v-if="stats.total > 0">
        <li><strong>Actions aujourd’hui</strong>{{ stats.today }}</li>
        <li><strong>Jours actifs</strong>{{ stats.days }}</li>
        <li><strong>Total enregistré</strong>{{ stats.total }}</li>
      </ul>

      <!-- Message d'encouragement -->
      <p class="motivation" v-if="stats.total > 0">{{ message }}</p>
      <p v-else>Aucune action enregistrée pour l’instant.</p>

      <!-- Répartition par type d'action -->
      <ul class="category-breakdown" v-if="stats.total > 0">
        <li v-for="(count, type) in typeCounts" :key="type">
          <img :src="emojiToIcon[type]" class="icon" :alt="type" />
          <span class="label">{{ labelMap[type] }} :</span>
          <strong>{{ count }}</strong>
        </li>
      </ul>

      <!-- Graphique barres empilées -->
      <div class="chart-wrapper">
        <canvas
          v-if="stats.total > 0"
          ref="chartCanvas"
          :width="last7DaysData.length * 60"
          height="240"
        ></canvas>
      </div>

      <button class="bottom-close" @click="$emit('close')">Fermer</button>
    </div>
  </div>
</template>

<script setup>
import { onBeforeUnmount, computed, ref, watch, nextTick } from 'vue'
import Chart from 'chart.js/auto'

const props = defineProps({
  show: Boolean,
  actionsByDay: Object,
  today: String,
})

const emit = defineEmits(['close'])
const chartCanvas = ref(null)
let chartInstance = null

// Emojis + Labels
const emojiToIcon = {
  generosite: new URL('@/assets/heart2.png', import.meta.url).href,
  bienetre: new URL('@/assets/bien2.png', import.meta.url).href,
  efficacite: new URL('@/assets/efficace2.png', import.meta.url).href,
}

const labelMap = {
  generosite: 'Générosité',
  bienetre: 'Bien-être',
  efficacite: 'Efficacité',
}

// Statistiques simples
const stats = computed(() => {
  const todayActions = props.actionsByDay[props.today] || []
  const days = Object.keys(props.actionsByDay)
  const total = Object.values(props.actionsByDay).flat()
  return {
    today: todayActions.length,
    days: days.length,
    total: total.length,
  }
})

// Message personnalisé
const message = computed(() =>
  stats.value.total > 0
    ? 'Bravo pour tes efforts ! Continue comme ça ✨'
    : 'Commence dès aujourd’hui avec une action positive 🌱',
)

// Répartition par type
const typeCounts = computed(() => {
  const all = Object.values(props.actionsByDay).flat()
  return all.reduce(
    (acc, a) => {
      acc[a.emoji] = (acc[a.emoji] || 0) + 1
      return acc
    },
    { generosite: 0, bienetre: 0, efficacite: 0 },
  )
})

// Données par type sur les 7 derniers jours
const last7DaysData = computed(() => {
  const sortedDays = [...Object.keys(props.actionsByDay)].sort().slice(-30)

  return sortedDays.map((day) => {
    const actions = props.actionsByDay[day] || []
    return {
      rawDate: day,
      formattedDate: new Date(day).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short' }),
      generosite: actions.filter((a) => a.emoji === 'generosite').length,
      bienetre: actions.filter((a) => a.emoji === 'bienetre').length,
      efficacite: actions.filter((a) => a.emoji === 'efficacite').length,
    }
  })
})

// Affichage du graphique quand le drawer est visible
watch(
  () => props.show,
  (visible) => {
    if (visible) {
      nextTick(() => {
        renderChart()
      })
    } else {
      destroyChart()
    }
  },
)

function renderChart() {
  destroyChart()
  if (!chartCanvas.value || last7DaysData.value.length === 0) return

  const reversedData = [...last7DaysData.value].reverse()

  const ctx = chartCanvas.value.getContext('2d')
  chartInstance = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: reversedData.map((d) => d.formattedDate),
      datasets: [
        {
          label: 'Générosité',
          data: reversedData.map((d) => d.generosite),
          backgroundColor: '#ef5350',
        },
        {
          label: 'Bien-être',
          data: reversedData.map((d) => d.bienetre),
          backgroundColor: '#ffd54f',
        },
        {
          label: 'Efficacité',
          data: reversedData.map((d) => d.efficacite),
          backgroundColor: '#42a5f5',
        },
      ],
    },
    options: {
      responsive: false /* ⬅️ ATTENTION, ici responsive:false pour respecter la taille réelle */,
      maintainAspectRatio: false,
      plugins: {
        legend: { position: 'bottom' },
      },
      scales: {
        x: {
          stacked: true,
        },
        y: {
          stacked: true,
          beginAtZero: true,
        },
      },
    },
  })
}

function destroyChart() {
  if (chartInstance) {
    chartInstance.destroy()
    chartInstance = null
  }
}

onBeforeUnmount(() => {
  destroyChart()
})
</script>

<style scoped>
.backdrop {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.3);
  display: flex;
  justify-content: center;
  align-items: flex-end;
  z-index: 9999;
}

.drawer {
  background: white;
  width: 100%;
  max-width: 480px;
  padding: 2rem;
  border-top-left-radius: 1.5rem;
  border-top-right-radius: 1.5rem;
  box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.2);
  animation: slideUp 0.25s ease-out;
  font-family: 'Fredoka', sans-serif;
  color: #333;
  text-align: center;
  max-height: 90vh;
  overflow-y: auto;
}

@keyframes slideUp {
  from {
    transform: translateY(100%);
  }
  to {
    transform: translateY(0%);
  }
}

h2 {
  font-size: 1.5rem;
  margin-bottom: 1.5rem;
  color: #222;
}

ul {
  list-style: none;
  padding: 0;
  margin: 0 0 1.5rem;
}

li {
  font-size: 1rem;
  margin-bottom: 1rem;
  background: #f9f9f9;
  padding: 0.8rem 1.2rem;
  border-radius: 1rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
  display: flex;
  align-items: center;
  gap: 0.5rem;
  justify-content: flex-start;
}

.icon {
  width: 24px;
  height: 24px;
  object-fit: contain;
}

li strong {
  font-size: 1rem;
  margin-left: auto;
  color: #444;
}

.label {
  flex: 1;
  text-align: left;
  color: #666;
}

.motivation {
  font-size: 0.9rem;
  font-style: italic;
  margin-bottom: 1rem;
  color: #666;
}

.close-button {
  position: absolute;
  top: 1rem;
  right: 1rem;
  background: none;
  border: none;
  font-size: 1.4rem;
  cursor: pointer;
  color: #888;
}

.bottom-close {
  margin-top: 2rem;
  padding: 0.8rem 1.4rem;
  background-color: #ffd54f;
  border: none;
  border-radius: 1rem;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  color: #333;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  transition: background-color 0.2s ease;
}

.bottom-close:hover {
  background-color: #ffca28;
}

.chart-wrapper {
  overflow-x: auto;
  margin-bottom: 1.5rem;
  width: 100%;
}

canvas {
  display: block;
  min-width: 100%;
}
</style>
