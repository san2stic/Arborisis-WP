# Déploiement Jelastic Infomaniak - Guide complet

## 🎯 Architecture cible

```
┌─────────────────────────────────────────────────────────┐
│                   JELASTIC INFOMANIAK                   │
├─────────────────────────────────────────────────────────┤
│                                                         │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  │
│  │   Nginx +    │  │   MariaDB    │  │    Redis     │  │
│  │   PHP-FPM    │  │  (managed)   │  │  (managed)   │  │
│  │  WordPress   │  │              │  │              │  │
│  │  8 cloudlets │  │  4 cloudlets │  │  2 cloudlets │  │
│  └──────────────┘  └──────────────┘  └──────────────┘  │
│         │                                               │
│         └──> Volume persistant: wp-content/uploads     │
│                                                         │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  │
│  │  OpenSearch  │  │   S3/MinIO   │  │  Cron Jobs   │  │
│  │  (managed)   │  │  (external)  │  │  (Jelastic)  │  │
│  │  8 cloudlets │  │              │  │              │  │
│  └──────────────┘  └──────────────┘  └──────────────┘  │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

## 📋 Checklist pré-déploiement

- [ ] Compte Jelastic Infomaniak actif
- [ ] Nom de domaine configuré
- [ ] Bucket S3 Infomaniak créé (ou MinIO)
- [ ] Salts WordPress générés
- [ ] Toutes les variables d'environnement préparées

## 🚀 Étape 1 : Création de l'environnement Jelastic

### 1.1 Créer un nouvel environnement

Dans le panneau Jelastic :

1. **New Environment**
2. Sélectionner **WordPress**
3. Configuration :
   - **Name** : `arborisis-prod`
   - **Region** : `eu-west-1` (ou proche de vous)

### 1.2 Topologie

**Application Server (PHP)** :
- Engine : `PHP 8.2`
- Server : `Nginx + PHP-FPM`
- Cloudlets : 8 (1 GB RAM)
- Scaling : Auto-scaling enabled (8-16 cloudlets)

**Database** :
- Type : `MariaDB 10.11`
- Cloudlets : 4
- Scaling : Fixed

**Cache** :
- Type : `Redis 7`
- Cloudlets : 2
- Scaling : Fixed

**Search** :
- Type : `OpenSearch 2.x`
- Cloudlets : 8
- Scaling : Fixed

### 1.3 Volume persistant

Ajouter un volume persistant :
- Path : `/var/www/webroot/ROOT/wp-content/uploads`
- Size : 10 GB (minimum)

## 🔧 Étape 2 : Configuration des nodes

### 2.1 Node WordPress (Nginx + PHP)

**SSH dans le node** :
```bash
# Via Jelastic dashboard : Web SSH
```

**Configuration PHP** (`/etc/php.ini`) :
```ini
memory_limit = 256M
upload_max_filesize = 200M
post_max_size = 200M
max_execution_time = 300
max_input_time = 300

; OPcache
opcache.enable = 1
opcache.memory_consumption = 128
opcache.interned_strings_buffer = 8
opcache.max_accelerated_files = 10000
opcache.revalidate_freq = 2
opcache.fast_shutdown = 1

; Error logging
display_errors = Off
log_errors = On
error_log = /var/log/php/error.log
```

**Redémarrer PHP-FPM** :
```bash
sudo systemctl restart php-fpm
```

### 2.2 Node MariaDB

Se connecter via phpMyAdmin (fourni par Jelastic) :

1. Créer la base de données :
```sql
CREATE DATABASE arborisis CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Créer un utilisateur :
```sql
CREATE USER 'arborisis'@'%' IDENTIFIED BY 'VotreMotDePasseSecurise';
GRANT ALL PRIVILEGES ON arborisis.* TO 'arborisis'@'%';
FLUSH PRIVILEGES;
```

### 2.3 Node Redis

Aucune configuration spécifique nécessaire. Noter :
- Host : `node12345-redis.jelastic.infomaniak.com`
- Port : `6379`
- Password : (fourni dans Jelastic dashboard)

