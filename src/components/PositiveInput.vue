<template>
  <form @submit.prevent="submit" class="form-block">
    <p class="title">The Good Me</p>
    <p class="instruction">Choisis une action</p>

    <div class="icon-grid">
      <div
        v-for="(cat, index) in categories"
        :key="index"
        :class="['icon-choice', { selected: selected?.key === cat.key }]"
        @click="select(cat)"
      >
        <img :src="cat.image" :alt="cat.label" class="icon-img" />
        <div class="label">{{ cat.label }}</div>
      </div>
    </div>

    <input
      v-if="selected"
      v-model="input"
      type="text"
      placeholder="Quelle action positive as-tu faite ?"
      class="input"
    />

    <!-- Bouton micro uniquement si supporté -->
    <!-- Nouveau bouton image -->
    <button
      v-if="voiceSupported && selected"
      @click="startListening"
      :class="['mic-button', { listening: isListening }]"
      :disabled="isListening"
    >
      <img src="@/assets/mic-icon.png" alt="Micro" class="mic-img" />
    </button>

    <!-- Message pendant l'écoute -->
    <p v-if="isListening" class="mic-status">Parle maintenant...</p>

    <!-- Résultat de la dictée avec validation -->
    <div v-if="showConfirmation" class="speech-review">
      <p class="preview">Tu as dit :</p>
      <p class="recognized">{{ recognizedText }}</p>
      <div class="speech-actions">
        <button @click="cancelSpeech" class="speech-btn cancel">❌ Annuler</button>
        <button @click="validateSpeech" class="speech-btn confirm">✅ Valider</button>
      </div>
    </div>

    <button type="submit" class="submit-button" :disabled="!input.trim() || !selected">
      Ajouter
    </button>
  </form>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { nextTick } from 'vue'

const emit = defineEmits(['add'])

const input = ref('')
const selected = ref(null)

const categories = [
  {
    image: new URL('@/assets/heart2.png', import.meta.url).href,
    label: 'Générosité',
    key: 'generosite',
  },
  {
    image: new URL('@/assets/bien2.png', import.meta.url).href,
    label: 'Bien-être',
    key: 'bienetre',
  },
  {
    image: new URL('@/assets/efficace2.png', import.meta.url).href,
    label: 'Efficacité',
    key: 'efficacite',
  },
]

function select(cat) {
  selected.value = selected.value?.key === cat.key ? null : cat
}

function submit() {
  if (!input.value.trim() || !selected.value) return

  console.log('SUBMIT:', input.value.trim(), selected.value.key)

  emit('add', {
    text: input.value.trim(),
    emoji: selected.value.key,
  })

  input.value = ''
  selected.value = null
}

// 🎙️ Reconnaissance vocale uniquement si supportée (Chrome, Android)
const voiceSupported = ref(false)
const isListening = ref(false)
const recognizedText = ref('')
const showConfirmation = ref(false)
let recognition = null

onMounted(() => {
  const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition
  if (SpeechRecognition) {
    voiceSupported.value = true
    recognition = new SpeechRecognition()
    recognition.lang = 'fr-FR'
    recognition.interimResults = false
    recognition.maxAlternatives = 1

    recognition.onresult = (event) => {
      const transcript = event.results[0][0].transcript.trim()
      recognizedText.value = transcript
      showConfirmation.value = true
    }

    recognition.onerror = (event) => {
      console.error('Erreur vocale :', event.error)
      isListening.value = false
    }

    recognition.onend = () => {
      isListening.value = false
    }
  }
})

function startListening() {
  if (recognition) {
    isListening.value = true
    recognition.start()
  }
}

function validateSpeech() {
  input.value = recognizedText.value
  recognizedText.value = ''
  showConfirmation.value = false

  nextTick(() => {
    // Juste pour forcer une mise à jour visuelle complète
    console.log('Champ mis à jour via reconnaissance vocale')
  })
}

function cancelSpeech() {
  recognizedText.value = ''
  showConfirmation.value = false
}
</script>

<style scoped>
.form-block {
  margin-top: 0rem; /* ↓ Réduit l’espace au-dessus */
}

.title {
  font-size: 1.9rem;
  text-align: center;
  color: #555;
  font-family: 'Fredoka', sans-serif;
  font-weight: 600;
  letter-spacing: 2px;
  z-index: 1;
}
.instruction {
  font-size: 1.2rem;
  text-align: center;
  color: #555;
  font-family: 'Fredoka', sans-serif;
  font-weight: 500;
  z-index: 1;
}

