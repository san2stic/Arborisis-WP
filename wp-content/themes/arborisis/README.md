# Arborisis WordPress Theme

Thème WordPress premium pour la plateforme Arborisis - Field Recording & Soundscape Exploration

## 🎨 Design

Thème moderne avec :
- Design system complet (Tailwind CSS)
- Dark mode avec détection système
- Animations fluides et professionnelles
- Responsive design mobile-first
- Glass morphism effects
- Typography professionnelle (Inter, Plus Jakarta Sans)

## 🚀 Fonctionnalités

### Pages
- **Homepage** : Hero, trending sounds, recent sounds, stats
- **Explore** : Browse avec filtres avancés (tags, durée, licence)
- **Map** : Carte interactive Leaflet avec clustering
- **Graph** : Visualisation D3.js force-directed
- **Stats** : Dashboard statistiques avec leaderboards
- **Upload** : Formulaire upload avec drag & drop S3
- **Profile** : Page profil utilisateur
- **Single Sound** : Page détail avec player WaveSurfer
- **Archive** : Liste sons par tag/licence
- **404** : Page d'erreur personnalisée

### Composants JavaScript
- **API Client** : Wrapper complet pour l'API REST
- **Search** : Recherche globale avec modal (Cmd/Ctrl+K)
- **Map** : Leaflet avec markers et clusters
- **Graph** : D3.js avec force simulation
- **Audio Player** : WaveSurfer avec waveform visualization
- **Dark Mode** : Toggle avec localStorage

### Composants UI
- Système de boutons (primary, outline, ghost)
- Cards avec hover effects
- Sound cards avec play overlay
- Audio player fixe
- Map markers et clusters
- Graph nodes et edges
- Navigation avec scroll effects
- Hero section avec gradients
- Badges, inputs, stats

## 📦 Installation

### Prérequis
- Node.js 18+
- npm ou yarn

### Installation des dépendances

```bash
cd wp-content/themes/arborisis
npm install
```

### Développement

```bash
# Mode développement avec Vite dev server
npm run dev
```

Le dev server démarre sur `http://localhost:3000` avec hot module replacement (HMR).

### Production

```bash
# Build pour production
npm run build
```

Les assets compilés seront dans le dossier `dist/`.

## 🎯 Configuration

### Vite

Le thème utilise Vite avec plusieurs entry points :
- `src/main.js` : JavaScript principal (API, search, dark mode)
- `src/map.js` : Carte Leaflet
- `src/graph.js` : Graphe D3.js
- `src/player.js` : Audio player WaveSurfer
- `src/styles/main.css` : CSS Tailwind + custom

### Tailwind

Configuration dans `tailwind.config.js` :
- Couleurs custom (primary green, secondary purple, dark slate)
- Typographie (Inter, Plus Jakarta Sans, JetBrains Mono)
- Animations custom (fade-in, slide-up, scale-in)
- Utilities custom (glass, gradient-mesh)

## 🔧 Personnalisation

### Couleurs

Modifier dans `tailwind.config.js` :

```js
colors: {
  primary: {
    500: '#22c55e',
    600: '#16a34a',
  },
  secondary: {
    500: '#a855f7',
    600: '#9333ea',
  },
}
```

### Fonts

Modifier dans `tailwind.config.js` :

```js
fontFamily: {
  sans: ['Inter', 'system-ui', 'sans-serif'],
  display: ['Plus Jakarta Sans', 'system-ui', 'sans-serif'],
  mono: ['JetBrains Mono', 'monospace'],
}
```

## 📁 Structure

```
arborisis/
├── assets/               # Images, logos, placeholders
├── dist/                 # Build production (généré)
├── src/
│   ├── main.js          # Entry point principal
│   ├── map.js           # Carte Leaflet
│   ├── graph.js         # Graphe D3.js
│   ├── player.js        # Audio player
│   └── styles/
│       └── main.css     # CSS principal
├── functions.php        # Fonctions du thème
├── header.php           # Header
├── footer.php           # Footer
├── front-page.php       # Homepage
├── single-sound.php     # Page son
├── page-explore.php     # Browse/filtres
├── page-map.php         # Carte
├── page-graph.php       # Graphe
├── page-stats.php       # Stats
├── page-upload.php      # Upload
├── page-profile.php     # Profil
├── archive-sound.php    # Archive
├── 404.php              # Erreur 404
├── comments.php         # Commentaires
├── searchform.php       # Formulaire recherche
├── package.json         # Dépendances npm
├── vite.config.js       # Config Vite
└── tailwind.config.js   # Config Tailwind
```

## 🌐 Navigateurs supportés

- Chrome/Edge (dernières 2 versions)
- Firefox (dernières 2 versions)
- Safari (dernières 2 versions)
- iOS Safari (iOS 14+)
- Chrome Android (dernières 2 versions)

## 📄 Licence

Ce thème fait partie du projet Arborisis. Tous droits réservés.

## 🤝 Support

Pour toute question ou problème, référez-vous à la documentation principale du projet Arborisis.

---

**Version** : 1.0.0
**Auteur** : Arborisis Team
**Requires** : WordPress 6.0+, PHP 8.2+
