# Commandes essentielles Arborisis

## 🚀 Installation initiale

```bash
# 1. Installer dépendances
composer install

# 2. Copier configuration
cp .env.example .env
nano .env  # Éditer avec vos valeurs

# 3. Installer WordPress
wp core install \
  --url="https://votre-domaine.com" \
  --title="Arborisis" \
  --admin_user="admin" \
  --admin_password="VotreMotDePasse" \
  --admin_email="admin@example.com"

# 4. Activer plugins
wp plugin activate arborisis-core
wp plugin activate arborisis-audio
wp plugin activate arborisis-search
wp plugin activate arborisis-geo
wp plugin activate arborisis-stats
wp plugin activate arborisis-graph

# 5. Activer Redis
wp redis enable

# 6. Créer index OpenSearch
wp eval 'ARB_OpenSearch_Client::create_index();'

# 7. Créer licences
wp term create sound_license "CC BY 4.0" --slug=cc-by-4
wp term create sound_license "CC BY-SA 4.0" --slug=cc-by-sa-4
wp term create sound_license "CC0 (Public Domain)" --slug=cc0
```

## 🔄 Maintenance quotidienne

```bash
# Vérifier statut services
wp redis status
wp eval 'echo ARB_OpenSearch_Client::is_available() ? "OK" : "DOWN";'

# Vérifier logs
tail -f wp-content/debug.log

# Vérifier cache
wp cache flush
```

## 📊 Gestion des stats

```bash
# Agréger plays du jour précédent
wp arborisis aggregate-plays

# Agréger toutes les données historiques
wp arborisis aggregate-plays --all

# Agréger une date spécifique
wp arborisis aggregate-plays --date=2024-01-15

# Calculer les trending scores
wp arborisis compute-trending

# Nettoyer anciens plays (> 90 jours)
wp arborisis cleanup-plays

# Nettoyer avec rétention custom
wp arborisis cleanup-plays --days=30

# Préchauffer le cache
wp arborisis warm-cache
```

## 🔍 Recherche OpenSearch

```bash
# Réindexer tous les sons
wp arborisis reindex

# Réindexer par lots de 50
wp arborisis reindex --batch-size=50

# Traiter la queue d'indexation
wp arborisis process-opensearch-queue

# Traiter max 50 items
wp arborisis process-opensearch-queue --limit=50

# Vérifier l'index
curl -u admin:password "https://opensearch:9200/arborisis_sounds/_count"

# Recréer l'index (ATTENTION : supprime tout)
wp eval 'ARB_OpenSearch_Client::delete_index(); ARB_OpenSearch_Client::create_index();'
wp arborisis reindex
```

## 🗺️ Géolocalisation

```bash
# Réindexer toutes les positions géographiques
wp arborisis reindex-geo

# Vérifier index geo
wp db query "SELECT COUNT(*) FROM wp_arb_geo_index;"
```

## 🎵 Audio

```bash
# Extraire metadata pour un son
wp arborisis extract-metadata 123

# Extraire metadata pour tous les sons sans metadata
wp post list --post_type=sound --meta_key=_arb_duration --meta_compare=NOT_EXISTS --format=ids | \
  xargs -I {} wp arborisis extract-metadata {}
```

## 👥 Utilisateurs

```bash
# Créer un uploader
wp user create uploader1 uploader@example.com \
  --role=uploader \
  --user_pass=password \
  --display_name="Test Uploader"

# Créer un moderator
wp user create moderator1 moderator@example.com \
  --role=moderator \
  --user_pass=password \
  --display_name="Test Moderator"

# Lister tous les uploaders
wp user list --role=uploader

# Promouvoir user en uploader
wp user set-role user123 uploader
```

## 🔧 Debug

