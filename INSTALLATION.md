# Installation rapide Arborisis

## Prérequis

- PHP 8.2+
- MySQL/MariaDB 8.0+
- Redis 6.0+
- OpenSearch 2.0+
- Composer
- WP-CLI
- Accès S3/MinIO

## Installation en 10 minutes

### 1. Installer les dépendances PHP

```bash
composer install
```

### 2. Configuration

```bash
# Copier .env.example
cp .env.example .env

# Éditer .env avec vos valeurs
nano .env
```

**Variables critiques à configurer :**

```bash
# Database
DB_NAME=arborisis
DB_USER=votre_user
DB_PASSWORD=votre_password
DB_HOST=localhost

# WordPress URLs
WP_HOME=https://votre-domaine.com
WP_SITEURL=https://votre-domaine.com

# Générer les salts WordPress
# Aller sur : https://api.wordpress.org/secret-key/1.1/salt/
# Copier-coller dans .env

# Redis
REDIS_HOST=localhost
REDIS_PORT=6379
REDIS_PASSWORD=votre_password_redis

# OpenSearch
OPENSEARCH_HOST=localhost
OPENSEARCH_PORT=9200
OPENSEARCH_USER=admin
OPENSEARCH_PASSWORD=votre_password_opensearch
OPENSEARCH_INDEX=arborisis_sounds

# S3/MinIO
S3_ENDPOINT=https://s3.infomaniak.com
S3_BUCKET=votre-bucket
S3_ACCESS_KEY=votre_key
S3_SECRET_KEY=votre_secret
```

### 3. Installer WordPress

```bash
wp core install \
  --url="https://votre-domaine.com" \
  --title="Arborisis" \
  --admin_user="admin" \
  --admin_password="VotreMotDePasseSecurisé" \
  --admin_email="admin@example.com"
```

### 4. Activer les plugins

```bash
wp plugin activate arborisis-core
wp plugin activate arborisis-audio
wp plugin activate arborisis-search
wp plugin activate arborisis-geo
wp plugin activate arborisis-stats
wp plugin activate arborisis-graph
```

### 5. Créer l'index OpenSearch

```bash
# Créer l'index avec le mapping
curl -X PUT "https://votre-opensearch:9200/arborisis_sounds" \
  -u admin:password \
  -H 'Content-Type: application/json' \
  -d @opensearch-mapping.json

# Ou utiliser la méthode via WP-CLI
wp eval 'ARB_OpenSearch_Client::create_index();'
```

### 6. Créer les licences par défaut

```bash
wp term create sound_license "CC BY 4.0" --slug=cc-by-4
wp term create sound_license "CC BY-SA 4.0" --slug=cc-by-sa-4
wp term create sound_license "CC BY-NC 4.0" --slug=cc-by-nc-4
wp term create sound_license "CC0 (Public Domain)" --slug=cc0
wp term create sound_license "Tous droits réservés" --slug=all-rights-reserved
```

### 7. Créer un utilisateur uploader

```bash
wp user create uploader uploader@example.com \
  --role=uploader \
  --user_pass=password123 \
  --display_name="Test Uploader"
```

### 8. Configuration Redis Object Cache

```bash
# Activer le cache Redis
wp redis enable

# Vérifier le statut
wp redis status
```

### 9. Tester l'installation

```bash
# Vérifier les endpoints
curl https://votre-domaine.com/wp-json/arborisis/v1/sounds

# Vérifier OpenSearch
curl -u admin:password https://votre-opensearch:9200/arborisis_sounds/_count

# Vérifier Redis
redis-cli -h localhost -p 6379 -a password PING
```

### 10. Configuration des crons (Production)

Dans `.env` :
```bash
DISABLE_WP_CRON=true
```

Ajouter dans le cron système (crontab -e) :

```cron
# Agrégation des plays (chaque heure)
0 * * * * cd /path/to/wp && wp arborisis aggregate-plays --allow-root

# Calcul des scores trending (chaque heure)
15 * * * * cd /path/to/wp && wp arborisis compute-trending --allow-root

# Warm cache (toutes les 6h)
0 */6 * * * cd /path/to/wp && wp arborisis warm-cache --allow-root

# Cleanup old plays (quotidien 3h)
0 3 * * * cd /path/to/wp && wp arborisis cleanup-plays --allow-root
```

## Vérification post-installation

### Tables créées

```bash
wp db query "SHOW TABLES LIKE 'wp_arb_%';"
```

Devrait afficher :
- `wp_arb_geo_index`
- `wp_arb_likes`
- `wp_arb_plays`
- `wp_arb_plays_daily`
- `wp_arb_opensearch_queue`

### Endpoints disponibles

```bash
# Sounds
curl https://votre-domaine.com/wp-json/arborisis/v1/sounds

# Search
curl "https://votre-domaine.com/wp-json/arborisis/v1/search?q=nature"

# Map
curl "https://votre-domaine.com/wp-json/arborisis/v1/map/sounds?bbox=48,2,49,3&zoom=10"

# Stats
curl https://votre-domaine.com/wp-json/arborisis/v1/stats/global

# Graph
curl "https://votre-domaine.com/wp-json/arborisis/v1/graph/explore?seed_id=1"
```

## Dépannage rapide

### OpenSearch ne se connecte pas

```bash
# Vérifier la connexion
curl -u admin:password https://opensearch-host:9200/_cluster/health

# Recréer l'index si nécessaire
wp eval 'ARB_OpenSearch_Client::delete_index();'
wp eval 'ARB_OpenSearch_Client::create_index();'
```

### Redis ne fonctionne pas

```bash
# Tester la connexion
redis-cli -h redis-host -p 6379 -a password PING

# Vider le cache
wp cache flush
```

### Réindexer tout

```bash
# OpenSearch
wp arborisis reindex

# Geo
wp arborisis reindex-geo

# Stats
wp arborisis aggregate-plays --all
wp arborisis compute-trending
```

## Upload de test

```bash
# Créer un son de test (nécessite curl et jq)
TOKEN=$(wp user meta get 1 session_tokens --format=json | jq -r 'keys[0]')

# Presign
curl -X POST "https://votre-domaine.com/wp-json/arborisis/v1/upload/presign" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "filename": "test.mp3",
    "content_type": "audio/mpeg",
    "filesize": 1048576
  }'

# Utiliser l'URL retournée pour upload
# Puis finalize avec les métadonnées
```

## Prochaines étapes

1. **Installer le thème** (voir wp-content/themes/arborisis/)
2. **Configurer le monitoring** (logs, métriques)
3. **Backup automatique** de la base de données
4. **SSL/TLS** pour tous les endpoints
5. **CDN** pour les fichiers audio S3

## Support

- Documentation complète : `README.md`
- Issues GitHub : créer un ticket
- Email : support@arborisis.example.com