### 2.4 Node OpenSearch

**Accéder aux paramètres** :
- Dashboard Jelastic → OpenSearch node → Config

**Configuration minimale** (`opensearch.yml`) :
```yaml
cluster.name: arborisis
node.name: node-1
network.host: 0.0.0.0
discovery.type: single-node
plugins.security.disabled: false
```

**Credentials** :
- User : `admin`
- Password : (fourni ou créer via dashboard)

## 📦 Étape 3 : Déploiement du code

### 3.1 Cloner le repository

**SSH dans le node WordPress** :
```bash
cd /var/www/webroot/ROOT

# Backup WordPress par défaut si nécessaire
mv wp-config.php wp-config.php.bak

# Cloner votre repo
git clone https://github.com/votre-org/arborisis-wordpress.git tmp-deploy
cp -r tmp-deploy/* .
rm -rf tmp-deploy

# Permissions
chown -R nginx:nginx /var/www/webroot/ROOT
chmod -R 755 /var/www/webroot/ROOT
```

### 3.2 Installer les dépendances

```bash
cd /var/www/webroot/ROOT

# Composer
composer install --no-dev --optimize-autoloader

# Vérifier
composer show
```

## 🔐 Étape 4 : Variables d'environnement

### 4.1 Configurer les variables

Dans **Jelastic Dashboard** → **WordPress node** → **Variables** :

```bash
# WordPress
WP_ENV=production
WP_HOME=https://arborisis.example.com
WP_SITEURL=https://arborisis.example.com

# Database (remplacer par vos valeurs Jelastic)
DB_HOST=node12345-mariadb.jelastic.infomaniak.com
DB_NAME=arborisis
DB_USER=arborisis
DB_PASSWORD=VotreMotDePasseSecurise

# WordPress Salts (générer sur https://api.wordpress.org/secret-key/1.1/salt/)
AUTH_KEY=xxxxxx
SECURE_AUTH_KEY=xxxxxx
LOGGED_IN_KEY=xxxxxx
NONCE_KEY=xxxxxx
AUTH_SALT=xxxxxx
SECURE_AUTH_SALT=xxxxxx
LOGGED_IN_SALT=xxxxxx
NONCE_SALT=xxxxxx

# Redis
REDIS_HOST=node12345-redis.jelastic.infomaniak.com
REDIS_PORT=6379
REDIS_PASSWORD=VotrePasswordRedis
REDIS_DB=0

# OpenSearch
OPENSEARCH_HOST=node12345-opensearch.jelastic.infomaniak.com
OPENSEARCH_PORT=9200
OPENSEARCH_USER=admin
OPENSEARCH_PASSWORD=VotrePasswordOpenSearch
OPENSEARCH_INDEX=arborisis_sounds

# S3 Infomaniak Object Storage
S3_ENDPOINT=https://s3.pub1.infomaniak.cloud
S3_REGION=pub1
S3_BUCKET=votre-bucket-arborisis
S3_ACCESS_KEY=VotreCleAcces
S3_SECRET_KEY=VotreCleSecrete
S3_PREFIX=sounds/

# Upload limits
UPLOAD_MAX_MB=200
AUDIO_ALLOWED_MIMES=audio/mpeg,audio/wav,audio/flac,audio/ogg,audio/mp4
RATE_LIMIT_PER_MINUTE=5

# WP-Cron (IMPORTANT: désactiver WP-Cron)
DISABLE_WP_CRON=true
```

**Redémarrer le node** après ajout des variables.

## 🎨 Étape 5 : Installation WordPress

### 5.1 Installer WordPress Core

```bash
wp core install \
  --url="https://arborisis.example.com" \
  --title="Arborisis Field Recording" \
  --admin_user="admin" \
  --admin_password="MotDePasseTreSecurise" \
  --admin_email="admin@example.com" \
  --allow-root
```

### 5.2 Activer les plugins

```bash
wp plugin activate arborisis-core --allow-root
wp plugin activate arborisis-audio --allow-root
wp plugin activate arborisis-search --allow-root
wp plugin activate arborisis-geo --allow-root
wp plugin activate arborisis-stats --allow-root
wp plugin activate arborisis-graph --allow-root
```

