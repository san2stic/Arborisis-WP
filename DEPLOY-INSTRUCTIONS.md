# Instructions de Déploiement - Arborisis

## Prérequis

- Docker et Docker Compose installés
- Accès au serveur de production

## Configuration Initiale

1. **Copier le fichier de configuration**
   ```bash
   cp .env.production.local .env
   ```

2. **Éditer les variables d'environnement** (si nécessaire)
   - Pour la production : Les variables sont déjà configurées
   - Pour le développement local : Vérifier que `S3_PUBLIC_ENDPOINT=http://localhost:9000`

## Déploiement

### Option 1 : Utiliser le script de déploiement

```bash
sudo ./docker-compose.env.sh build
sudo ./docker-compose.env.sh up
```

### Option 2 : Commandes Docker Compose manuelles

```bash
# Construire les images
docker compose build

# Démarrer tous les services
docker compose up -d

# Vérifier le statut
docker compose ps
```

## Services Déployés

- **WordPress** : Application principale (port 80)
- **MySQL** : Base de données
- **Redis** : Cache
- **OpenSearch** : Recherche et indexation
- **MinIO** : Stockage S3 (ports 9000-9001)
  - API : port 9000
  - Console Web : port 9001
- **Cloudflared** : Tunnel Cloudflare
- **MinIO Init** : Initialisation automatique du bucket S3 (s'arrête après exécution)

## Vérification

### Vérifier que tous les services sont démarrés

```bash
docker compose ps
```

Tous les services doivent être en état "Up" ou "healthy".

### Vérifier les logs

```bash
# Logs de tous les services
docker compose logs -f

# Logs d'un service spécifique
docker compose logs -f wordpress
docker compose logs -f minio
```

### Tester MinIO

```bash
# Accéder à la console web
open http://localhost:9001
# ou sur le serveur : https://arborisis.social:9001

# Credentials :
# Username: Zabou007**Jule
# Password: Zabou007**Jule
```

## Upload de Fichiers Audio

L'upload se fait automatiquement vers MinIO/S3 :

1. Le bucket `arborisis-audio` est créé automatiquement au démarrage
2. Les fichiers sont uploadés directement depuis le navigateur vers S3
3. Les URLs publiques sont générées pour la lecture

### Configuration S3

- **Endpoint interne** (pour WordPress) : `http://minio:9000`
- **Endpoint public** (pour le navigateur) : `http://localhost:9000` (local) ou `https://arborisis.social:9000` (production)
- **Bucket** : `arborisis-audio`
- **Région** : `us-east-1`

## Troubleshooting

### Le bucket S3 n'est pas créé

Si le service `minio-init` échoue, créer manuellement le bucket :

```bash
# Se connecter au conteneur MinIO
docker exec -it arborisis-minio /bin/sh

# Configurer mc (MinIO Client)
mc alias set myminio http://localhost:9000 Zabou007**Jule Zabou007**Jule

# Créer le bucket
mc mb myminio/arborisis-audio

# Rendre le bucket public en lecture
mc anonymous set download myminio/arborisis-audio

# Vérifier
mc ls myminio/
```

### Erreur 500 lors de l'upload

1. Vérifier les logs WordPress :
   ```bash
   docker compose logs wordpress | tail -50
   ```

2. Vérifier que MinIO est accessible :
   ```bash
   curl http://localhost:9000/minio/health/live
   ```

3. Vérifier les variables d'environnement dans le conteneur :
   ```bash
   docker exec arborisis-wordpress env | grep S3
   ```

### Redémarrer un service

```bash
# Redémarrer WordPress
docker compose restart wordpress

# Redémarrer MinIO
docker compose restart minio

# Redémarrer tous les services
docker compose restart
```

### Réinitialiser complètement

⚠️ **ATTENTION** : Cela supprimera toutes les données !

```bash
# Arrêter tous les services
docker compose down

# Supprimer les volumes (données)
docker volume rm arborisiswordpress_mysql_data
docker volume rm arborisiswordpress_redis_data
docker volume rm arborisiswordpress_opensearch_data
docker volume rm arborisiswordpress_minio_data

# Redémarrer
docker compose up -d
```

## Notes de Production

### Pour la production (arborisis.social)

- MinIO doit être accessible via HTTPS
- Configurer `S3_PUBLIC_ENDPOINT=https://s3.arborisis.social` ou similaire
- Ajouter un certificat SSL pour MinIO
- Considérer l'utilisation d'un reverse proxy (Nginx/Cloudflare) devant MinIO

### Sécurité

- Les ports MinIO (9000-9001) sont exposés - utiliser un firewall en production
- Changer les mots de passe par défaut dans `.env`
- Générer de nouveaux salts WordPress
- Configurer les backups réguliers des volumes Docker

## Support

Pour toute question : bastienjavaux@icloud.com
