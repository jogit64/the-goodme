<template>
  <div>
    <div v-if="showSplash" class="splash-screen">
      <img src="@/assets/favicon.png" alt="Logo The Good Me" class="splash-logo" />
      <p class="titre">Bienvenue sur The Good Me 🌱</p>
      <p class="tagline">Une application pour célébrer tes gestes positifs au quotidien.</p>

      <p v-if="installPrompt">📲 Tu peux installer l'application pour une expérience complète !</p>
      <p v-else>✅ L'application est déjà installée sur ton appareil.</p>

      <button v-if="installPrompt" @click="installApp" class="btn">
        📲 Installer l'application
      </button>

      <p class="web-link" @click="launchApp">Continuer en mode navigateur</p>

      <p class="storage-warning">
        ℹ️ Les données en mode web et en mode app ne sont pas partagées : ce sont deux espaces
        séparés.
      </p>
    </div>

    <MainApp v-if="appReady" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import MainApp from './MainApp.vue'

const appReady = ref(false)
const installPrompt = ref(null)
const showSplash = ref(true)
const isStandalone = ref(false)
/* const showStorageWarning = ref(false) */

function launchApp() {
  showSplash.value = false
  appReady.value = true
}

function installApp() {
  if (installPrompt.value) {
    installPrompt.value.prompt()
    installPrompt.value.userChoice
      .then((choiceResult) => {
        if (choiceResult?.outcome === 'accepted') {
          installPrompt.value = null
        }
      })
      .catch(() => {})
  }
}

function checkStandalone() {
  return (
    window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true
  )
}

onMounted(() => {
  const urlParams = new URLSearchParams(window.location.search)
  const forceWeb = urlParams.get('web') === 'true'

  isStandalone.value = checkStandalone()

  if (isStandalone.value || forceWeb) {
    launchApp()
    return
  }

  showSplash.value = true

  // Catch du prompt d’installation
  window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault()
    installPrompt.value = e
  })

  // Si aucun prompt d'install n’est reçu après 1 seconde → peut-être déjà installée
  /* setTimeout(() => {
    if (!installPrompt.value && !checkStandalone()) {
      showStorageWarning.value = true
    }
  }, 1000)
 */
  // Si app installée pendant l’usage
  /* window.addEventListener('appinstalled', () => {
    installPrompt.value = null
    showStorageWarning.value = false
  }) */
})
</script>

<style scoped>
.splash-screen {
  min-height: 80vh;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
  padding: 2rem;
}

.splash-logo {
  max-width: 85px;
  margin-bottom: 1rem;
  margin-top: 2rem;
}

.titre {
  font-size: 1.8rem;
  margin-bottom: 0.5rem;
  color: #333;
  font-family: 'Fredoka', sans-serif;
  font-weight: bold;
}

.tagline {
  font-size: 1rem;
  margin-bottom: 1.5rem;
  color: #555;
  max-width: 300px;
}

.btn {
  background: #ffd54f;
  border: none;
  border-radius: 1rem;
  padding: 0.8rem 1.6rem;
  font-size: 1rem;
  margin: 0.5rem;
  cursor: pointer;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

.web-link {
  font-size: 0.9rem;
  margin-top: 1.2rem;
  text-decoration: underline;
  color: #555;
  cursor: pointer;
}

.storage-warning {
  font-size: 0.75rem;
  color: #666;
  margin-top: 0.75rem;
  text-align: center;
  max-width: 280px;
}
</style>
