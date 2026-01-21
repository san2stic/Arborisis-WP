# Quick Server Deployment - Fixed Version

## The Problem We Fixed

1. **composer.lock missing** → Added to git ✅
2. **Bash syntax errors from WordPress salts** → Created safe loading scripts ✅  
3. **Environment variables not passing through sudo** → Multiple solutions ✅

## ⚡ Fastest Way to Deploy (Use This!)

On your server, run these commands:

```bash
cd ~/Arborisis-WP
git pull origin main

# Make script executable (first time only)
chmod +x deploy.sh

# Deploy with one command
./deploy.sh .env.production.local build
./deploy.sh .env.production.local up
```

## How It Works

The `deploy.sh` script:
1. Copies your `.env.production.local` to `.env`
2. Docker Compose reads `.env` automatically (no sudo issues!)
3. Validates required variables exist
4. Runs the docker compose command

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
