# Corrections appliquées - Arborisis WordPress

## 1. Problème de z-index de la carte ✅

### Problème
La carte interactive se superposait aux autres éléments de l'interface (header, contrôles, etc.).

### Solution
Ajout d'une règle CSS spécifique pour l'élément `#map` :

**Fichier modifié :** `wp-content/themes/arborisis/src/styles/main.css`

```css
#map {
  @apply z-0;
}
```

Cette règle force la carte à rester en arrière-plan (z-index: 0) tandis que :
- Les contrôles overlay utilisent `z-10` et `z-20`
- Le header utilise `z-40`
- Les modales utilisent `z-50`

### Test
1. Visitez `/map`
2. Vérifiez que les contrôles de gauche et droite sont cliquables
3. Vérifiez que le header reste visible et cliquable

---

## 2. Bug TypeError dans page Explorer ✅

### Problème
```
Failed to load sounds: TypeError: sounds.map is not a function
```

L'API retournait parfois un objet au lieu d'un tableau, ce qui causait l'erreur lors de l'appel à `.map()`.

### Solution
Ajout d'une vérification pour gérer les deux formats de réponse :

**Fichier modifié :** `wp-content/themes/arborisis/page-explore.php` (lignes 201-214)

```javascript
const data = await response.json();

// Handle both array and object responses
const sounds = Array.isArray(data) ? data : (data.sounds || []);
```

Cette modification :
- Vérifie si `data` est un tableau
- Si oui, l'utilise directement
- Sinon, extrait `data.sounds` ou utilise un tableau vide

### Test
1. Visitez `/explore`
2. Changez les filtres et le tri
3. Vérifiez qu'aucune erreur n'apparaît dans la console
4. Vérifiez que les sons s'affichent correctement

---

## 3. Bug TypeError dans la carte interactive ✅

### Problème
```
Failed to load sounds: TypeError: Cannot read properties of undefined (reading 'lat')
```

Le code tentait d'accéder à `cluster.centroid.lat` sans vérifier si ces propriétés existaient.

### Solution
Ajout de validations strictes avant de créer les marqueurs :

**Fichier modifié :** `wp-content/themes/arborisis/src/map.js`

#### Validation des sons (lignes 141-148)
```javascript
if (data.sounds && Array.isArray(data.sounds)) {
    data.sounds.forEach(sound => {
        if (sound && sound.latitude && sound.longitude) {
            const marker = this.createSoundMarker(sound);
            this.markers.push(marker);
        }
    });
}
```

#### Validation des clusters (lignes 150-157)
```javascript
if (data.clusters && Array.isArray(data.clusters)) {
    data.clusters.forEach(cluster => {
        if (cluster && cluster.centroid && cluster.centroid.lat && cluster.centroid.lon) {
            const marker = this.createClusterMarker(cluster);
            this.markers.push(marker);
        }
    });
}
```

#### Calcul sécurisé du compteur (lignes 119-126)
```javascript
const soundsCount = (Array.isArray(data.sounds) ? data.sounds.length : 0);
const clustersCount = (Array.isArray(data.clusters) ? data.clusters.reduce((sum, c) => sum + (c.count || 0), 0) : 0);
const totalSounds = soundsCount + clustersCount;

const countElement = document.getElementById('visible-count');
if (countElement) {
    countElement.textContent = totalSounds;
}
```

### Test
1. Visitez `/map`
2. Déplacez et zoomez sur la carte
3. Vérifiez qu'aucune erreur n'apparaît dans la console
4. Vérifiez que les marqueurs et clusters s'affichent
5. Vérifiez que le compteur "Visible" s'actualise

---

## 4. Script de création de pages WordPress ✅

### Création
Un script PHP a été créé pour automatiser la création de toutes les pages WordPress.

**Fichier créé :** `create-pages.php`

### Pages créées automatiquement

