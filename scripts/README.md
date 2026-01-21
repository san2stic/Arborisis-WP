# Scripts pour gérer les disques Docker

Ce répertoire contient des scripts utilitaires pour gérer les disques et volumes Docker.

## Scripts disponibles

### 1. `list-disks.sh` - Script interactif
Script complet et interactif pour lister les disques et générer une configuration Docker personnalisée.

**Usage:**
```bash
./scripts/list-disks.sh
```

**Fonctionnalités:**
- Affiche tous les disques montés avec l'espace disponible
- Liste les volumes externes
- Mode interactif pour générer automatiquement la configuration
- Crée un fichier de configuration prêt à être copié dans `docker-compose.yml`

### 2. `show-disks.sh` - Affichage rapide
Script non-interactif pour afficher rapidement l'état des disques.

**Usage:**
```bash
./scripts/show-disks.sh
```

**Fonctionnalités:**
- Vue d'ensemble rapide des disques
- Liste des volumes Docker existants
- Recommandations automatiques pour la configuration

## Comment ajouter un disque externe à Docker

### Étape 1: Identifier le disque
Exécutez l'un des scripts pour voir les disques disponibles:
```bash
./scripts/show-disks.sh
```

### Étape 2: Configurer docker-compose.yml
Ajoutez le volume dans la section `volumes:` de votre `docker-compose.yml`:

```yaml
volumes:
  # Volumes existants...
  wordpress_data:
    driver: local
  mysql_data:
    driver: local
  
  # Nouveau volume pour disque externe
  external_backup:
    driver: local
    driver_opts:
      type: none
      o: bind
      device: /Volumes/NomDeVotreDisque  # Remplacez par le nom réel
```

### Étape 3: Monter le volume dans un service
Ajoutez le volume dans le service désiré (par exemple `wordpress`):

```yaml
services:
  wordpress:
    volumes:
      - wordpress_data:/var/www/html
      - external_backup:/var/www/html/backup  # Nouveau montage
```

### Étape 4: Redémarrer Docker
```bash
docker-compose down
docker-compose up -d
```

## Cas d'usage courants

### Backup sur disque externe
```yaml
volumes:
  backup_storage:
    driver: local
    driver_opts:
      type: none
      o: bind
      device: /Volumes/BackupDisk

services:
  wordpress:
    volumes:
      - backup_storage:/var/www/html/wp-content/backup
```

### Uploads sur disque externe (médias WordPress)
```yaml
volumes:
  media_storage:
    driver: local
    driver_opts:
      type: none
      o: bind
      device: /Volumes/MediaDisk

services:
  wordpress:
    volumes:
      - media_storage:/var/www/html/wp-content/uploads
```

### Base de données sur disque externe (pour de meilleures performances)
```yaml
volumes:
  mysql_external:
    driver: local
    driver_opts:
      type: none
      o: bind
      device: /Volumes/FastSSD/mysql

services:
  mysql:
    volumes:
      - mysql_external:/var/lib/mysql
```

## Notes importantes

⚠️ **Avertissements:**
- Assurez-vous que le disque externe est toujours monté avant de démarrer Docker
- Les chemins doivent être absolus (`/Volumes/...`)
- Les permissions du disque doivent permettre à Docker d'écrire
- Pour macOS, vérifiez que Docker Desktop a accès au chemin dans Préférences > Resources > File Sharing

💡 **Conseils:**
- Utilisez des disques SSD pour les bases de données (meilleures performances)
- Les disques HDD sont adaptés pour les backups et médias
- Testez toujours avec `docker-compose config` avant de lancer
