<template>
  <p class="titre-progress-card">Ta progression du jour</p>
  <transition-group name="fade-slide" tag="div" class="category-progress">
    <div v-for="cat in categories" :key="cat.key" class="category-line">
      <div class="line-content">
        <img :src="cat.image" :alt="cat.label" class="icon" />

        <div class="progress-bar">
          <div
            v-for="(_, i) in groupedActions[cat.key] || []"
            :key="i"
            class="dot"
            :style="{ backgroundColor: cat.color }"
          />
        </div>

        <span class="count-text">
          {{ (groupedActions[cat.key] || []).length }}
        </span>
      </div>
    </div>
  </transition-group>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  actions: {
    type: Array,
    default: () => [],
  },
})

const categories = [
  {
    key: 'generosite',
    label: 'Générosité',
    image: new URL('@/assets/heart2.png', import.meta.url),
    color: '#F87171',
  },
  {
    key: 'bienetre',
    label: 'Bien-être',
    image: new URL('@/assets/bien2.png', import.meta.url),
    color: '#FBBF24',
  },
  {
    key: 'efficacite',
    label: 'Efficacité',
    image: new URL('@/assets/efficace2.png', import.meta.url),
    color: '#60A5FA',
  },
]

const groupedActions = computed(() => {
  const groups = {}
  for (const action of props.actions) {
    const cat = action.emoji
    if (!groups[cat]) groups[cat] = []
    groups[cat].push(action)
  }
  return groups
})
</script>

<style scoped>
.category-progress {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  padding: 1rem;
}

.category-line {
  width: 95%;
  background-color: rgb(248, 248, 248);
}

.line-content {
  display: flex;
  align-items: center;
  gap: 0.6rem;
}

.icon {
  width: 22px;
  height: 22px;
  flex-shrink: 0;
}

.progress-bar {
  display: flex;
  flex-wrap: wrap;
  flex-grow: 1;
  padding: 0.2rem 0;
}

.dot {
  width: 14px;
  height: 14px;
  animation: pop 0.3s ease;
}

.count-text {
  font-size: 0.85rem;
  color: #fff;
  font-weight: 500;
  min-width: 24px;
  text-align: right;
}

@keyframes pop {
  0% {
    transform: scale(0.5);
    opacity: 0;
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}

/* Transition pour l’apparition des lignes */
.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.4s ease;
}

.fade-slide-enter-from {
  opacity: 0;
  transform: translateY(10px);
}

.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

.titre-progress-card {
  font-size: 1.2rem;

  text-align: center;
  color: #555;
  font-family: 'Fredoka', sans-serif;
  font-weight: 500;
  z-index: 1;
}
</style>
