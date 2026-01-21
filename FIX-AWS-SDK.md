# 🚨 Fix Critique - AWS SDK Non Chargé

## Le Vrai Problème

L'erreur réelle est :
```
PHP Fatal error: Uncaught Error: Class "Aws\S3\S3Client" not found
```

**Cause** : Le plugin `arborisis-audio` n'incluait pas l'autoloader Composer, donc les classes du SDK AWS (qui sont installées dans `/var/www/html/vendor`) n'étaient pas chargées.

## Solution Appliquée

Le fichier `wp-content/plugins/arborisis-audio/arborisis-audio.php` a été modifié pour charger l'autoloader Composer au démarrage du plugin.

## Ce Qui a Été Changé

```php
// Avant (manquant)
if (!defined('ABSPATH')) exit;
require_once ARB_AUDIO_PATH . 'includes/class-s3-client.php';

// Après (avec autoloader)
if (!defined('ABSPATH')) exit;

// Load Composer autoloader for AWS SDK
if (file_exists(ABSPATH . 'vendor/autoload.php')) {
    require_once ABSPATH . 'vendor/autoload.php';
}

require_once ARB_AUDIO_PATH . 'includes/class-s3-client.php';
```

## Actions Requises sur le Serveur

### Option 1 : Rebuild Complet (RECOMMANDÉ)

```bash
# 1. Pull les changements
git pull

# 2. Rendre le script exécutable
chmod +x rebuild-and-deploy.sh

# 3. Rebuilder et redéployer
./rebuild-and-deploy.sh
```

Ce script va :
- Arrêter WordPress
- Rebuilder l'image Docker (avec le nouveau code du plugin)
- Redémarrer WordPress
- Attendre que tout soit prêt

**Durée estimée** : 5-10 minutes (build + redémarrage)

### Option 2 : Fix Manuel Rapide (si pas accès git)

Si vous ne pouvez pas pull les changements :

```bash
# 1. Éditer le fichier dans le conteneur
sudo docker exec -it arborisis-wordpress vi /var/www/html/wp-content/plugins/arborisis-audio/arborisis-audio.php

# 2. Ajouter cette ligne APRÈS la ligne 16 (define ARB_AUDIO_URL):
if (file_exists(ABSPATH . 'vendor/autoload.php')) { require_once ABSPATH . 'vendor/autoload.php'; }

# 3. Redémarrer PHP-FPM
sudo docker exec arborisis-wordpress supervisorctl restart php-fpm:*

# 4. Vérifier
sudo docker compose logs wordpress | tail -20
```

## Vérification

Après le rebuild, testez l'upload :

1. Aller sur https://arborisis.social/upload
2. Sélectionner un fichier audio
3. L'upload devrait fonctionner !

Si erreur, vérifier les logs :
```bash
sudo docker compose logs wordpress | tail -50
```

Vous ne devriez PLUS voir :
```
Class "Aws\S3\S3Client" not found
```

## Note : Le Bucket MinIO

N'oubliez pas que le bucket doit toujours être créé :

```bash
sudo docker exec arborisis-minio sh -c "mc alias set myminio http://localhost:9000 'Zabou007**Jule' 'Zabou007**Jule' && mc mb myminio/arborisis-audio && mc anonymous set download myminio/arborisis-audio"
```

## Ordre des Opérations Complet

Pour un déploiement propre depuis zéro :

```bash
# 1. Pull les derniers changements
git pull

# 2. Copier la configuration
cp .env.production.local .env

# 3. Rebuilder et démarrer
./rebuild-and-deploy.sh

# 4. Attendre que tout soit up (déjà inclus dans le script)

# 5. Créer le bucket MinIO
sudo docker exec arborisis-minio sh -c "mc alias set myminio http://localhost:9000 'Zabou007**Jule' 'Zabou007**Jule' && mc mb myminio/arborisis-audio && mc anonymous set download myminio/arborisis-audio"

# 6. Tester l'upload
# https://arborisis.social/upload
```

## Troubleshooting

### "vendor/autoload.php not found"

Si après le rebuild vous avez cette erreur, cela signifie que Composer n'a pas installé les dépendances pendant le build.

Vérifier le build :
```bash
sudo docker compose build --no-cache wordpress
```

### "S3Client still not found"

Vérifier que l'autoloader est bien chargé :
```bash
sudo docker exec arborisis-wordpress cat /var/www/html/wp-content/plugins/arborisis-audio/arborisis-audio.php | grep -A5 "vendor/autoload"
```

### Logs du Build

Pour voir les logs complets du build :
```bash
sudo docker compose build --no-cache --progress=plain wordpress 2>&1 | tee build.log
```

Vérifier que composer install s'est bien exécuté dans le log.
