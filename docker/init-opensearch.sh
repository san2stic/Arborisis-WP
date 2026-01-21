#!/bin/bash
set -e

echo "Waiting for OpenSearch to be ready..."
until curl -k -u "admin:${OPENSEARCH_PASSWORD}" https://localhost:9200/_cluster/health &>/dev/null; do
    echo "OpenSearch is unavailable - sleeping"
    sleep 5
done

echo "OpenSearch is up - checking if index exists"

# Check if index already exists
INDEX_EXISTS=$(curl -k -s -o /dev/null -w "%{http_code}" -u "admin:${OPENSEARCH_PASSWORD}" https://localhost:9200/arborisis_sounds)

if [ "$INDEX_EXISTS" == "404" ]; then
    echo "Creating arborisis_sounds index with mapping..."
    curl -k -XPUT "https://localhost:9200/arborisis_sounds" \
        -u "admin:${OPENSEARCH_PASSWORD}" \
        -H 'Content-Type: application/json' \
        -d @/usr/share/opensearch/mapping.json
    
    echo "Index created successfully!"
else
    echo "Index already exists, skipping creation"
fi

echo "OpenSearch initialization complete"
