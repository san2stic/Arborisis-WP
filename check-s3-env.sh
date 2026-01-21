#!/bin/bash
# Script to check S3 environment variables in the WordPress container

echo "🔍 Checking S3 environment variables in WordPress container..."
echo ""

if ! docker exec arborisis-wordpress env | grep -E "^S3_" ; then
    echo ""
    echo "❌ No S3 environment variables found!"
    echo ""
    echo "This is the problem - WordPress doesn't have access to S3 credentials."
    echo ""
    echo "Solution: Restart the WordPress container to load new environment variables:"
    echo "  docker compose restart wordpress"
else
    echo ""
    echo "✅ S3 environment variables are set"
fi

echo ""
echo "🔍 Checking if MinIO is accessible from WordPress..."
if docker exec arborisis-wordpress wget -q --spider http://minio:9000/minio/health/live 2>/dev/null; then
    echo "✅ MinIO is accessible from WordPress"
else
    echo "❌ MinIO is NOT accessible from WordPress"
fi

echo ""
echo "🔍 Checking MinIO bucket..."
docker exec arborisis-minio mc ls myminio/ 2>/dev/null || echo "⚠️  MinIO client not configured or bucket not created"
