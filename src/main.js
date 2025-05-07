import { createApp } from 'vue'
import App from './App.vue' // ✅ On lance maintenant App.vue, qui contient le splash + MainApp ensuite
import './assets/style.css'

createApp(App).mount('#app')
