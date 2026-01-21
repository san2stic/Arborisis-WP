# Quick Start - Arborisis WordPress

## Déploiement Rapide

### Sur le Serveur de Production

```bash
# 1. Cloner le repository
git clone <repository-url>
cd arborisis-wordpress

# 2. Copier le fichier d'environnement
cp .env.production.local .env

# 3. Lancer le déploiement
docker compose build
docker compose up -d

# 4. Attendre que tout démarre (environ 2 minutes)
sleep 120

# 5. Vérifier que tout fonctionne
./fix-production.sh
```

C'est tout ! Le site devrait être accessible.

## Vérification Rapide

```bash
# Voir le statut de tous les conteneurs
docker compose ps

# Tous doivent être "Up" ou "healthy"
```

## Problème d'Upload Audio ?

Si vous avez une erreur 500 lors de l'upload :

```bash
# Exécuter le script de correction
./fix-production.sh

# Vérifier les variables S3
./check-s3-env.sh
```

## Accès aux Services

- **Site Web** : https://arborisis.social
- **MinIO Console** : http://localhost:9001 (credentials dans `.env`)
- **Upload Page** : https://arborisis.social/upload

## Logs

```bash
# Voir tous les logs
docker compose logs -f

# Logs d'un service spécifique
docker compose logs -f wordpress
docker compose logs -f minio
```

## Redémarrer

```bash
# Redémarrer tous les services
docker compose restart

# Redémarrer un service spécifique
docker compose restart wordpress
```

## Arrêter

```bash
# Arrêter tous les services (garde les données)
docker compose down

# Arrêter et supprimer les données (⚠️ ATTENTION)
docker compose down -v
```

## Documentation Complète

- 📘 [Instructions de déploiement détaillées](DEPLOY-INSTRUCTIONS.md)
- 🐛 [Guide de débogage](DEBUG-UPLOAD-ERROR.md)
- 📝 [Changelog](CHANGELOG-UPLOAD-FIX.md)
