# Quick Server Deployment - Final Version ✅

## ⚡ Deploy Now (This Will Work!)

On your server:

```bash
cd ~/Arborisis-WP
git pull origin main

# Make scripts executable (first time only)
chmod +x deploy.sh quote-env.sh

# Deploy
./deploy.sh .env.production.local build
./deploy.sh .env.production.local up
```

**That's it!** No warnings, no errors. The scripts handle all the special character issues automatically.


## How It Works

The deployment process now has **three layers of protection**:

1. **`quote-env.sh`** - Wraps all environment values in double quotes
   - Prevents Docker Compose from parsing `$variables` in WordPress salts
   - Escapes special characters properly

2. **`deploy.sh`** - Orchestrates the deployment
   - Calls `quote-env.sh` to create properly formatted `.env` file
   - Validates required variables exist
   - Runs docker compose commands

3. **`docker-compose.yml`** - Reads `.env` automatically
   - No more `env_file` references (removed to prevent conflicts)
   - Variables are safely interpolated: `${DB_NAME}`, `${DB_PASSWORD}`, etc.

## What Was Fixed

| Issue | Cause | Solution |
|-------|-------|----------|
| `composer.lock` missing | In `.gitignore` | Removed from gitignore ✅ |
| Bash syntax errors | Special chars in salts | Created safe loader scripts ✅ |
| Variables parsing as fragments | Docker Compose `$` interpretation | Quote all values ✅ |
| Env vars not loaded | `sudo` doesn't preserve env | Use `.env` file instead ✅ |

## Available Commands

```bash
./deploy.sh <env-file> <command>

# Examples:
./deploy.sh .env.production.local build   # Build images
./deploy.sh .env.production.local up      # Start containers
./deploy.sh .env.production.local restart # Restart all
./deploy.sh .env.production.local logs    # View logs
./deploy.sh .env.production.local down    # Stop all
./deploy.sh .env.production.local ps      # Show status
```

## Alternative: Use Updated docker-compose.env.sh

This also works now (uses `sudo -E` to preserve environment):

```bash
./docker-compose.env.sh build
./docker-compose.env.sh up
```

## After Deployment

```bash
# Check containers are running
sudo docker compose ps

# View WordPress logs
sudo docker compose logs -f wordpress

# Access bash in WordPress container
sudo docker compose exec wordpress bash
```

## First Time WordPress Setup

```bash
# Install WordPress
sudo docker compose exec wordpress wp core install \
  --url="https://yourdomain.com" \
  --title="Arborisis" \
  --admin_user="admin" \
  --admin_password="YourSecurePassword" \
  --admin_email="admin@yourdomain.com"

# Initialize Arborisis indexes (if plugin supports it)
sudo docker compose exec wordpress wp arborisis init
```

## Troubleshooting

If variables still show as blank:

1. Check `.env.production.local` has values:
   ```bash
   grep "DB_NAME" .env.production.local
   ```

2. Check `.env` was created:
   ```bash
   cat .env | head -20
   ```

3. Remove docker-compose.yml env_file references if needed:
   ```bash
   # Edit docker-compose.yml and comment out these lines:
   # env_file: .env.production
   ```

## Clean Start (if needed)

```bash
# Stop everything
sudo docker compose down -v

# Remove old images
sudo docker system prune -a -f

# Rebuild from scratch
./deploy.sh .env.production.local build
./deploy.sh .env.production.local up
```
