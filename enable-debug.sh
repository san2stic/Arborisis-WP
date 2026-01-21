#!/bin/bash
# Script to enable WordPress debugging
# Run this on the server to see detailed errors

echo "Enabling WordPress debug mode..."

# Backup current wp-config.php
docker exec arborisis-wordpress cp /var/www/html/wp-config.php /var/www/html/wp-config.php.backup

# Enable debug mode
docker exec arborisis-wordpress bash -c "cat >> /var/www/html/wp-config.php << 'EOF'

// Debug mode (temporary)
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', true);
@ini_set('display_errors', 1);
EOF"

echo "✅ Debug mode enabled"
echo "Try the upload again, then check logs with:"
echo "  docker exec arborisis-wordpress cat /var/www/html/wp-content/debug.log"
echo ""
echo "To disable debug mode, run: ./disable-debug.sh"
