# Server Deployment Quick Guide

## Fixed Issues

✅ **composer.lock missing** - Now tracked in git and will be deployed to server
✅ **Docker build optimization** - Added `.dockerignore` to reduce build context size

## Environment Variables Required

Before running `docker compose build`, ensure you have set these environment variables on your server:

### Database Configuration
```bash
export DB_NAME=arborisis
export DB_USER=arborisis_user
export DB_PASSWORD=<secure_password>
export MYSQL_ROOT_PASSWORD=<secure_root_password>
```

### OpenSearch Configuration
```bash
export OPENSEARCH_PASSWORD=<secure_password>
```

### S3/MinIO Configuration
```bash
export S3_ACCESS_KEY=<your_access_key>
export S3_SECRET_KEY=<your_secret_key>
```

### Cloudflare Tunnel (if using)
```bash
export CLOUDFLARE_TUNNEL_TOKEN=<your_tunnel_token>
```

## Quick Setup on Server

### Option 1: Use Deployment Script (Recommended - Handles Special Characters)

1. Copy the example environment file:
   ```bash
   cp .env.example .env.production.local
   ```

2. Edit the file with your actual values:
   ```bash
   nano .env.production.local
   ```

3. Use the deployment script (handles WordPress salts with special characters):
   ```bash
   # Build images
   ./docker-compose.env.sh build
   
   # Start containers
   ./docker-compose.env.sh up
   
   # Or combine both
   ./docker-compose.env.sh build && ./docker-compose.env.sh up
   ```

### Option 2: Load environment manually (if deployment script not available)

```bash
# Use the safe environment loader
source ./load-env.sh

# Then build and start
sudo docker compose build
sudo docker compose up -d
```

### Option 3: Use docker-compose.yml directly (variables in compose file)

Docker Compose can read `.env.production.local` automatically if configured:
```bash
# Just make sure the file exists
cp .env.example .env.production.local
nano .env.production.local

# Docker Compose will read it automatically
sudo docker compose build
sudo docker compose up -d
```

> **Note:** The standard bash methods (`set -a && source` or `export $(...)`) will fail with WordPress salts because they contain special characters like `>`, `<`, `()` that bash interprets as commands. Use the provided scripts instead.

## After Deployment

1. **Check container status:**
   ```bash
   sudo docker compose ps
   ```

2. **View logs:**
   ```bash
   sudo docker compose logs -f wordpress
   ```

3. **Initialize WordPress (first time only):**
   ```bash
   sudo docker compose exec wordpress wp core install \
     --url="https://yourdomain.com" \
     --title="Arborisis" \
     --admin_user="admin" \
     --admin_email="admin@yourdomain.com"
   ```

4. **Create database indexes:**
   ```bash
   sudo docker compose exec wordpress wp arborisis init
   ```

## Troubleshooting

### If composer.lock is still missing on server

Pull the latest changes:
```bash
git pull origin main
```

Verify the file exists:
```bash
ls -la composer.lock
```

### If environment variables aren't loading

Check if docker-compose.yml references them correctly:
```bash
grep -n "OPENSEARCH_PASSWORD" docker-compose.yml
```

### Clear Docker cache and rebuild

```bash
sudo docker compose down
sudo docker system prune -f
sudo docker compose build --no-cache
sudo docker compose up -d
```

## Production Checklist

- [ ] Environment variables configured
- [ ] Database passwords are secure
- [ ] SSL certificates configured (via Cloudflare or Let's Encrypt)
- [ ] Backup strategy in place
- [ ] Monitoring configured
- [ ] Firewall rules updated
- [ ] Domain DNS pointed to server
- [ ] Test WordPress admin access
- [ ] Test file uploads
- [ ] Test search functionality
