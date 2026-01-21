#!/bin/bash
# Fix composer.lock to use PHP 8.2 instead of 8.4

set -e

echo "🔧 Fixing composer.lock for PHP 8.2"
echo ""

# Backup current lock file
if [ -f composer.lock ]; then
    echo "📦 Backing up current composer.lock..."
    cp composer.lock composer.lock.backup
fi

# Remove lock file
echo "🗑️  Removing composer.lock..."
rm -f composer.lock

# Regenerate lock file with Docker (uses PHP 8.2 from composer:2 image)
echo "🔨 Regenerating composer.lock with PHP 8.2..."
docker run --rm -v "$(pwd):/app" composer:2 composer update --no-scripts --no-interaction

echo ""
echo "✅ composer.lock regenerated successfully!"
echo ""
echo "Now commit and push:"
echo "  git add composer.lock"
echo "  git commit -m 'Fix composer.lock for PHP 8.2'"
echo "  git push"
