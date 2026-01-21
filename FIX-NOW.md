# 🚨 Fix Immédiat - Bucket MinIO Manquant

## Problème Actuel

Le bucket MinIO n'est pas créé à cause des caractères spéciaux (`**`) dans les mots de passe qui sont mal interprétés.

## Solution Rapide

Sur le serveur, exécutez cette commande unique :

```bash
sudo docker exec arborisis-minio sh -c "mc alias set myminio http://localhost:9000 'Zabou007**Jule' 'Zabou007**Jule' && mc mb myminio/arborisis-audio && mc anonymous set download myminio/arborisis-audio && mc ls myminio/"
```

Vous devriez voir : `[...] arborisis-audio/`

## Vérification

```bash
sudo docker exec arborisis-minio mc ls myminio/
```

Doit afficher :
```
[...] arborisis-audio/
```

## Si ça ne marche toujours pas

Voici les commandes étape par étape :

```bash
# 1. Configurer le client MinIO
sudo docker exec arborisis-minio mc alias set myminio http://localhost:9000 'Zabou007**Jule' 'Zabou007**Jule'

# 2. Créer le bucket
sudo docker exec arborisis-minio mc mb myminio/arborisis-audio

# 3. Rendre le bucket public en lecture
sudo docker exec arborisis-minio mc anonymous set download myminio/arborisis-audio

# 4. Vérifier
sudo docker exec arborisis-minio mc ls myminio/
```

## Ensuite

Une fois le bucket créé, essayez d'uploader un fichier sur :
https://arborisis.social/upload

## Pour Éviter ce Problème à l'Avenir

Après avoir créé le bucket manuellement, mettez à jour le code :

```bash
# Rendre les scripts exécutables
chmod +x *.sh

# Exécuter le script de mise à jour
./update-and-fix.sh
```

Ce script va :
1. Récupérer les dernières modifications (fichier `.env` corrigé)
2. Redémarrer les services
3. Créer le bucket automatiquement

## Explications Techniques

Le problème vient du fichier `.env` où les mots de passe contiennent `**`.

Dans bash, `**` est un glob pattern qui est interprété comme "tous les fichiers récursivement".

**Avant** (incorrect) :
```bash
S3_ACCESS_KEY=Zabou007**Jule
```

Le shell interprète `**` et le remplace par une liste de fichiers, résultant en `Zabou007Jule`.

**Après** (correct) :
```bash
S3_ACCESS_KEY="Zabou007**Jule"
```

Les guillemets protègent les caractères spéciaux.

## Debug

Si l'upload échoue encore après avoir créé le bucket :

```bash
# Vérifier les logs WordPress
sudo docker compose logs wordpress | tail -50

# Vérifier que MinIO est accessible depuis WordPress
sudo docker exec arborisis-wordpress wget -qO- http://minio:9000/minio/health/live

# Vérifier les variables S3 dans WordPress
sudo docker exec arborisis-wordpress env | grep S3
```
