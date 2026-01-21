#!/bin/bash
# Restore script for Arborisis WordPress
# Restores database, uploads, and configuration from backup

set -e

if [ -z "$1" ]; then
  echo "Usage: ./restore.sh <backup_file.tar.gz>"
  exit 1
fi

BACKUP_FILE="$1"
TEMP_DIR="/tmp/arborisis_restore_$$"

echo "Starting restore from: ${BACKUP_FILE}"

# Extract backup
echo "Extracting backup..."
mkdir -p "${TEMP_DIR}"
tar xzf "${BACKUP_FILE}" -C "${TEMP_DIR}"

# Find backup directory
BACKUP_DIR=$(find "${TEMP_DIR}" -maxdepth 1 -type d -name "arborisis_backup_*" | head -n 1)

if [ -z "${BACKUP_DIR}" ]; then
  echo "Error: Invalid backup file"
  rm -rf "${TEMP_DIR}"
  exit 1
fi

# Stop services
echo "Stopping services..."
docker-compose down

# Restore database
echo "Restoring database..."
docker-compose up -d mysql
sleep 10  # Wait for MySQL to start

docker-compose exec -T mysql mysql \
  -u "${DB_USER}" \
  -p"${DB_PASSWORD}" \
  "${DB_NAME}" \
  < "${BACKUP_DIR}/database.sql"

# Restore uploads
echo "Restoring uploads..."
docker-compose up -d wordpress
sleep 5

docker-compose exec -T wordpress tar xzf - -C / \
  < "${BACKUP_DIR}/uploads.tar.gz"

# Restore configuration (optional, ask user)
if [ -f "${BACKUP_DIR}/.env.production" ]; then
  echo "Configuration backup found. Restore? (y/n)"
  read -r answer
  if [ "$answer" = "y" ]; then
    cp "${BACKUP_DIR}/.env.production" .env.production
    echo "Configuration restored"
  fi
fi

# Cleanup
rm -rf "${TEMP_DIR}"

# Start all services
echo "Starting all services..."
docker-compose up -d

echo "Restore completed successfully!"
echo "Please verify your installation at http://localhost"