```bash
# Vérifier configuration
wp config list

# Vérifier plugins actifs
wp plugin list --status=active

# Vérifier capacités utilisateur
wp user get admin --field=caps

# Tester endpoint API
curl "https://votre-domaine.com/wp-json/arborisis/v1/sounds"

# Vérifier connexion S3
wp eval 'var_dump(ARB_S3_Client::get()->listBuckets());'

# Vérifier connexion Redis
wp redis status
redis-cli -h localhost -p 6379 -a password PING

# Vérifier connexion OpenSearch
curl -u admin:password "https://opensearch:9200/_cluster/health"
```

## 📦 Base de données

```bash
# Export DB
wp db export backup-$(date +%Y%m%d).sql

# Import DB
wp db import backup.sql

# Optimiser tables
wp db optimize

# Vérifier tables custom
wp db query "SHOW TABLES LIKE 'wp_arb_%';"

# Compter likes
wp db query "SELECT COUNT(*) FROM wp_arb_likes;"

# Compter plays
wp db query "SELECT COUNT(*) FROM wp_arb_plays;"

# Top 10 sons par plays
wp db query "
  SELECT p.ID, p.post_title, pm.meta_value as plays
  FROM wp_posts p
  JOIN wp_postmeta pm ON p.ID = pm.post_id
  WHERE p.post_type = 'sound'
    AND pm.meta_key = '_arb_plays_count'
  ORDER BY CAST(pm.meta_value AS UNSIGNED) DESC
  LIMIT 10;
"
```

## 🗑️ Nettoyage

```bash
# Vider cache Redis
wp cache flush

# Vider cache OpenSearch
curl -X POST "https://opensearch:9200/arborisis_sounds/_cache/clear" \
  -u admin:password

# Supprimer transients expirés
wp transient delete --expired

# Supprimer révisions
wp post delete $(wp post list --post_type=revision --format=ids) --force

# Optimiser DB
wp db optimize
```

## 📈 Stats et monitoring

```bash
# Stats globales
curl "https://votre-domaine.com/wp-json/arborisis/v1/stats/global" | jq

# Stats user
curl "https://votre-domaine.com/wp-json/arborisis/v1/stats/user/1" | jq

# Leaderboard sons
curl "https://votre-domaine.com/wp-json/arborisis/v1/stats/leaderboards?type=sounds&period=7d" | jq

# Leaderboard users
curl "https://votre-domaine.com/wp-json/arborisis/v1/stats/leaderboards?type=users" | jq

# Compter sons par status
wp post list --post_type=sound --post_status=publish --format=count
```

## 🔐 Sécurité

```bash
# Générer nouveaux salts WordPress
curl https://api.wordpress.org/secret-key/1.1/salt/

# Lister utilisateurs admin
wp user list --role=administrator

# Changer mot de passe admin
wp user update admin --user_pass=NouveauMotDePasse

# Vérifier permissions fichiers
find wp-content -type f -exec chmod 644 {} \;
find wp-content -type d -exec chmod 755 {} \;

# Permissions correctes
chown -R www-data:www-data wp-content/uploads
```

## 🚀 Déploiement

```bash
# Pull dernières modifs
git pull origin main

# Installer nouvelles dépendances
composer install --no-dev --optimize-autoloader

# Update DB si nécessaire
wp core update-db

# Vider cache
wp cache flush

# Réindexer si structure changée
wp arborisis reindex
wp arborisis reindex-geo
```

## 🧪 Tests

```bash
# Test upload presign
curl -X POST "https://votre-domaine.com/wp-json/arborisis/v1/upload/presign" \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "filename": "test.mp3",
    "content_type": "audio/mpeg",
    "filesize": 1048576
  }'

# Test search
curl "https://votre-domaine.com/wp-json/arborisis/v1/search?q=nature&tags=birds"

# Test map
curl "https://votre-domaine.com/wp-json/arborisis/v1/map/sounds?bbox=48,2,49,3&zoom=10"

# Test graph
curl "https://votre-domaine.com/wp-json/arborisis/v1/graph/explore?seed_id=1&depth=2"
```

## 📝 Logs

