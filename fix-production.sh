#!/bin/bash
# Quick fix script for production server
# This script ensures all environment variables are loaded and services are running

set -e

echo "🚀 Arborisis Production Fix Script"
echo ""

# Check if .env file exists
if [ ! -f ".env" ]; then
    echo "📝 Creating .env from .env.production.local..."
    cp .env.production.local .env
fi

# Check if docker-compose is running
if ! docker compose ps | grep -q "arborisis-wordpress"; then
    echo "⚠️  WordPress container not running. Starting all services..."
    docker compose up -d
    echo "⏳ Waiting for services to start (60s)..."
    sleep 60
else
    echo "✅ WordPress container is running"
fi

# Check S3 environment variables
echo ""
echo "🔍 Checking S3 environment variables..."
if ! docker exec arborisis-wordpress env | grep -q "S3_BUCKET"; then
    echo "❌ S3 environment variables NOT found in WordPress container"
    echo "🔄 Restarting WordPress with new environment..."
    docker compose restart wordpress
    echo "⏳ Waiting for WordPress to restart (30s)..."
    sleep 30
else
    echo "✅ S3 environment variables are set"
fi

# Check MinIO
echo ""
echo "🔍 Checking MinIO status..."
if docker compose ps | grep -q "arborisis-minio.*Up"; then
    echo "✅ MinIO is running"

    # Check if bucket exists
    echo "🔍 Checking bucket..."
    if docker exec arborisis-minio mc ls myminio/arborisis-audio > /dev/null 2>&1; then
        echo "✅ Bucket 'arborisis-audio' exists"
    else
        echo "⚠️  Bucket not found. Running minio-init..."
        docker compose up -d minio-init
        sleep 10
    fi
else
    echo "❌ MinIO is not running"
    docker compose up -d minio
    echo "⏳ Waiting for MinIO to start (20s)..."
    sleep 20
fi

# Final verification
echo ""
echo "🧪 Final verification..."
echo ""

# Check S3 vars again
echo "S3 Environment Variables:"
docker exec arborisis-wordpress env | grep "^S3_" || echo "  ❌ Still missing!"

echo ""
echo "MinIO Health:"
if curl -sf http://localhost:9000/minio/health/live > /dev/null 2>&1; then
    echo "  ✅ MinIO is accessible on localhost:9000"
else
    echo "  ❌ MinIO not accessible on localhost:9000"
fi

echo ""
echo "MinIO Buckets:"
docker exec arborisis-minio mc ls myminio/ 2>/dev/null | grep arborisis-audio || echo "  ❌ Bucket not found"

echo ""
echo "📊 Container Status:"
docker compose ps

echo ""
echo "✅ Fix script completed!"
echo ""
echo "Next steps:"
echo "1. Try uploading a file again"
echo "2. If still failing, check WordPress logs:"
echo "   docker compose logs wordpress | tail -50"
echo "3. Check detailed error with:"
echo "   ./check-s3-env.sh"
