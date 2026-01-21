#!/bin/bash
# Properly quote environment variables for Docker Compose
# Docker Compose parses .env files and interprets $VAR as variable substitution
# This script ensures all values are properly quoted

INPUT_FILE="${1:-.env.production.local}"
OUTPUT_FILE="${2:-.env}"

if [ ! -f "$INPUT_FILE" ]; then
    echo "Error: Input file '$INPUT_FILE' not found"
    exit 1
fi

echo "Converting $INPUT_FILE to Docker Compose compatible format..."

# Process the file line by line
while IFS= read -r line || [ -n "$line" ]; do
    # Skip empty lines and comments
    if [[ -z "$line" || "$line" =~ ^[[:space:]]*# ]]; then
        echo "$line" >> "$OUTPUT_FILE.tmp"
        continue
    fi
    
    # Split on first = sign
    if [[ "$line" =~ ^([^=]+)=(.*)$ ]]; then
        key="${BASH_REMATCH[1]}"
        value="${BASH_REMATCH[2]}"
        
        # Remove existing quotes if any
        value="${value#\"}"
        value="${value%\"}"
        value="${value#\'}"
        value="${value%\'}"
        
        # Escape double quotes in the value
        value="${value//\"/\\\"}"
        
        # Write with double quotes
        echo "${key}=\"${value}\"" >> "$OUTPUT_FILE.tmp"
    else
        # Invalid line, keep as is
        echo "$line" >> "$OUTPUT_FILE.tmp"
    fi
done < "$INPUT_FILE"

# Move temp file to output
mv "$OUTPUT_FILE.tmp" "$OUTPUT_FILE"

echo "✅ Created $OUTPUT_FILE with properly quoted values"
