#!/bin/bash
# Script to manually create the MinIO bucket

set -e

echo "📦 Creating MinIO bucket manually..."
echo ""

# Configure MinIO client
echo "🔧 Configuring MinIO client..."
sudo docker exec arborisis-minio mc alias set myminio http://localhost:9000 'Zabou007**Jule' 'Zabou007**Jule'

# Create bucket
echo "📦 Creating bucket 'arborisis-audio'..."
sudo docker exec arborisis-minio mc mb myminio/arborisis-audio || echo "Bucket may already exist"

# Set public read policy
echo "🔓 Setting public download policy..."
sudo docker exec arborisis-minio mc anonymous set download myminio/arborisis-audio

# Verify
echo ""
echo "✅ Verification:"
sudo docker exec arborisis-minio mc ls myminio/

echo ""
echo "🎉 Bucket created successfully!"
echo ""
echo "Now try uploading a file on: https://arborisis.social/upload"
