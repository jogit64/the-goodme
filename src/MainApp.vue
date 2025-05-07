<template>
  <div class="app-wrapper">
    <!-- -------------------------------------- -->
    <!-- BAN HAUT IMG                 -->
    <!-- -------------------------------------- -->

    <div class="banner-container">
      <img src="@/assets/combined-visual11.jpg" alt="The GoodMe" class="banner-img" />
    </div>

    <!-- -------------------------------------- -->
    <!-- CARD MESS ALERT SI VID + CHOIX ACTION AJOUTER EDIT -->
    <!-- -------------------------------------- -->

    <Card>
      <div v-if="!limitedTodayActions.length && !hasAnyAction" class="empty">
        ✨ Rien encore aujourd’hui... Et si tu notais un petit geste positif ?
      </div>

      <PositiveInput @add="addAction" />
    </Card>

    <!-- -------------------------------------- -->
    <!-- DIV LIGNE BAR PROGRESSION & SCORE DAYS        -->
    <!-- -------------------------------------- -->

    <div class="progress-wrapper">
      <ProgressToday :actions="limitedTodayActions" />
      <ScoreDayScroll :actions-by-day="actionsByDay" />
    </div>

    <!-- -------------------------------------- -->
    <!-- HISTORIQUE MODIFIABLE (actionListGroup) -->
    <!-- -------------------------------------- -->
    <EditableHistory :actions-by-day="actionsByDay" @remove="removeAction" />

    <!-- -------------------------------------- -->
    <!-- BOUTON BOOST ME                        -->
    <!-- -------------------------------------- -->

    <BoostMe :actions-by-day="actionsByDay" />

    <!-- -------------------------------------- -->
    <!-- MODAL STATS    -->
    <!-- -------------------------------------- -->

    <StatsModal
      :show="drawerOpen === 'stats'"
      :actions-by-day="actionsByDay"
      :today="today"
      @close="drawerOpen = null"
    />

    <!-- -------------------------------------- -->
    <!-- PANNEAU DROIT RESET                    -->
    <!-- -------------------------------------- -->

    <RightDrawer :show="drawerOpen === 'reset'" @close="drawerOpen = null">
      <SettingsPanel @reset="resetActions" @close="drawerOpen = null" />
    </RightDrawer>

    <!-- -------------------------------------- -->
    <!-- MENU BAS                               -->
    <!-- -------------------------------------- -->

    <BottomNav @open="(section) => (drawerOpen = section)" />

    <!-- -------------------------------------- -->
    <!-- NOTIF BRAVO                            -->
    <!-- -------------------------------------- -->

    <Notification :message="notificationMessage" :trigger="notificationTrigger" />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

import PositiveInput from './components/PositiveInput.vue'

import ProgressToday from './components/ProgressToday.vue'

import SettingsPanel from './components/SettingsPanel.vue'
import Card from './components/Card.vue'
import Notification from './components/Notification.vue'
import BottomNav from './components/BottomNav.vue'
import RightDrawer from './components/RightDrawer.vue'
import ScoreDayScroll from './components/ScoreDayScroll.vue'
import EditableHistory from './components/EditableHistory.vue'
import BoostMe from './components/BoostMe.vue'
import StatsModal from './components/StatsModal.vue'

const STORAGE_KEY = 'goodme-actions'
const actionsByDay = ref({})
const today = new Date().toISOString().split('T')[0]
const drawerOpen = ref(null)

const notificationMessage = ref('')
const notificationTrigger = ref(0)
const showHistory = ref(false)

function saveLimitedActions() {
  // Trie les dates par ordre décroissant
  const sortedEntries = Object.entries(actionsByDay.value).sort((a, b) => b[0].localeCompare(a[0]))

  // Garde uniquement les 31 derniers jours
  const limitedEntries = sortedEntries.slice(0, 31)

  // Reformate en objet
  const limitedActionsByDay = Object.fromEntries(limitedEntries)

  // Sauvegarde dans le localStorage
  localStorage.setItem(STORAGE_KEY, JSON.stringify(limitedActionsByDay))

  // Met à jour la valeur réactive
  actionsByDay.value = limitedActionsByDay
}

// ➕ Ajouter une action
function addAction(action) {
  if (!actionsByDay.value[today]) {
    actionsByDay.value[today] = []
  }
  actionsByDay.value[today].push(action)
  saveLimitedActions()

  notificationMessage.value = 'Bravo pour cette nouvelle action positive !'
  notificationTrigger.value = Math.random()
}

// ❌ Supprimer une action par jour et index
function removeAction(date, index) {
  if (!actionsByDay.value[date]) return

  actionsByDay.value[date].splice(index, 1)

  // 🧹 Si plus aucune action ce jour-là, supprime carrément la clé
  if (actionsByDay.value[date].length === 0) {
    delete actionsByDay.value[date]
  }

  // 🔄 Ensuite, on limite aux 31 derniers jours comme prévu
  saveLimitedActions()
}

// 🧹 Réinitialiser toutes les actions
function resetActions() {
  if (confirm('Es-tu sûr de vouloir tout effacer ?')) {
    actionsByDay.value = {}
    localStorage.removeItem(STORAGE_KEY)
  }
}

// 💾 Chargement initial
onMounted(() => {
  const data = localStorage.getItem(STORAGE_KEY)
  if (data) {
    actionsByDay.value = JSON.parse(data)
  }
})

// Actions du jour (filtrées)
const limitedTodayActions = computed(() => {
  return actionsByDay.value[today] || []
})

// Vérifie s'il y a au moins une action enregistrée un jour quelconque
const hasAnyAction = computed(() => {
  return Object.keys(actionsByDay.value).some((date) => actionsByDay.value[date]?.length > 0)
})
</script>

<style>
:root {
  --bg-color: #281846;
  --text-color: #333;
  --card-bg: #ffffff;
  --card-radius: 1rem;
  --primary: #4caf50;
  --secondary: #2196f3;
  --danger: #f44336;
  --muted: #9e9e9e;
  --shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
}

body {
  background: var(--bg-color);
  color: var(--text-color);
  margin: 0;
  font-family: 'Inter', system-ui, sans-serif;
}

.app-wrapper {
  max-width: 600px;
  margin: 0 auto;
  padding: 0 1rem;
  padding-bottom: 5rem; /* espace pour le menu fixe en bas */
  z-index: 0;
}

.banner-container {
  width: 100%;
  overflow: hidden;
  border-radius: 15px;

  margin-top: 10px;
  z-index: 0;
}

.banner-img {
  width: 100%;
  display: block;
  object-fit: cover;
  z-index: 0;
}

.empty {
  background: #fff3cd;
  color: #856404;
  padding: 1rem;
  border-radius: 0.75rem;
  font-style: italic;
  border: 1px solid #ffeeba;
  text-align: center;
}

.progress-wrapper {
  width: 100%;
  padding: 1rem 1.5rem;
  margin-bottom: 1rem;
  background-color: #ffffff;
  border-radius: 1rem;
  box-shadow: var(--shadow);
  box-sizing: border-box;
}

@media screen and (max-width: 480px) {
  .app-wrapper {
    padding: 1rem 0.75rem;
  }
}
</style>
