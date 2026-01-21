# ❌ Erreur: "Composer dependencies require PHP >= 8.4.0"

## Le Problème

Lors du build Docker, vous obtenez :
```
Composer detected issues in your platform:
Your Composer dependencies require a PHP version ">= 8.4.0".
```

## Pourquoi Cette Erreur ?

Le serveur utilise encore l'**ancien** `composer.lock` qui requiert PHP 8.4.

## Solution : Pull les Derniers Changements

Le `composer.lock` a été corrigé pour requérir PHP 8.2. Il faut le récupérer :

```bash
cd ~/Arborisis-WP

# Pull les derniers changements
git pull

# Vérifier que le composer.lock requiert PHP 8.2
grep -A3 '"platform"' composer.lock
```

Vous devriez voir :
```json
"platform": {
    "php": ">=8.2"
},
```

Si vous voyez `">=8.4"`, le pull n'a pas fonctionné.

## Si git pull ne Fonctionne Pas

### Option 1 : Forcer le Pull

```bash
cd ~/Arborisis-WP

# Sauvegarder vos modifications locales
git stash

# Pull avec force
git pull --rebase

# Réappliquer vos modifications si nécessaire
git stash pop
```

### Option 2 : Reset Complet (⚠️ Perd les modifications locales)

```bash
cd ~/Arborisis-WP

# Sauvegarder les fichiers importants
cp .env .env.backup

# Reset au dernier commit distant
git fetch origin
git reset --hard origin/main

# Restaurer .env
cp .env.backup .env
```

### Option 3 : Régénérer Manuellement

Si vraiment git ne fonctionne pas :

```bash
cd ~/Arborisis-WP

# Supprimer l'ancien lock
rm composer.lock

# Régénérer avec Docker (utilise PHP 8.2)
docker run --rm -v "$(pwd):/app" composer:2 composer update --no-scripts --no-interaction

# Vérifier
grep -A3 '"platform"' composer.lock
```

## Vérification Finale

Après avoir récupéré le bon `composer.lock` :

```bash
# Le fichier doit montrer PHP 8.2
grep '"php".*8' composer.lock

# Si vous voyez ">=8.2", c'est bon !
# Si vous voyez ">=8.4", recommencez
```

## Ensuite : Rebuilder

Une fois le bon `composer.lock` en place :

```bash
# Rebuilder l'image
sudo ./rebuild-and-deploy.sh
```

Le build devrait maintenant réussir !

## Historique du Problème

1. L'ancien `composer.lock` avait été généré avec PHP 8.4
2. Le Dockerfile utilise PHP 8.2
3. Conflit → erreur de build
4. Solution : Régénéré avec `composer:2` (PHP 8.2)
5. Commit : `12dde3b` et précédents

## Vérifier Quel Commit Vous Avez

```bash
git log --oneline -5

# Vous devez voir :
# 12dde3b Update deployment guide with composer.lock fix
# bda7dfb Fix composer.lock for PHP 8.2 compatibility
```

Si vous ne les voyez pas, vous n'avez pas la dernière version → faites `git pull`
