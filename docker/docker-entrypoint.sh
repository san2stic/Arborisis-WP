#!/bin/bash
set -e

# Create wp-config.php if it doesn't exist
if [ ! -f /var/www/html/wp-config.php ]; then
    echo "Creating wp-config.php from environment variables..."
    
    cat > /var/www/html/wp-config.php <<'EOF'
<?php
/**
 * WordPress Configuration - Auto-generated
 */

// Database settings
define('DB_NAME', getenv('DB_NAME'));
define('DB_USER', getenv('DB_USER'));
define('DB_PASSWORD', getenv('DB_PASSWORD'));
define('DB_HOST', getenv('DB_HOST'));
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', '');

// Security keys - generate new ones at https://api.wordpress.org/secret-key/1.1/salt/
define('AUTH_KEY',         'put your unique phrase here');
define('SECURE_AUTH_KEY',  'put your unique phrase here');
define('LOGGED_IN_KEY',    'put your unique phrase here');
define('NONCE_KEY',        'put your unique phrase here');
define('AUTH_SALT',        'put your unique phrase here');
define('SECURE_AUTH_SALT', 'put your unique phrase here');
define('LOGGED_IN_SALT',   'put your unique phrase here');
define('NONCE_SALT',       'put your unique phrase here');

// WordPress database table prefix
$table_prefix = 'wp_';

// Redis cache
define('WP_REDIS_HOST', getenv('REDIS_HOST'));
define('WP_REDIS_PORT', 6379);

// Debug mode
define('WP_DEBUG', getenv('WP_ENV') !== 'production');
define('WP_DEBUG_LOG', getenv('WP_ENV') !== 'production');
define('WP_DEBUG_DISPLAY', false);

// WordPress URLs (will be set from first request)
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

require_once ABSPATH . 'wp-settings.php';
EOF

    chown www-data:www-data /var/www/html/wp-config.php
    chmod 640 /var/www/html/wp-config.php
    echo "wp-config.php created successfully"
fi

# Execute the original command
exec "$@"
