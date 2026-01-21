#!/bin/bash
# Complete update and fix script for production
# This script pulls latest changes and fixes everything

set -e

echo "🔄 Arborisis - Update and Fix Script"
echo ""

# Pull latest changes
echo "📥 Pulling latest changes from git..."
git pull

echo ""
echo "📝 Updating .env file with correct format..."
# Backup current .env
cp .env .env.backup.$(date +%Y%m%d_%H%M%S)

# Copy new .env with quoted passwords
cp .env.production.local .env

echo ""
echo "🔄 Restarting services to apply changes..."
sudo docker compose restart wordpress minio

echo ""
echo "⏳ Waiting for services to be ready (30s)..."
sleep 30

echo ""
echo "📦 Creating MinIO bucket..."
# Configure MinIO client with proper quoting
sudo docker exec arborisis-minio mc alias set myminio http://localhost:9000 'Zabou007**Jule' 'Zabou007**Jule' || true

# Create bucket
sudo docker exec arborisis-minio mc mb myminio/arborisis-audio 2>/dev/null || echo "  ℹ️  Bucket may already exist"

# Set permissions
sudo docker exec arborisis-minio mc anonymous set download myminio/arborisis-audio

echo ""
echo "✅ Verification:"
echo ""

# Check S3 vars
echo "S3 Variables in WordPress:"
sudo docker exec arborisis-wordpress env | grep "^S3_" | head -5

echo ""
echo "MinIO Buckets:"
sudo docker exec arborisis-minio mc ls myminio/ 2>/dev/null || echo "  ⚠️  Could not list buckets"

echo ""
echo "MinIO Health:"
curl -sf http://localhost:9000/minio/health/live > /dev/null && echo "  ✅ MinIO is healthy" || echo "  ❌ MinIO not accessible"

echo ""
echo "🎉 Update completed!"
echo ""
echo "✨ You can now try uploading a file at: https://arborisis.social/upload"
echo ""
echo "If you still have issues, check logs with:"
echo "  sudo docker compose logs wordpress | tail -50"
