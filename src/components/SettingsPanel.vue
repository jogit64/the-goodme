<template>
  <div class="settings-panel">
    <h2>⚙️ Paramètres</h2>
    <p class="intro">
      Ici, tu peux partager GoodMe, m’écrire ou bien réinitialiser ton historique.
    </p>

    <!-- Actions normales -->
    <div class="actions-group">
      <button class="action-button neutral" @click.prevent="shareApp">
        <Share2 class="icon" />
        <span>Partager l’app'</span>
      </button>

      <a
        class="action-button neutral"
        href="https://thegoodme.fr/contact/"
        target="_blank"
        rel="noopener noreferrer"
      >
        <Mail class="icon" />
        <span>Contact</span>
      </a>

      <a
        class="action-button neutral"
        href="https://thegoodme.fr/mentions-legales/"
        target="_blank"
        rel="noopener noreferrer"
      >
        <FileText class="icon" />
        <span>Mentions légales</span>
      </a>
    </div>

    <!-- Séparateur -->
    <div class="separator" />

    <!-- Action critique -->
    <div class="reset-group">
      <ResetButton @reset="$emit('reset')" />
    </div>
  </div>

  <!-- Deuxième séparateur -->
  <div class="separator" />

  <!-- Bouton de fermeture -->
  <div class="close-settings">
    <button class="bottom-close" @click="$emit('close')">Fermer</button>
  </div>
</template>

<script setup>
defineEmits(['reset', 'close'])

import { Share2, Mail, FileText } from 'lucide-vue-next'

import ResetButton from './ResetButton.vue'

function shareApp() {
  if (navigator.share) {
    navigator
      .share({
        title: 'The GoodMe',
        text: 'Découvre The GoodMe – une app douce pour célébrer le positif 🌱',
        url: window.location.href,
      })
      .catch((err) => {
        console.error('Partage annulé ou erreur :', err)
      })
  } else {
    alert("Le partage n'est pas disponible sur cet appareil.")
  }
}
</script>

<style scoped>
.settings-panel {
  padding: 0 1.2rem 1.5rem;
  font-family: 'Fredoka', sans-serif;
  color: var(--text-color);
}

h2 {
  text-align: center;
  font-size: 1.5rem;
  color: #555;
  font-weight: 700;
  margin-bottom: 0.5rem;
}

.intro {
  text-align: center;
  font-size: 0.95rem;
  margin-bottom: 2rem;
  color: #666;
  line-height: 1.4;
}

.actions-group,
.reset-group {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
}

.separator {
  height: 1px;
  width: 100%;
  max-width: 280px;
  background-color: #eee;
  margin: 2rem auto;
  border-radius: 2px;
}

/* === Boutons === */
.action-button {
  display: inline-flex;
  align-items: center;
  justify-content: flex-start; /* Aligne tout à gauche */
  gap: 0.6rem;
  width: 100%;
  max-width: 280px;
  min-height: 52px; /* Hauteur uniforme */
  padding: 0.6rem 1rem;
  font-size: 1rem;
  font-family: 'Fredoka', sans-serif;
  font-weight: 500;
  background: white;
  border: 1px solid #ddd;
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.2s ease;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
  text-align: left;
}

.action-button span {
  flex: 1; /* Prend tout l’espace dispo */
  text-align: left;
}

.icon {
  width: 20px;
  height: 20px;
  flex-shrink: 0; /* Ne se réduit pas */
  stroke-width: 1.8;
  color: #555;
}

/* Hover */
.action-button.neutral:hover {
  border-color: #bbb;
  background: #f8f8f8;
  color: #333;
}

/* Style lien identique au bouton */
a.action-button {
  text-decoration: none;
  color: inherit;
}

/* Bouton de fermeture */
.bottom-close {
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

.close-settings {
  display: flex;
  justify-content: center;
}
</style>
