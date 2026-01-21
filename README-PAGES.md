# Guide de création des pages WordPress

Ce guide explique comment créer automatiquement toutes les pages WordPress pour Arborisis.

## Script de création automatique

Un script `create-pages.php` a été créé pour automatiser la création de toutes les pages nécessaires.

### Méthode 1: Via le navigateur (Recommandé)

1. Connectez-vous en tant qu'administrateur WordPress
2. Accédez à l'URL: `https://arborisis.social/create-pages.php`
3. Le script va créer automatiquement toutes les pages manquantes
4. Vous verrez un rapport détaillé des pages créées

### Méthode 2: Via la ligne de commande

```bash
cd /Users/bastienjavaux/Documents/Arborisis\ Wordpress
php create-pages.php
```

## Pages créées

Le script crée les pages suivantes avec leurs templates associés:

### Pages publiques
- **Explorer** (`/explore`) - Exploration des sons
- **Carte** (`/map`) - Carte interactive des sons
- **Graphe** (`/graph`) - Visualisation en graphe
- **Statistiques** (`/stats`) - Statistiques de la plateforme
- **À propos** (`/about`) - Présentation du projet
- **Contact** (`/contact`) - Formulaire de contact
- **FAQ** (`/faq`) - Questions fréquentes

### Pages légales
- **Règles de la communauté** (`/guidelines`)
- **Licences** (`/licenses`)
- **Confidentialité** (`/privacy`)
- **Conditions d'utilisation** (`/terms`)

### Pages techniques
- **Documentation API** (`/api-docs`)

### Pages utilisateur (connexion requise)
- **Mon profil** (`/profile`)
- **Mes sons** (`/my-sounds`)
- **Favoris** (`/favorites`)
- **Notifications** (`/notifications`)
- **Paramètres** (`/settings`)
- **Uploader un son** (`/upload`)

## Fonctionnalités du script

✅ Vérifie si les pages existent déjà (évite les doublons)
✅ Crée les pages manquantes
✅ Assigne automatiquement le bon template à chaque page
✅ Met à jour le template des pages existantes si nécessaire
✅ Affiche un rapport détaillé
✅ Définit l'ordre du menu
✅ Désactive les commentaires sur toutes les pages

## Après la création

Une fois les pages créées, vous pouvez:

1. **Accéder à la liste des pages**: `/wp-admin/edit.php?post_type=page`
2. **Personnaliser le contenu** de chaque page si nécessaire
3. **Créer un menu** avec ces pages: `/wp-admin/nav-menus.php`

## Correction du problème de z-index de la carte

Le problème où la carte se superposait aux autres éléments a été corrigé:

- Ajout de `z-0` à l'élément `#map` dans `src/styles/main.css`
- Les overlays (contrôles, légende) utilisent maintenant correctement `z-10` et `z-20`
- Le header reste en `z-40` pour toujours être au-dessus

### Recompiler les styles

Si vous modifiez les styles, recompilez avec:

```bash
cd wp-content/themes/arborisis
npm run build
```

## Suppression du script

Une fois toutes les pages créées, vous pouvez supprimer le fichier `create-pages.php` pour des raisons de sécurité:

```bash
rm /Users/bastienjavaux/Documents/Arborisis\ Wordpress/create-pages.php
```

## Dépannage

### Les pages ne se créent pas

Vérifiez que:
- Vous êtes connecté en tant qu'administrateur
- Les permissions d'écriture sur la base de données sont correctes
- Il n'y a pas d'erreur PHP dans les logs

### Le template ne s'applique pas

- Assurez-vous que les fichiers de template existent dans `/wp-content/themes/arborisis/`
- Rechargez la page dans l'éditeur WordPress
- Vérifiez que le template est bien sélectionné dans l'attribut de page

### Les styles de la carte ne s'appliquent pas

Compilez les styles avec:
```bash
npm run build
```

Et videz le cache du navigateur (Cmd+Shift+R sur Mac).