### 5.3 Créer l'index OpenSearch

```bash
# Méthode 1 : curl
curl -X PUT "https://node12345-opensearch.jelastic.infomaniak.com:9200/arborisis_sounds" \
  -u admin:password \
  -H 'Content-Type: application/json' \
  -d @opensearch-mapping.json

# Méthode 2 : WP-CLI
wp eval 'ARB_OpenSearch_Client::create_index();' --allow-root
```

### 5.4 Activer Redis Object Cache

```bash
wp redis enable --allow-root
wp redis status --allow-root
```

### 5.5 Créer les licences

```bash
wp term create sound_license "CC BY 4.0" --slug=cc-by-4 --allow-root
wp term create sound_license "CC BY-SA 4.0" --slug=cc-by-sa-4 --allow-root
wp term create sound_license "CC BY-NC 4.0" --slug=cc-by-nc-4 --allow-root
wp term create sound_license "CC0 (Public Domain)" --slug=cc0 --allow-root
wp term create sound_license "Tous droits réservés" --slug=all-rights-reserved --allow-root
```

## ⏰ Étape 6 : Configuration des crons

### 6.1 Désactiver WP-Cron

Vérifier que `DISABLE_WP_CRON=true` est bien défini.

### 6.2 Ajouter les crons Jelastic

**Dashboard Jelastic** → **WordPress node** → **Cron**

Ajouter ces tâches :

```cron
# Agrégation plays (toutes les heures)
0 * * * * cd /var/www/webroot/ROOT && wp arborisis aggregate-plays --allow-root

# Calcul trending scores (toutes les heures)
15 * * * * cd /var/www/webroot/ROOT && wp arborisis compute-trending --allow-root

# Warm cache (toutes les 6h)
0 */6 * * * cd /var/www/webroot/ROOT && wp arborisis warm-cache --allow-root

# Cleanup old plays (quotidien 3h)
0 3 * * * cd /var/www/webroot/ROOT && wp arborisis cleanup-plays --allow-root
```

## 🔒 Étape 7 : SSL/TLS

### 7.1 Installer Let's Encrypt

**Dashboard Jelastic** → **Add-ons** → **Let's Encrypt**

1. Sélectionner le node WordPress
2. Domaines : `arborisis.example.com`, `www.arborisis.example.com`
3. Email : votre@email.com
4. **Install**

Le certificat se renouvelle automatiquement.

### 7.2 Forcer HTTPS

Vérifier dans `wp-config.php` :
```php
define('FORCE_SSL_ADMIN', true);
```

Ajouter redirection HTTP → HTTPS dans Nginx :
```nginx
# Via dashboard Jelastic : WordPress node → Config → Nginx
# Ou fichier /etc/nginx/conf.d/redirect.conf

server {
    listen 80;
    server_name arborisis.example.com;
    return 301 https://$server_name$request_uri;
}
```

## 🧪 Étape 8 : Vérification

### 8.1 Tests endpoints

```bash
# Health check
curl https://arborisis.example.com/wp-json/arborisis/v1/sounds

# OpenSearch
curl -u admin:password https://node12345-opensearch:9200/arborisis_sounds/_count

# Redis
redis-cli -h node12345-redis -p 6379 -a password PING

# Stats
curl https://arborisis.example.com/wp-json/arborisis/v1/stats/global
```

### 8.2 Test upload

1. Se connecter au dashboard WordPress
2. Créer un utilisateur "uploader"
3. Tester l'upload via l'interface (ou API)

### 8.3 Vérifier les logs

```bash
# PHP errors
tail -f /var/log/php/error.log

# Nginx access
tail -f /var/log/nginx/access.log

# Nginx errors
tail -f /var/log/nginx/error.log
```

## 📊 Étape 9 : Monitoring

### 9.1 Jelastic built-in

- **Dashboard** → **Statistics** : CPU, RAM, Network
- **Load Alerts** : configurer alertes email

### 9.2 Health endpoint custom

