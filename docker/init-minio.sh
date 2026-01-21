#!/bin/sh
# MinIO Initialization Script
# This script creates the bucket and sets permissions on first start

set -e

echo "🗄️  Waiting for MinIO to be ready..."
sleep 5

# Configure MinIO client (use quotes for passwords with special characters)
mc alias set myminio http://minio:9000 "${S3_ACCESS_KEY}" "${S3_SECRET_KEY}"

# Create bucket if it doesn't exist
if ! mc ls myminio/${S3_BUCKET} > /dev/null 2>&1; then
    echo "📦 Creating bucket: ${S3_BUCKET}"
    mc mb myminio/${S3_BUCKET}
    echo "✅ Bucket created successfully"
else
    echo "✅ Bucket ${S3_BUCKET} already exists"
fi

# Set public read policy for the bucket
echo "🔓 Setting public read policy..."
mc anonymous set download myminio/${S3_BUCKET}

# Configure CORS for browser uploads
echo "🌐 Configuring CORS..."
cat > /tmp/cors.xml << 'EOF'
<CORSConfiguration>
  <CORSRule>
    <AllowedOrigin>*</AllowedOrigin>
    <AllowedMethod>GET</AllowedMethod>
    <AllowedMethod>HEAD</AllowedMethod>
    <AllowedMethod>PUT</AllowedMethod>
    <AllowedMethod>POST</AllowedMethod>
    <AllowedHeader>*</AllowedHeader>
  </CORSRule>
</CORSConfiguration>
EOF
mc cors set myminio/${S3_BUCKET} /tmp/cors.xml

echo "✅ MinIO initialization complete"
