#!/bin/bash
# Rebuild Docker image and redeploy
# Use this after code changes that require rebuilding

set -e

echo "🔨 Arborisis - Rebuild and Deploy"
echo ""

# Stop WordPress container
echo "⏸️  Stopping WordPress container..."
sudo docker compose stop wordpress

# Rebuild WordPress image
echo ""
echo "🔨 Rebuilding WordPress image..."
echo "This may take several minutes..."
sudo docker compose build wordpress

# Start WordPress container
echo ""
echo "🚀 Starting WordPress container..."
sudo docker compose up -d wordpress

# Wait for WordPress to be ready
echo ""
echo "⏳ Waiting for WordPress to be ready (45s)..."
sleep 45

# Check health
echo ""
echo "🏥 Checking container health..."
sudo docker compose ps wordpress

# Show logs
echo ""
echo "📋 Recent WordPress logs:"
sudo docker compose logs wordpress | tail -20

echo ""
echo "✅ Rebuild completed!"
echo ""
echo "🧪 Test the upload at: https://arborisis.social/upload"
echo ""
echo "💡 If you still have issues:"
echo "   - Check logs: sudo docker compose logs wordpress | tail -50"
echo "   - Verify S3: ./check-s3-env.sh"
