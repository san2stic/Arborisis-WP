# 🌐 Configurer MinIO avec Cloudflare Tunnel

## Problème Actuel

L'upload échoue avec **403 Forbidden** car le navigateur essaie d'accéder à :
```
http://localhost:9000/arborisis-audio/sounds/...
```

Le navigateur client ne peut pas accéder à `localhost:9000` du serveur !

## Solution : Exposer MinIO via Cloudflare

### Option 1 : Sous-domaine Dédié (RECOMMANDÉ)

Créer un sous-domaine `s3.arborisis.social` pour MinIO.

#### Étape 1 : Configurer le Tunnel Cloudflare

1. Aller sur https://one.dash.cloudflare.com/
2. Naviguer vers **Access** → **Tunnels**
3. Trouver votre tunnel (celui qui utilise le token dans `.env`)
4. Cliquer sur **Configure**
5. Aller dans l'onglet **Public Hostname**

#### Étape 2 : Ajouter un Hostname pour MinIO

Cliquer sur **Add a public hostname** :

**Configuration** :
- **Subdomain** : `s3`
- **Domain** : `arborisis.social`
- **Path** : (laisser vide)
- **Type** : `HTTP`
- **URL** : `minio:9000`

Cliquer **Save**.

#### Étape 3 : Mettre à Jour .env sur le Serveur

```bash
cd ~/Arborisis-WP
nano .env
```

Changer :
```bash
# Avant
S3_PUBLIC_ENDPOINT=http://localhost:9000

# Après
S3_PUBLIC_ENDPOINT=https://s3.arborisis.social
```

Sauvegarder (Ctrl+O, Enter, Ctrl+X).

#### Étape 4 : Redémarrer WordPress

```bash
sudo docker compose restart wordpress
```

#### Étape 5 : Tester

Aller sur https://arborisis.social/upload et essayer d'uploader un fichier.

L'URL devrait maintenant être :
```
https://s3.arborisis.social/arborisis-audio/sounds/...
```

---

### Option 2 : Path-based (Alternative)

Si vous ne voulez pas créer de sous-domaine, vous pouvez utiliser un path :

#### Dans Cloudflare Tunnel :

**Public Hostname** :
- **Subdomain** : `arborisis`
- **Domain** : `social`
- **Path** : `/s3`
- **Type** : `HTTP`
- **URL** : `minio:9000`

#### Dans .env :

```bash
S3_PUBLIC_ENDPOINT=https://arborisis.social/s3
```

**Note** : Cette option peut nécessiter une configuration supplémentaire pour les path rewrites.

---

## Vérification

### Test 1 : Accès Direct

Ouvrir dans le navigateur :
```
https://s3.arborisis.social/minio/health/live
```

Devrait afficher du XML indiquant que MinIO est accessible.

### Test 2 : Bucket Public

```
https://s3.arborisis.social/arborisis-audio/
```

Devrait afficher un XML listant le bucket (ou 404 si vide, mais pas 403).

### Test 3 : Upload

Essayer d'uploader un fichier audio sur :
```
https://arborisis.social/upload
```

---

## Sécurité

### CORS (Cross-Origin Resource Sharing)

MinIO doit autoriser les requêtes depuis `arborisis.social`. Configurer CORS :

```bash
# Se connecter à MinIO
sudo docker exec -it arborisis-minio sh

# Configurer le client mc
mc alias set myminio http://localhost:9000 'Zabou007**Jule' 'Zabou007**Jule'

# Configurer CORS pour le bucket
mc anonymous set-json /tmp/cors.json myminio/arborisis-audio
```

Créer `/tmp/cors.json` avec :
```json
{
  "CORSRules": [
    {
      "AllowedOrigins": ["https://arborisis.social"],
      "AllowedMethods": ["GET", "PUT", "POST"],
      "AllowedHeaders": ["*"],
      "ExposeHeaders": ["ETag"]
    }
  ]
}
```

Ou utiliser la commande simplifiée :
```bash
sudo docker exec arborisis-minio sh -c 'cat > /tmp/cors.json << EOF
{
  "CORSRules": [
    {
      "AllowedOrigins": ["https://arborisis.social"],
      "AllowedMethods": ["GET", "PUT", "POST"],
      "AllowedHeaders": ["*"],
      "ExposeHeaders": ["ETag"]
    }
  ]
}
EOF
mc alias set myminio http://localhost:9000 "Zabou007**Jule" "Zabou007**Jule"
mc admin config set myminio api cors_allowed_origins=https://arborisis.social'
```

### Permissions Bucket

Le bucket doit être accessible en écriture pour les uploads signés :

```bash
sudo docker exec arborisis-minio sh -c "mc alias set myminio http://localhost:9000 'Zabou007**Jule' 'Zabou007**Jule' && mc anonymous set download myminio/arborisis-audio"
```

---

## Troubleshooting

### Erreur 403 Forbidden

**Cause** : Signature AWS invalide ou bucket non accessible

**Solutions** :
1. Vérifier que `S3_PUBLIC_ENDPOINT` est correct dans `.env`
2. Redémarrer WordPress : `sudo docker compose restart wordpress`
3. Vérifier la configuration CORS
4. Vérifier les permissions du bucket

### Erreur CORS

**Symptôme** : Console browser montre erreur CORS

**Solution** : Configurer CORS (voir section Sécurité)

### Tunnel ne fonctionne pas

**Vérifier** :
```bash
sudo docker compose logs cloudflared
```

Devrait montrer :
```
Connection to Cloudflare edge established
Registered tunnel connection
```

---

## Configuration Finale

Une fois MinIO accessible via HTTPS, mettre à jour `.env.production.local` aussi :

```bash
cd ~/Arborisis-WP
nano .env.production.local
```

Changer :
```bash
S3_PUBLIC_ENDPOINT=https://s3.arborisis.social
```

Puis commit :
```bash
git add .env.production.local
git commit -m "Update S3_PUBLIC_ENDPOINT to use Cloudflare tunnel"
git push
```

---

## Résumé des Commandes

```bash
# 1. Configurer le tunnel dans Cloudflare Dashboard
# 2. Mettre à jour .env
echo 'S3_PUBLIC_ENDPOINT=https://s3.arborisis.social' >> ~/Arborisis-WP/.env

# 3. Configurer CORS dans MinIO
sudo docker exec arborisis-minio sh -c "mc alias set myminio http://localhost:9000 'Zabou007**Jule' 'Zabou007**Jule' && mc admin config set myminio api cors_allowed_origins=https://arborisis.social"

# 4. Redémarrer MinIO pour appliquer CORS
sudo docker compose restart minio

# 5. Redémarrer WordPress pour charger le nouveau endpoint
sudo docker compose restart wordpress

# 6. Tester l'upload
# https://arborisis.social/upload
```

---

## Architecture Finale

```
Navigateur
    ↓
https://arborisis.social/upload
    ↓
[Cloudflare Tunnel] → WordPress → Génère URL presignée
    ↓
https://s3.arborisis.social/arborisis-audio/...
    ↓
[Cloudflare Tunnel] → MinIO
    ↓
Stockage fichier ✅
```

L'upload sera alors 100% fonctionnel ! 🚀