#### Pages publiques (9)
- Explorer (`/explore`) → `page-explore.php`
- Carte (`/map`) → `page-map.php`
- Graphe (`/graph`) → `page-graph.php`
- Statistiques (`/stats`) → `page-stats.php`
- À propos (`/about`) → `page-about.php`
- Contact (`/contact`) → `page-contact.php`
- FAQ (`/faq`) → `page-faq.php`
- Documentation API (`/api-docs`) → `page-api-docs.php`

#### Pages légales (4)
- Règles de la communauté (`/guidelines`) → `page-guidelines.php`
- Licences (`/licenses`) → `page-licenses.php`
- Confidentialité (`/privacy`) → `page-privacy.php`
- Conditions d'utilisation (`/terms`) → `page-terms.php`

#### Pages utilisateur (6)
- Mon profil (`/profile`) → `page-profile.php`
- Mes sons (`/my-sounds`) → `page-my-sounds.php`
- Favoris (`/favorites`) → `page-favorites.php`
- Notifications (`/notifications`) → `page-notifications.php`
- Paramètres (`/settings`) → `page-settings.php`
- Uploader un son (`/upload`) → `page-upload.php`

### Utilisation du script

#### Méthode 1 : Via navigateur (recommandé)
```
https://arborisis.social/create-pages.php
```

#### Méthode 2 : Via ligne de commande
```bash
php create-pages.php
```

### Fonctionnalités
✅ Détecte les pages existantes (évite les doublons)
✅ Crée uniquement les pages manquantes
✅ Assigne automatiquement le bon template
✅ Met à jour le template des pages existantes
✅ Affiche un rapport détaillé
✅ Définit l'ordre du menu
✅ Désactive les commentaires

### Après utilisation
Le script peut être supprimé pour des raisons de sécurité :
```bash
rm create-pages.php
```

---

## 5. Compilation des assets ✅

Les fichiers JavaScript et CSS ont été recompilés avec Vite pour appliquer toutes les corrections.

### Commande utilisée
```bash
cd wp-content/themes/arborisis
npm run build
```

### Fichiers générés
- `dist/map.B6_26wiw.js` (nouvelle version avec corrections)
- `dist/main.Q21CCLkG.css` (avec fix z-index)
- Toutes les versions compressées `.gz`

---

## Récapitulatif des fichiers modifiés

1. ✅ `wp-content/themes/arborisis/src/styles/main.css` - Fix z-index carte
2. ✅ `wp-content/themes/arborisis/src/map.js` - Fix bugs carte (3 endroits)
3. ✅ `wp-content/themes/arborisis/page-explore.php` - Fix bug sounds.map
4. ✅ `create-pages.php` - Nouveau script de création de pages
5. ✅ `README-PAGES.md` - Documentation du script
6. ✅ Compilation des assets avec npm run build

---

## Tests à effectuer

### Page Explorer (/explore)
- [ ] Les sons s'affichent correctement
- [ ] Les filtres fonctionnent sans erreur
- [ ] Le tri fonctionne
- [ ] La pagination fonctionne
- [ ] Pas d'erreur dans la console

### Page Carte (/map)
- [ ] La carte se charge
- [ ] Les marqueurs s'affichent
- [ ] Les clusters s'affichent
- [ ] Le compteur "Visible" s'actualise
- [ ] Les contrôles sont cliquables
- [ ] Le header est visible au-dessus
- [ ] Pas d'erreur dans la console

### Script de création de pages
- [ ] Exécuter le script
- [ ] Vérifier que toutes les pages sont créées
- [ ] Vérifier que les templates sont bien assignés
- [ ] Tester quelques pages pour voir si elles s'affichent

---

## En cas de problème

### La carte ne s'affiche pas
```bash
# Vider le cache du navigateur
Cmd + Shift + R (Mac)
Ctrl + Shift + R (Windows/Linux)
```

### Les corrections ne s'appliquent pas
```bash
cd wp-content/themes/arborisis
npm run build
```

### Les pages ne se créent pas
- Vérifier que vous êtes connecté comme administrateur
- Vérifier les permissions de la base de données
- Consulter les logs PHP pour les erreurs

---

**Date des corrections :** 21 janvier 2026
**Version du thème :** 1.0.0
**Build system :** Vite 5.4.21
