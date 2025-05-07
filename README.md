# 🌱 The GoodMe

**The GoodMe** est une application web bienveillante, conçue en Vue.js, qui aide à suivre et célébrer ses actions positives du quotidien.  
Pensée pour une expérience mobile-first, elle fonctionne comme une PWA et offre un design chaleureux et motivant.

---

## 🎯 Objectif du projet

Ce projet a été réalisé pour démontrer mes compétences en **Vue.js 3**, **Vite**, **Pinia**, et **l’intégration d’API externe (OpenAI)**.  
L’interface est pensée pour le mobile, et l’application peut être installée comme une vraie app via PWA.

---

## 🧰 Stack technique

- **Vue 3** — composition API, `<script setup>`
- **Vite** — bundler ultra rapide
- **Pinia** — gestion d’état moderne
- **Chart.js** — visualisation des données utilisateur
- **OpenAI API** — génération de messages motivants
- **Vite PWA Plugin** — installation mobile
- **ESLint + Prettier** — qualité du code
- **CSS scoped + animations** — UI soignée

---

## 🖥️ Démo en ligne

- Site vitrine : [https://thegoodme.fr](https://thegoodme.fr)
- App (PWA) : [https://app.thegoodme.fr](https://app.thegoodme.fr)

Sur mobile, l’utilisateur peut choisir d’installer l’application ou de continuer dans son navigateur.

---

## 🗂️ Structure du projet

- `src/` : Composants Vue organisés par usage (navigation, actions, stats…)
- `public/` : Icônes, manifest et splash screen
- `local-api/` : Petite API PHP locale connectée à OpenAI
- `vite.config.js` : config PWA + alias
- `.env.example` : configuration API locale

---

## 🛠️ Scripts utiles

```bash
npm run dev       # Lancer l'app en développement
npm run build     # Build de production
npm run preview   # Aperçu du build
npm run lint      # Analyse ESLint
npm run format    # Formatage Prettier
```

---

## 🔐 À propos de l'API

Un petit script PHP (`local-api/soutien.php`) permet d’envoyer les actions de l’utilisateur à l’API OpenAI pour générer un message personnalisé, motivant et humain.

---

## 📄 Licence

Projet open-source, librement réutilisable à des fins personnelles ou professionnelles (non commercialisées).

---

## 🙌 Remerciements

Merci à celles et ceux qui croient à l’impact des petits pas.  
Avec **The GoodMe**, chaque geste compte 🌟
