#!/bin/bash
# Safe environment variable loader for Docker Compose
# Handles special characters in .env files properly

ENV_FILE="${1:-.env.production.local}"

if [ ! -f "$ENV_FILE" ]; then
    echo "Error: Environment file '$ENV_FILE' not found"
    exit 1
fi

echo "Loading environment variables from: $ENV_FILE"

# Read the file line by line and export variables
# This method properly handles special characters in values
while IFS= read -r line || [ -n "$line" ]; do
    # Skip empty lines and comments
    if [[ -z "$line" || "$line" =~ ^[[:space:]]*# ]]; then
        continue
    fi
    
    # Export the variable (bash will handle quoting properly)
    export "$line"
done < "$ENV_FILE"

echo "✅ Environment variables loaded successfully"
echo ""
echo "You can now run:"
echo "  sudo docker compose build"
echo "  sudo docker compose up -d"
