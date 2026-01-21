# 🚀 Déploiement Complet - Arborisis Audio Upload

## Guide Étape par Étape

Ce guide contient **TOUTES** les étapes nécessaires pour déployer et réparer le système d'upload audio.

---

## ✅ Étape 1 : Récupérer les Derniers Changements

```bash
cd ~/Arborisis-WP
git pull
```

**Fichiers mis à jour** :
- Plugin audio avec autoloader Composer
- Scripts de déploiement
- Configuration .env corrigée
- Documentation complète

---

## ✅ Étape 2 : Copier la Configuration

```bash
cp .env.production.local .env
```

Cela crée le fichier `.env` avec les mots de passe correctement quotés.

---

## ✅ Étape 3 : Rebuilder l'Image Docker

**C'EST L'ÉTAPE LA PLUS IMPORTANTE !**

Le plugin a été modifié pour charger le SDK AWS, donc il faut rebuilder l'image :

```bash
chmod +x *.sh
sudo ./rebuild-and-deploy.sh
```

**Attendez environ 5-10 minutes** pendant que Docker :
- Build l'image avec Composer (installe AWS SDK)
- Compile le thème avec Node.js
- Redémarre WordPress

Vous verrez :
```
🔨 Rebuilding WordPress image...
This may take several minutes...
```

---

## ✅ Étape 4 : Créer le Bucket MinIO

Une fois WordPress redémarré, créer le bucket :

```bash
sudo docker exec arborisis-minio sh -c "mc alias set myminio http://localhost:9000 'Zabou007**Jule' 'Zabou007**Jule' && mc mb myminio/arborisis-audio && mc anonymous set download myminio/arborisis-audio"
```

Vérifier que le bucket existe :
```bash
sudo docker exec arborisis-minio mc ls myminio/
```

Vous devriez voir :
```
[...] arborisis-audio/
```

---

## ✅ Étape 5 : Vérifier que Tout Fonctionne

```bash
sudo ./check-s3-env.sh
```

**Résultat attendu** :
```
✅ S3 environment variables are set
✅ MinIO is accessible from WordPress
✅ Bucket exists: arborisis-audio
```

---

## ✅ Étape 6 : Tester l'Upload

1. Ouvrir : https://arborisis.social/upload
2. Sélectionner un fichier audio (MP3, WAV, FLAC, OGG)
3. Remplir le titre et la licence
4. Cliquer sur "Publier l'Enregistrement"

**Si ça fonctionne** :
- Barre de progression s'affiche
- Upload vers S3 réussit
- Création du post sound
- Redirection vers la page du son

**Si ça échoue** :
Aller à l'Étape 7 (Troubleshooting)

---

## 🐛 Étape 7 : Troubleshooting

### Erreur : "Class Aws\S3\S3Client not found"

❌ **Problème** : L'autoloader Composer n'est pas chargé

✅ **Solution** : Rebuilder l'image (Étape 3)

```bash
sudo docker compose logs wordpress | grep "S3Client"
```

Si vous voyez encore cette erreur après le rebuild, vérifier :
```bash
# L'autoloader existe-t-il ?
sudo docker exec arborisis-wordpress ls -la /var/www/html/vendor/autoload.php

# Le plugin charge-t-il l'autoloader ?
sudo docker exec arborisis-wordpress grep -n "vendor/autoload" /var/www/html/wp-content/plugins/arborisis-audio/arborisis-audio.php
```

### Erreur : "Bucket does not exist"

❌ **Problème** : Le bucket MinIO n'est pas créé

✅ **Solution** : Exécuter l'Étape 4

### Erreur : "Failed to connect to minio:9000"

❌ **Problème** : MinIO n'est pas accessible

✅ **Solution** : Redémarrer MinIO
```bash
sudo docker compose restart minio
sleep 10
# Puis recréer le bucket (Étape 4)
```

### Erreur 500 générique

❌ **Problème** : Erreur WordPress non identifiée

✅ **Solution** : Activer le debug et voir les logs
```bash
sudo ./enable-debug.sh
# Essayer l'upload
sudo docker compose logs wordpress | tail -100
```

---

## 📊 Commandes Utiles

### Voir l'état des conteneurs
```bash
sudo docker compose ps
```

### Voir les logs en temps réel
```bash
sudo docker compose logs -f wordpress
```

### Redémarrer un service
```bash
sudo docker compose restart wordpress
sudo docker compose restart minio
```

### Vérifier les variables S3
```bash
sudo docker exec arborisis-wordpress env | grep S3
```

### Lister les fichiers uploadés
```bash
sudo docker exec arborisis-minio mc ls myminio/arborisis-audio/
```

---

## 🎯 Checklist Complète

Avant de dire que c'est terminé, vérifier :

- [ ] `git pull` exécuté
- [ ] `.env` copié depuis `.env.production.local`
- [ ] Image Docker rebuildée avec `./rebuild-and-deploy.sh`
- [ ] Bucket MinIO créé et visible avec `mc ls`
- [ ] Variables S3 présentes dans WordPress (`./check-s3-env.sh`)
- [ ] Upload testé et fonctionnel
- [ ] Fichier visible dans MinIO
- [ ] Post sound créé dans WordPress

---

## 🔄 Si Vous Devez Tout Recommencer

Reset complet (⚠️ supprime les données) :

```bash
# Arrêter tout
sudo docker compose down

# Supprimer les volumes
sudo docker volume rm arborisiswordpress_minio_data
sudo docker volume rm arborisiswordpress_mysql_data
sudo docker volume rm arborisiswordpress_redis_data
sudo docker volume rm arborisiswordpress_opensearch_data

# Rebuilder et redémarrer
sudo docker compose build --no-cache
sudo docker compose up -d

# Attendre 2 minutes
sleep 120

# Créer le bucket
sudo docker exec arborisis-minio sh -c "mc alias set myminio http://localhost:9000 'Zabou007**Jule' 'Zabou007**Jule' && mc mb myminio/arborisis-audio && mc anonymous set download myminio/arborisis-audio"

# Tester
```

---

## 📞 Support

Si rien ne fonctionne après toutes ces étapes, récupérer les logs :

```bash
sudo docker compose logs wordpress > debug-wordpress.txt
sudo docker compose logs minio > debug-minio.txt
sudo docker compose ps > debug-status.txt
sudo docker exec arborisis-wordpress env | grep S3 > debug-env.txt
```

Et m'envoyer ces fichiers : bastienjavaux@icloud.com
