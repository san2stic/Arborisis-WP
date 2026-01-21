# 🐳 Arborisis - Docker Deployment Guide

Guide complet pour déployer Arborisis avec Docker et Cloudflared sur votre serveur self-hosted.

## 📋 Prérequis

- Docker Engine 20.10+
- Docker Compose 2.0+
- Compte Cloudflare (gratuit)
- Minimum 4GB RAM
- 50GB espace disque disponible

## 🚀 Déploiement Initial

### 1. Configuration de l'environnement

```bash
# Copier le template d'environnement
cp .env.production .env.production.local

# Éditer avec vos valeurs
nano .env.production.local
```

**Variables critiques à modifier:**

- `WP_ADMIN_PASSWORD`: Mot de passe admin WordPress
- `DB_PASSWORD`: Mot de passe de la base de données
- `MYSQL_ROOT_PASSWORD`: Mot de passe root MySQL
- `OPENSEARCH_PASSWORD`: Mot de passe OpenSearch (min 8 caractères avec majuscule, minuscule, chiffre, symbole)
- `S3_ACCESS_KEY` et `S3_SECRET_KEY`: Clés d'accès MinIO
- `CLOUDFLARE_TUNNEL_TOKEN`: Token du tunnel Cloudflare

**Générer les WordPress salts:**
```bash
curl https://api.wordpress.org/secret-key/1.1/salt/
```

Copiez les valeurs générées dans votre `.env.production.local`.

### 2. Configuration du Tunnel Cloudflare