.icon-grid {
  display: flex;
  justify-content: space-between;
  gap: 0.75rem;
  margin-bottom: 0rem;
  flex-wrap: wrap;
}

.icon-choice {
  flex: 1;
  text-align: center;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 1rem;
  border: 2px solid transparent;
  transition: all 0.2s ease;
  max-width: 100px;
  margin: 0 auto;
}

.icon-choice.selected {
  border-color: var(--primary, #d1efff);
  background: #d1efff;
}

.icon-img {
  width: 38px;
  height: 38px;
  object-fit: contain;
  background: transparent;
  display: block;
  margin: 0 auto 0.25rem auto; /* centré + petit espace en bas */
}

.label {
  margin-top: 0;
  font-size: 0.9rem;
  text-align: center;
  color: #555;
  font-family: 'Fredoka', sans-serif;
}

.input {
  display: block;
  width: 100%;
  box-sizing: border-box;
  padding: 0.7rem 0.9rem;
  border-radius: 1rem;
  border: 1px solid #ccc;
  margin: 1rem 0;
  font-size: 1rem;
}

/* 🎙️ Bouton micro */
.mic-button {
  margin-top: 0.6rem;
  margin-bottom: 1rem;
  background: #ffffff;
  border: 2px solid #fbd05d;
  border-radius: 50%;
  width: 54px;
  height: 54px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
  margin-left: auto;
  margin-right: auto;
  transition: transform 0.1s ease;
  padding: 0; /* important pour centrer parfaitement l'image */
}

.mic-button:active {
  transform: scale(0.95);
}

.mic-button.listening {
  background: #e3f2fd;
  animation: pulse 1.2s infinite;
  box-shadow: 0 0 0 0 rgba(49, 159, 222, 0.6);
}

@keyframes pulse {
  0% {
    box-shadow: 0 0 0 0 rgba(255, 208, 94, 0.6);
  }
  70% {
    box-shadow: 0 0 0 10px rgba(255, 208, 94, 0);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(255, 208, 94, 0);
  }
}

.mic-status {
  text-align: center;
  font-size: 0.85rem;
  color: #555;
  margin-top: 0.3rem;
  font-style: italic;
  animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-4px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.speech-review {
  margin-top: 1.1rem;
  margin-bottom: 1.5rem;
  padding: 0.8rem;
  background: #f9f9f9;
  border-radius: 1rem;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
}

.speech-actions {
  margin-top: 0.5rem;
}

/* espacement bouton Ajouter */
.submit-button {
  margin-top: 1.5rem;
}

.preview {
  font-size: 0.85rem;
  color: #555;
  font-style: italic;
  margin-bottom: 0.25rem;
}

.recognized {
  font-size: 1rem;
  font-weight: 500;
  color: #333;
  margin-bottom: 0.5rem;
}

.speech-actions {
  display: flex;
  justify-content: center;
  gap: 1rem;
}

.speech-btn {
  padding: 0.4rem 0.8rem;
  border: none;
  border-radius: 1rem;
  font-size: 0.9rem;
  cursor: pointer;
  font-family: 'Poppins', sans-serif;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.speech-btn.confirm {
  background: #a5d6a7;
  color: #1b5e20;
}

.speech-btn.cancel {
  background: #ef9a9a;
  color: #b71c1c;
}

.submit-button {
  display: block;
  margin: 0 auto;
  padding-top: 0.5rem;
  padding-bottom: 0.5rem;
  padding-left: 2rem;
  padding-right: 2rem;
  font-size: 1rem;
  background: #319fde;
  color: #ffffff;
  font-family: 'Poppins', sans-serif;
  font-weight: bold;
  border: none;
  border-radius: 1.5rem;
  cursor: pointer;
  transition: 0.2s;
}

.submit-button:disabled {
  background: #ddd; /* gris clair */
  color: #aaa;
  cursor: not-allowed;
  opacity: 0.7;
}

.submit-button:not(:disabled) {
  animation: popin 0.3s ease;
}

@keyframes popin {
  0% {
    transform: scale(0.95);
    opacity: 0.5;
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}

.mic-img {
  width: 22px;
  height: 22px;
  object-fit: contain;
}
</style>
