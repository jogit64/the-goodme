<template>
  <div class="boost-card-wrapper">
    <div class="boost-inline">
      <img src="@/assets/boosthead2.png" alt="Boost" class="boost-icon" />
      <div class="boost-text">
        <p class="boost-label">Besoin d’un coup de boost ?</p>
        <span class="alinea">(3 appels max. / jour)</span>
      </div>
    </div>
    <button class="boost-button" @click="triggerBoost">
      Boost
      <span class="boost-counter" :class="{ danger: remainingBoosts <= 0 }">
        ({{ remainingBoosts }})
      </span>
    </button>

    <!-- Overlay pleine page -->
    <div v-if="showOverlay" class="boost-overlay" @click.self="closeOverlay">
      <div class="boost-message">
        <p v-if="loading">Chargement d’un mot de soutien...</p>
        <p v-else-if="error">Erreur : {{ error }}</p>
        <div class="boost-message-box" v-else>
          {{ message }}
        </div>
        <button class="close-btn" @click="closeOverlay">Fermer</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

// ✅ Déclare les props
const props = defineProps({
  actionsByDay: Object,
})

const showOverlay = ref(false)
const loading = ref(false)
const message = ref('')
const error = ref(null)

const MAX_DAILY_BOOSTS = 3

function getTodayKey() {
  const today = new Date().toISOString().split('T')[0]
  return `boost_count_${today}`
}

function getBoostCount() {
  const key = getTodayKey()
  return parseInt(localStorage.getItem(key) || '0', 10)
}

function incrementBoostCount() {
  const key = getTodayKey()
  const current = getBoostCount()
  const newCount = current + 1
  localStorage.setItem(key, newCount)
  boostCount.value = newCount // ✅ MAJ réactive
}

// ✅ Bien déclaré ici, à l'extérieur de triggerBoost

const boostCount = ref(getBoostCount())

const remainingBoosts = computed(() => {
  return MAX_DAILY_BOOSTS - boostCount.value
})

function closeOverlay() {
  showOverlay.value = false
  message.value = ''
  error.value = null
}

function triggerBoost() {
  const count = getBoostCount()
  if (count >= MAX_DAILY_BOOSTS) {
    showOverlay.value = true
    message.value = `Tu as atteint ta limite de ${MAX_DAILY_BOOSTS} boosts pour aujourd’hui 🚫`
    return
  }

  showOverlay.value = true
  loading.value = true
  error.value = null

  const sortedDates = Object.keys(props.actionsByDay).sort().reverse()
  const allActions = sortedDates.flatMap((date) => props.actionsByDay[date])
  const lastTexts = allActions.slice(0, 5).map((a) => a.text)

  if (!lastTexts.length) {
    message.value =
      'Tu n’as encore rien noté… mais il n’est jamais trop tard pour semer du positif 🌱'
    loading.value = false
    return
  }

  fetch(import.meta.env.VITE_API_URL, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ actions: lastTexts }),
  })
    .then((res) => res.json())
    .then((data) => {
      message.value = data.choices?.[0]?.message?.content || 'Un message arrive bientôt...'
      incrementBoostCount()
    })
    .catch((err) => {
      error.value = 'Une erreur est survenue.'
      console.error(err)
    })
    .finally(() => {
      loading.value = false
    })
}
</script>

<style scoped>
.boost-card-wrapper {
  margin: 1rem 0rem 4.5rem 0rem;
  text-align: center;
  background-color: #ffd54f;
  border-radius: 20px;
  padding: 1rem;
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
}

/* Nouvelle structure plus compacte */
.boost-me-wrapper {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.6rem;
}

.boost-inline {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.6rem;
}

.boost-image,
.boost-icon {
  width: 36px;
  height: 36px;
}

.boost-text {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}

.boost-label {
  font-size: 1.1rem;
  color: #333;
  font-family: 'Fredoka', sans-serif;
  font-weight: 600;
  margin: 0;
}

.alinea {
  font-size: 0.75rem;
  color: #555;
  font-family: 'Fredoka', sans-serif;
  font-weight: 400;
}

.boost-button {
  font-size: 0.95rem;
  padding: 0.5rem 1.2rem;
  background: linear-gradient(145deg, #ffe082, #ffd54f);
  border: none;
  border-radius: 1.5rem;
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
  cursor: pointer;
  font-family: 'Fredoka', sans-serif;
  font-weight: 600;
  transition: transform 0.2s ease;
  margin-top: 0.4rem;
}

.boost-button:hover {
  transform: scale(1.07);
}

.boost-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(8px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

.boost-message {
  background: white;
  padding: 2rem;
  border-radius: 1rem;
  max-width: 90%;
  width: 320px;
  text-align: center;
  font-size: 1.05rem;
  line-height: 1.5;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
  max-height: 80vh;
  overflow-y: auto;
}

.boost-message-box {
  max-height: 200px;
  overflow-y: auto;
  line-height: 1.5;
  font-size: 0.96rem;
  white-space: pre-line;
  margin-top: 1rem;
}

.close-btn {
  margin-top: 1.5rem;
  padding: 0.5rem 1.2rem;
  background: #ffe082;
  border: none;
  border-radius: 0.5rem;
  font-weight: 600;
  cursor: pointer;
}

.boost-counter {
  margin-left: 0.5rem;
  font-size: 0.8rem;
  font-weight: 500;
  color: #333;
}

.boost-counter.danger {
  color: red;
  font-weight: 700;
}
</style>
