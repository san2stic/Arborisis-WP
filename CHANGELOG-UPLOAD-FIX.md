# Correction du Système d'Upload Audio

## Problème Initial

Erreur 500 lors de l'upload de fichiers audio :
```
POST /wp-json/arborisis/v1/upload/presign 500 (Internal Server Error)
Error: Failed to get presigned URL
```

## Cause

1. **MinIO non accessible depuis le navigateur** : Le conteneur MinIO n'exposait pas de ports, donc le navigateur ne pouvait pas uploader directement vers S3
2. **Endpoint S3 incorrect** : L'endpoint `http://minio:9000` n'est accessible que depuis l'intérieur du réseau Docker
3. **Bucket non créé** : Pas d'initialisation automatique du bucket S3
4. **Format de données incorrect** : Mismatch entre le format attendu par le backend et celui envoyé par le frontend

## Corrections Apportées

### 1. Configuration S3 avec Double Endpoint

**Fichier** : `wp-content/plugins/arborisis-audio/includes/class-s3-client.php`

- Ajout du support pour `S3_PUBLIC_ENDPOINT` séparé de `S3_ENDPOINT`
- `S3_ENDPOINT` : utilisé par WordPress pour communiquer avec MinIO (réseau Docker interne)
- `S3_PUBLIC_ENDPOINT` : utilisé pour les URLs presignées accessibles depuis le navigateur

```php
// Replace internal endpoint with public endpoint if configured
$internal_endpoint = getenv('S3_ENDPOINT');
$public_endpoint = getenv('S3_PUBLIC_ENDPOINT') ?: $internal_endpoint;

if ($internal_endpoint && $public_endpoint && $internal_endpoint !== $public_endpoint) {
    $url = str_replace($internal_endpoint, $public_endpoint, $url);
}
```

### 2. Exposition des Ports MinIO

**Fichier** : `docker-compose.yml`

```yaml
minio:
  ports:
    - "9000:9000"  # MinIO API
    - "9001:9001"  # MinIO Console
  volumes:
    - minio_data:/data  # Changé de /mnt/data à volume Docker
```

### 3. Initialisation Automatique du Bucket

**Fichier** : `docker/init-minio.sh` (nouveau)

Script qui s'exécute automatiquement au démarrage et :
- Crée le bucket `arborisis-audio` s'il n'existe pas
- Configure les permissions publiques en lecture

**Fichier** : `docker-compose.yml`

Ajout du service `minio-init` :
```yaml
minio-init:
  image: minio/mc:latest
  command: /init-minio.sh
  depends_on:
    minio:
      condition: service_healthy
  restart: "no"
```

### 4. Correction du Format de Données Frontend

**Fichier** : `wp-content/themes/arborisis/page-upload.php`

- Correction : `s3_key` → `key` (ligne 307)
- Restructuration des données pour correspondre au format attendu par l'API :

```javascript
const formData = {
    key: uploadedS3Key,
    sound_data: {
        title: ...,
        content: ...,
        geo: { lat, lon, name },
        tags: [...],
        license: ...,
        recorded_at: ...,
        equipment: ...
    }
};
```

### 5. Variables d'Environnement

**Fichier** : `.env` et `.env.production.local`

Ajout de toutes les variables S3 nécessaires :
```bash
S3_ENDPOINT=http://minio:9000
S3_PUBLIC_ENDPOINT=http://localhost:9000  # local
S3_BUCKET=arborisis-audio
S3_REGION=us-east-1
S3_ACCESS_KEY=...
S3_SECRET_KEY=...
S3_PREFIX=sounds/
```

**Fichier** : `docker-compose.yml`

Passage de toutes les variables d'environnement au conteneur WordPress :
```yaml
environment:
  - S3_ENDPOINT=http://minio:9000
  - S3_PUBLIC_ENDPOINT=${S3_PUBLIC_ENDPOINT}
  - S3_BUCKET=${S3_BUCKET}
  - S3_REGION=${S3_REGION}
  - S3_ACCESS_KEY=${S3_ACCESS_KEY}
  - S3_SECRET_KEY=${S3_SECRET_KEY}
  - S3_PREFIX=${S3_PREFIX}
  - UPLOAD_MAX_MB=${UPLOAD_MAX_MB}
  - AUDIO_ALLOWED_MIMES=${AUDIO_ALLOWED_MIMES}
  - RATE_LIMIT_PER_MINUTE=${RATE_LIMIT_PER_MINUTE}
```

### 6. Amélioration du Debugging

**Fichier** : `wp-content/themes/arborisis/page-upload.php`

Ajout de meilleurs messages d'erreur :
```javascript
if (!presignResponse.ok) {
    const errorData = await presignResponse.json();
    console.error('Presign error:', errorData);
    throw new Error(errorData.message || 'Failed to get presigned URL');
}
```

## Déploiement

### Développement Local

```bash
# 1. Copier la configuration
cp .env.production.local .env

# 2. Démarrer les services
docker compose up -d

# 3. Le bucket sera créé automatiquement
```

### Production

Pour la production sur `arborisis.social`, modifier dans `.env` :

```bash
S3_PUBLIC_ENDPOINT=https://s3.arborisis.social
# ou
S3_PUBLIC_ENDPOINT=https://arborisis.social:9000
```

Et configurer un reverse proxy HTTPS pour MinIO.

## Tests

1. Accéder à la page d'upload : `/upload`
2. Sélectionner un fichier audio (MP3, WAV, FLAC, OGG)
3. L'upload doit se faire avec une barre de progression
4. La publication doit créer le post sound et rediriger vers `/sound/{id}`

## Fichiers Modifiés

- `wp-content/plugins/arborisis-audio/includes/class-s3-client.php`
- `wp-content/themes/arborisis/page-upload.php`
- `docker-compose.yml`
- `.env`
- `.env.production.local`

## Fichiers Créés

- `docker/init-minio.sh`
- `DEPLOY-INSTRUCTIONS.md`
- `CHANGELOG-UPLOAD-FIX.md`

## Prochaines Étapes

1. Tester l'upload complet en local
2. Configurer HTTPS pour MinIO en production
3. Ajouter un reverse proxy Nginx/Cloudflare devant MinIO
4. Configurer les backups du volume `minio_data`
