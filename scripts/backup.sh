#!/bin/bash
# Backup script for Arborisis WordPress
# Creates backups of database, uploads, and configuration

set -e

BACKUP_DIR="${BACKUP_DIR:-/backups}"
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_NAME="arborisis_backup_${DATE}"

echo "Starting backup: ${BACKUP_NAME}"

# Create backup directory
mkdir -p "${BACKUP_DIR}/${BACKUP_NAME}"

# Backup database
echo "Backing up database..."
docker-compose exec -T mysql mysqldump \
  -u "${DB_USER}" \
  -p"${DB_PASSWORD}" \
  "${DB_NAME}" \
  > "${BACKUP_DIR}/${BACKUP_NAME}/database.sql"

# Backup uploads
echo "Backing up uploads..."
docker-compose exec -T wordpress tar czf - /var/www/html/wp-content/uploads \
  > "${BACKUP_DIR}/${BACKUP_NAME}/uploads.tar.gz"

# Backup configuration
echo "Backing up configuration..."
cp .env.production "${BACKUP_DIR}/${BACKUP_NAME}/.env.production"
cp docker-compose.yml "${BACKUP_DIR}/${BACKUP_NAME}/docker-compose.yml"

# Compress everything
echo "Compressing backup..."
cd "${BACKUP_DIR}"
tar czf "${BACKUP_NAME}.tar.gz" "${BACKUP_NAME}"
rm -rf "${BACKUP_NAME}"

# Cleanup old backups (keep last 7 days)
find "${BACKUP_DIR}" -name "arborisis_backup_*.tar.gz" -mtime +7 -delete

echo "Backup completed: ${BACKUP_DIR}/${BACKUP_NAME}.tar.gz"
