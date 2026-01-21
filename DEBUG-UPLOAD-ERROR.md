# Debug Upload Error 500

## Erreur Actuelle

```
POST /wp-json/arborisis/v1/upload/presign 500 (Internal Server Error)
Error: There has been a critical error on this website
```

## Cause Probable

Le conteneur WordPress n'a pas accès aux variables d'environnement S3, donc la classe `ARB_S3_Client` ne peut pas se connecter à MinIO.

## Solution Rapide (Sur le Serveur)

### Étape 1 : Exécuter le script de correction

```bash
cd /path/to/arborisis-wordpress
./fix-production.sh
```

Ce script va :
- ✅ Créer le fichier `.env` si manquant
- ✅ Vérifier que WordPress tourne
- ✅ Recharger les variables d'environnement
- ✅ Vérifier MinIO et créer le bucket

### Étape 2 : Vérifier les Variables

```bash
./check-s3-env.sh
```

Vous devriez voir :
```
S3_ENDPOINT=http://minio:9000
S3_PUBLIC_ENDPOINT=http://localhost:9000
S3_BUCKET=arborisis-audio
S3_REGION=us-east-1
S3_ACCESS_KEY=...
S3_SECRET_KEY=...
```

### Étape 3 : Tester l'Upload

Retournez sur le site et essayez d'uploader un fichier.

## Debug Manuel

### 1. Vérifier les Variables d'Environnement

```bash
# Vérifier dans le conteneur WordPress
docker exec arborisis-wordpress env | grep S3

# Devrait afficher :
# S3_ENDPOINT=http://minio:9000
# S3_PUBLIC_ENDPOINT=http://localhost:9000
# S3_BUCKET=arborisis-audio
# S3_REGION=us-east-1
# S3_ACCESS_KEY=...
# S3_SECRET_KEY=...
# S3_PREFIX=sounds/
```

❌ **Si vide** : Les variables ne sont pas chargées
```bash
# Solution : Redémarrer WordPress
docker compose restart wordpress
```

### 2. Vérifier MinIO

```bash
# Vérifier que MinIO est accessible
curl http://localhost:9000/minio/health/live

# Devrait retourner : HTTP/1.1 200 OK
```

❌ **Si erreur** : MinIO n'est pas accessible
```bash
# Solution : Démarrer MinIO
docker compose up -d minio
```

### 3. Vérifier le Bucket

```bash
# Lister les buckets
docker exec arborisis-minio mc alias set myminio http://localhost:9000 'Zabou007**Jule' 'Zabou007**Jule'
docker exec arborisis-minio mc ls myminio/

# Devrait afficher : arborisis-audio
```

❌ **Si le bucket n'existe pas** :
```bash
# Créer le bucket manuellement
docker exec arborisis-minio mc mb myminio/arborisis-audio
docker exec arborisis-minio mc anonymous set download myminio/arborisis-audio
```

### 4. Vérifier la Connexion WordPress → MinIO

```bash
# Depuis le conteneur WordPress, tester l'accès à MinIO
docker exec arborisis-wordpress wget -O- http://minio:9000/minio/health/live

# Devrait réussir
```

❌ **Si erreur** : Problème réseau Docker
```bash
# Vérifier le réseau
docker network ls
docker network inspect arborisiswordpress_arborisis-network
```

### 5. Activer le Debug WordPress

```bash
./enable-debug.sh

# Puis essayer l'upload et vérifier les logs
docker exec arborisis-wordpress cat /var/www/html/wp-content/debug.log | tail -50
```

### 6. Vérifier les Logs WordPress

```bash
# Logs en temps réel
docker compose logs -f wordpress

# Dernières 50 lignes
docker compose logs wordpress | tail -50
```

## Erreurs Fréquentes et Solutions

### Erreur : "S3 credentials not configured"

**Cause** : Variables d'environnement S3 manquantes dans WordPress

**Solution** :
```bash
# Vérifier .env existe
ls -la .env

# Si manquant, créer depuis .env.production.local
cp .env.production.local .env

# Redémarrer WordPress
docker compose restart wordpress
```

### Erreur : "Failed to connect to minio:9000"

**Cause** : MinIO n'est pas accessible depuis WordPress

**Solution** :
```bash
# Vérifier MinIO tourne
docker compose ps | grep minio

# Redémarrer MinIO
docker compose restart minio

# Vérifier le réseau
docker exec arborisis-wordpress ping -c 3 minio
```

### Erreur : "Bucket does not exist"

**Cause** : Le bucket `arborisis-audio` n'a pas été créé

**Solution** :
```bash
# Lancer l'initialisation MinIO
docker compose up -d minio-init

# Ou créer manuellement
docker exec arborisis-minio mc alias set myminio http://localhost:9000 'Zabou007**Jule' 'Zabou007**Jule'
docker exec arborisis-minio mc mb myminio/arborisis-audio
docker exec arborisis-minio mc anonymous set download myminio/arborisis-audio
```

### Erreur : "Connection refused to localhost:9000"

**Cause** : Le navigateur ne peut pas accéder à MinIO (problème de `S3_PUBLIC_ENDPOINT`)

**Solution pour Production** :

Dans `.env`, changer :
```bash
# Au lieu de :
S3_PUBLIC_ENDPOINT=http://localhost:9000

# Utiliser :
S3_PUBLIC_ENDPOINT=https://arborisis.social:9000
# ou mieux, un sous-domaine dédié :
S3_PUBLIC_ENDPOINT=https://s3.arborisis.social
```

Puis redémarrer :
```bash
docker compose restart wordpress
```

## Vérification Complète

Script de vérification tout-en-un :

```bash
echo "=== 1. Docker Containers ==="
docker compose ps

echo -e "\n=== 2. S3 Environment Variables ==="
docker exec arborisis-wordpress env | grep "^S3_"

echo -e "\n=== 3. MinIO Health ==="
curl -I http://localhost:9000/minio/health/live

echo -e "\n=== 4. MinIO Buckets ==="
docker exec arborisis-minio mc ls myminio/ 2>/dev/null

echo -e "\n=== 5. WordPress → MinIO Connection ==="
docker exec arborisis-wordpress wget -qO- http://minio:9000/minio/health/live && echo "OK"

echo -e "\n=== 6. WordPress Logs (last 20 lines) ==="
docker compose logs wordpress | tail -20
```

## Si Rien ne Fonctionne

### Reset Complet (⚠️ Perd les données)

```bash
# Arrêter tout
docker compose down

# Supprimer les volumes
docker volume rm arborisiswordpress_minio_data

# Redémarrer
docker compose up -d

# Attendre 60 secondes
sleep 60

# Vérifier
./fix-production.sh
```

## Support

Si l'erreur persiste après toutes ces étapes, récupérer les informations suivantes :

```bash
# 1. Variables d'environnement
docker exec arborisis-wordpress env | grep S3 > debug-env.txt

# 2. Logs WordPress
docker compose logs wordpress > debug-wordpress.txt

# 3. Logs MinIO
docker compose logs minio > debug-minio.txt

# 4. Status conteneurs
docker compose ps > debug-status.txt

# 5. Test de connexion
docker exec arborisis-wordpress wget -v http://minio:9000/minio/health/live 2>&1 > debug-connection.txt
```

Puis envoyer ces fichiers pour analyse.