Créer `/health.php` à la racine :
```php
<?php
http_response_code(200);
echo json_encode([
    'status' => 'ok',
    'timestamp' => time(),
]);
```

Configurer monitoring externe (UptimeRobot, etc.) sur :
`https://arborisis.example.com/health.php`

### 9.3 Logs centralisés (optionnel)

Configurer Sentry ou équivalent :
```bash
composer require sentry/sentry
```

## 🔄 Étape 10 : Backup & Maintenance

### 10.1 Backup automatique DB

**Jelastic Dashboard** → **MariaDB node** → **Add-ons** → **Backup**

- Fréquence : quotidien
- Rétention : 7 jours
- Heure : 2h du matin

### 10.2 Backup volumes

Créer script backup uploads :
```bash
#!/bin/bash
# /root/backup-uploads.sh

DATE=$(date +%Y%m%d)
tar -czf /backup/uploads-$DATE.tar.gz /var/www/webroot/ROOT/wp-content/uploads
find /backup -name "uploads-*.tar.gz" -mtime +7 -delete
```

Ajouter au cron :
```cron
0 2 * * * /root/backup-uploads.sh
```

### 10.3 Mise à jour

```bash
# Composer
composer update

# WordPress core
wp core update --allow-root

# Plugins WordPress (pas les custom)
wp plugin update --all --exclude=arborisis-* --allow-root
```

## 🚨 Troubleshooting

### Problème : OpenSearch inaccessible

```bash
# Vérifier le service
systemctl status opensearch

# Logs
tail -f /var/log/opensearch/opensearch.log

# Recréer l'index
wp eval 'ARB_OpenSearch_Client::delete_index(); ARB_OpenSearch_Client::create_index();' --allow-root
```

### Problème : Redis cache ne fonctionne pas

```bash
# Test connexion
wp redis status --allow-root

# Vider cache
wp cache flush --allow-root

# Logs Redis
redis-cli -h node-redis -a password MONITOR
```

### Problème : Upload S3 échoue

```bash
# Test connexion S3
wp eval 'var_dump(ARB_S3_Client::get()->listBuckets());' --allow-root

# Vérifier variables S3
wp eval 'echo getenv("S3_ENDPOINT");' --allow-root
```

### Problème : Performances lentes

```bash
# Vérifier OPcache
php -i | grep opcache

# Stats Redis
redis-cli -h node-redis -a password INFO stats

# Warm cache
wp arborisis warm-cache --allow-root
```

## 📈 Scaling

### Auto-scaling horizontal

**Dashboard** → **WordPress node** → **Scaling** :

- Stateless : activation auto-scaling
- Trigger : CPU > 70% pendant 5min
- Min nodes : 1
- Max nodes : 3

**Load balancer** : Jelastic ajoute automatiquement Nginx load balancer.

### Scaling vertical

Augmenter cloudlets selon besoin :
- WordPress : 8 → 16 cloudlets (2 GB)
- OpenSearch : 8 → 16 cloudlets (2 GB)
- MariaDB : 4 → 8 cloudlets (1 GB)

## ✅ Checklist post-déploiement

- [ ] WordPress installé et accessible
- [ ] Tous les plugins activés
- [ ] OpenSearch index créé et fonctionnel
- [ ] Redis cache opérationnel
- [ ] SSL/TLS configuré (Let's Encrypt)
- [ ] Crons configurés et testés
- [ ] Upload S3 testé
- [ ] Backup DB automatique activé
- [ ] Monitoring configuré
- [ ] DNS pointé vers Jelastic
- [ ] Tests endpoints API réussis
- [ ] Logs accessibles et propres

## 🎉 Déploiement terminé !

Votre instance Arborisis est maintenant en production sur Jelastic Infomaniak.

**Accès** :
- Frontend : https://arborisis.example.com
- Admin : https://arborisis.example.com/wp-admin
- API : https://arborisis.example.com/wp-json/arborisis/v1/

**Support** :
- Jelastic : support@infomaniak.com
- Documentation : `README.md`, `INSTALLATION.md`