```bash
# WordPress debug log
tail -f wp-content/debug.log

# PHP errors
tail -f /var/log/php/error.log

# Nginx access
tail -f /var/log/nginx/access.log

# Nginx errors
tail -f /var/log/nginx/error.log

# OpenSearch logs
tail -f /var/log/opensearch/opensearch.log

# Redis logs
tail -f /var/log/redis/redis-server.log
```

## ⚡ Performance

```bash
# Vérifier OPcache status
wp eval 'var_dump(opcache_get_status());'

# Vérifier Redis stats
redis-cli -h localhost -p 6379 -a password INFO stats

# Vérifier OpenSearch stats
curl -u admin:password "https://opensearch:9200/_stats"

# Analyser requêtes lentes MySQL
wp db query "
  SELECT * FROM mysql.slow_log
  WHERE start_time > DATE_SUB(NOW(), INTERVAL 1 HOUR)
  ORDER BY query_time DESC
  LIMIT 10;
"
```

## 🔄 Crons

```bash
# Lister crons WP (si activés)
wp cron event list

# Exécuter tous les crons manuellement
wp cron event run --all

# Tester un cron spécifique
wp arborisis aggregate-plays
wp arborisis compute-trending
wp arborisis warm-cache
```

## 💾 Backup

```bash
# Backup complet
DATE=$(date +%Y%m%d-%H%M)
mkdir -p backups/$DATE

# DB
wp db export backups/$DATE/database.sql

# Uploads
tar -czf backups/$DATE/uploads.tar.gz wp-content/uploads

# Plugins custom
tar -czf backups/$DATE/plugins.tar.gz \
  wp-content/plugins/arborisis-*

# Archive complète
tar -czf backups/arborisis-full-$DATE.tar.gz backups/$DATE
```

## 🔄 Restore

```bash
# Restore DB
wp db import backup.sql

# Restore uploads
tar -xzf uploads.tar.gz -C wp-content/

# Réindexer après restore
wp arborisis reindex
wp arborisis reindex-geo
wp arborisis aggregate-plays --all
wp arborisis compute-trending
```

## 📊 Rapports

```bash
# Rapport complet
echo "=== ARBORISIS STATUS REPORT ===" && \
echo "Date: $(date)" && \
echo "" && \
echo "Sounds: $(wp post list --post_type=sound --post_status=publish --format=count)" && \
echo "Users: $(wp user list --format=count)" && \
echo "Likes: $(wp db query 'SELECT COUNT(*) FROM wp_arb_likes;' --skip-column-names)" && \
echo "Plays: $(wp db query 'SELECT COUNT(*) FROM wp_arb_plays;' --skip-column-names)" && \
echo "" && \
echo "Redis: $(wp redis status 2>&1 | grep -o 'Connected' || echo 'Disconnected')" && \
echo "OpenSearch: $(curl -s -u admin:password https://opensearch:9200/_cluster/health | jq -r '.status')" && \
echo "" && \
echo "Disk usage: $(df -h / | tail -1 | awk '{print $5}')" && \
echo "=== END REPORT ==="
```

## 🆘 Urgence

```bash
# Mode maintenance ON
wp maintenance-mode activate

# Mode maintenance OFF
wp maintenance-mode deactivate

# Reset admin password (si oublié)
wp user update admin --user_pass=NewPassword --skip-email

# Réinitialiser complètement cache
wp cache flush
redis-cli -h localhost -a password FLUSHDB
wp rewrite flush

# Reconstruire index OpenSearch complet
wp eval 'ARB_OpenSearch_Client::delete_index(); ARB_OpenSearch_Client::create_index();'
wp arborisis reindex

# En cas de problème critique DB
wp db repair
wp db optimize
```

---

**Note** : Remplacer `admin:password`, `localhost`, URLs par vos valeurs réelles.

Pour Jelastic : ajouter `--allow-root` à toutes les commandes WP-CLI.