1. Connectez-vous à [Cloudflare Zero Trust](https://one.dash.cloudflare.com/)
2. Allez dans **Networks** → **Tunnels**
3. Cliquez sur **Create a tunnel**
4. Donnez-lui un nom (ex: `arborisis`)
5. Sélectionnez **Docker** comme environnement
6. Copiez le token généré
7. Configurez les hostnames:
   - **Public hostname**: `arborisis.votre-domaine.com`
   - **Service**: `http://wordpress:80`

Collez le token dans `CLOUDFLARE_TUNNEL_TOKEN` de votre `.env.production.local`.

### 3. Configuration DNS

Dans votre zone DNS Cloudflare, le tunnel créera automatiquement un enregistrement CNAME pour votre sous-domaine.

### 4. Build et démarrage

```bash
# Build des images
docker-compose build

# Démarrer tous les services
docker-compose up -d

# Vérifier le statut
docker-compose ps

# Suivre les logs
docker-compose logs -f wordpress
```

### 5. Initialisation de WordPress

WordPress s'installera automatiquement au premier démarrage. Surveillez les logs:

```bash
docker-compose logs -f wordpress
```

Une fois l'installation terminée, accédez à votre site via l'URL configurée dans Cloudflare.

### 6. Initialisation de l'index OpenSearch

Exécutez le script d'initialisation:

```bash
docker exec arborisis-opensearch bash /usr/share/opensearch/init.sh
```

Vérifiez que l'index a été créé:

```bash
docker exec arborisis-opensearch curl -k -u admin:YOUR_PASSWORD https://localhost:9200/_cat/indices
```

### 7. Configuration de MinIO (S3)

1. Accédez à la console MinIO via tunnel Cloudflare (créez un hostname public vers `minio:9001`)
2. Connectez-vous avec vos `S3_ACCESS_KEY` et `S3_SECRET_KEY`
3. Créez le bucket `arborisis-audio`
4. Configurez la policy publique pour les lectures:
   - Bucket → Manage → Access Policy
   - Ajoutez une policy de lecture publique

### 8. Configuration des Cron Jobs

Les cron jobs doivent être configurés sur votre système hôte:

```bash
# Ouvrir crontab
crontab -e

# Ajouter ces lignes:
*/5 * * * * docker exec arborisis-wordpress wp arborisis process-opensearch-queue --allow-root
0 * * * * docker exec arborisis-wordpress wp arborisis aggregate-plays --allow-root
15 * * * * docker exec arborisis-wordpress wp arborisis compute-trending --allow-root
0 */6 * * * docker exec arborisis-wordpress wp arborisis warm-cache --allow-root
0 3 * * * docker exec arborisis-wordpress wp arborisis cleanup-plays --allow-root
```

## 🔧 Gestion et Maintenance

### Commandes utiles

```bash
# Voir tous les conteneurs
docker-compose ps

# Logs d'un service spécifique
docker-compose logs -f wordpress
docker-compose logs -f mysql
docker-compose logs -f opensearch

# Redémarrer un service
docker-compose restart wordpress

# Arrêter tous les services
docker-compose down

# Arrêter et supprimer les volumes (⚠️ PERTE DE DONNÉES)
docker-compose down -v

# Rebuild après modifications
docker-compose up -d --build

# Accéder au shell WordPress
docker exec -it arborisis-wordpress bash

# Exécuter des commandes WP-CLI
docker exec arborisis-wordpress wp plugin list --allow-root
docker exec arborisis-wordpress wp theme list --allow-root
```

### Sauvegardes

**Base de données:**
```bash
# Backup
docker exec arborisis-mysql mysqldump -u root -p${MYSQL_ROOT_PASSWORD} arborisis > backup-$(date +%Y%m%d).sql

# Restore
docker exec -i arborisis-mysql mysql -u root -p${MYSQL_ROOT_PASSWORD} arborisis < backup-20260121.sql
```

**Fichiers uploads:**
```bash
# Backup
docker run --rm \
  -v arborisis-wordpress_wordpress_data:/data \
  -v $(pwd):/backup \
  alpine tar czf /backup/uploads-$(date +%Y%m%d).tar.gz -C /data wp-content/uploads

# Restore
docker run --rm \
  -v arborisis-wordpress_wordpress_data:/data \
  -v $(pwd):/backup \
  alpine tar xzf /backup/uploads-20260121.tar.gz -C /data
```

**Volumes complets:**
```bash
# Backup de tous les volumes
docker run --rm \
  -v arborisis-wordpress_mysql_data:/mysql \
  -v arborisis-wordpress_opensearch_data:/opensearch \
  -v arborisis-wordpress_minio_data:/minio \
  -v $(pwd):/backup \
  alpine tar czf /backup/volumes-backup-$(date +%Y%m%d).tar.gz -C / mysql opensearch minio
```

### Mise à jour

```bash
# 1. Sauvegarder tout
# (voir section Sauvegardes)

# 2. Pull les dernières modifications
git pull origin main

# 3. Rebuild et redémarrer
docker-compose down
docker-compose build --no-cache
docker-compose up -d

# 4. Vérifier les logs
docker-compose logs -f
```

## 🔍 Monitoring et Health Checks

### Vérifier la santé des services

```bash
# Health check endpoint WordPress
curl https://votre-domaine.com/wp-json/arborisis/v1/health

# Status des conteneurs
docker-compose ps

# OpenSearch cluster health
docker exec arborisis-opensearch curl -k -u admin:PASSWORD https://localhost:9200/_cluster/health?pretty

# Redis ping
docker exec arborisis-redis redis-cli ping
```

### Métriques de performance

```bash
# Utilisation des ressources
docker stats

# Taille des volumes
docker system df -v

# Logs d'erreurs PHP
docker exec arborisis-wordpress tail -f /var/log/php/error.log

# Logs d'erreurs Nginx
docker exec arborisis-wordpress tail -f /var/log/nginx/error.log
```

## 🐛 Troubleshooting

### WordPress ne démarre pas

```bash
# Vérifier les logs
docker-compose logs wordpress

# Vérifier la connexion MySQL
docker exec arborisis-wordpress wp db check --allow-root

# Réinitialiser les permissions
docker exec arborisis-wordpress chown -R www-data:www-data /var/www/html/wp-content
```

### OpenSearch ne démarre pas

```bash
# Vérifier les logs
docker-compose logs opensearch

# Vérifier les ulimits
docker exec arborisis-opensearch ulimit -a

# Le mot de passe doit contenir au moins 8 caractères avec maj, min, chiffre, symbole
```

### Problèmes de connexion Cloudflare

```bash
# Vérifier les logs du tunnel
docker-compose logs cloudflared

# Vérifier le token
echo $CLOUDFLARE_TUNNEL_TOKEN

# Redémarrer le tunnel
docker-compose restart cloudflared
```

### Uploads qui échouent

```bash
# Vérifier les permissions
docker exec arborisis-wordpress ls -la /var/www/html/wp-content/uploads

# Vérifier la config PHP
docker exec arborisis-wordpress php -i | grep upload_max_filesize

# Vérifier MinIO
docker exec arborisis-minio mc admin info local
```

### Performance lente

```bash
# Vérifier l'utilisation RAM/CPU
docker stats

# Vérifier le cache Redis
docker exec arborisis-redis redis-cli INFO stats

# Activer le debug mode temporairement
docker exec arborisis-wordpress wp config set WP_DEBUG true --allow-root
docker exec arborisis-wordpress wp config set WP_DEBUG_LOG true --allow-root
```

## 🔒 Sécurité

### Bonnes pratiques

1. **Changez tous les mots de passe par défaut** immédiatement
2. **Gardez `.env.production.local` en sécurité** (ne jamais commit)
3. **Mettez à jour régulièrement** les images Docker
4. **Activez 2FA** dans Cloudflare
5. **Limitez l'accès SSH** aux IPs de confiance
6. **Utilisez des clés SSH** au lieu de mots de passe
7. **Configurez un firewall** (UFW ou iptables)

### Ports exposés

Par défaut, **aucun port n'est exposé** en dehors de Docker. Tout le trafic passe par Cloudflare Tunnel de manière sécurisée.

Si vous avez besoin d'accès direct (déconseillé):
```yaml
# Dans docker-compose.yml, ajoutez sous un service:
ports:
  - "127.0.0.1:3306:3306"  # MySQL accessible uniquement en local
```

## 📊 Architecture

```
Internet
    ↓
Cloudflare Tunnel (cloudflared)
    ↓
Nginx (reverse proxy)
    ↓
PHP-FPM (WordPress)
    ↓
├── MySQL (database)
├── Redis (cache)
├── OpenSearch (search engine)
└── MinIO (S3 storage)
```

Tous les services communiquent via le réseau Docker interne `arborisis-network`.

## 📝 Licence

GPL-2.0-or-later

## 🆘 Support

Pour toute question ou problème:
1. Vérifiez les logs: `docker-compose logs -f`
2. Consultez la section Troubleshooting ci-dessus
3. Ouvrez une issue sur GitHub
