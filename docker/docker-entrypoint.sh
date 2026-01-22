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

// Security keys - auto-generated or from environment
define('AUTH_KEY',         getenv('WP_AUTH_KEY') ?: bin2hex(random_bytes(32)));
define('SECURE_AUTH_KEY',  getenv('WP_SECURE_AUTH_KEY') ?: bin2hex(random_bytes(32)));
define('LOGGED_IN_KEY',    getenv('WP_LOGGED_IN_KEY') ?: bin2hex(random_bytes(32)));
define('NONCE_KEY',        getenv('WP_NONCE_KEY') ?: bin2hex(random_bytes(32)));
define('AUTH_SALT',        getenv('WP_AUTH_SALT') ?: bin2hex(random_bytes(32)));
define('SECURE_AUTH_SALT', getenv('WP_SECURE_AUTH_SALT') ?: bin2hex(random_bytes(32)));
define('LOGGED_IN_SALT',   getenv('WP_LOGGED_IN_SALT') ?: bin2hex(random_bytes(32)));
define('NONCE_SALT',       getenv('WP_NONCE_SALT') ?: bin2hex(random_bytes(32)));

// WordPress database table prefix
$table_prefix = 'wp_';

// Redis cache
define('WP_REDIS_HOST', getenv('REDIS_HOST'));
define('WP_REDIS_PORT', 6379);

// Debug mode
define('WP_DEBUG', getenv('WP_ENV') !== 'production');
define('WP_DEBUG_LOG', getenv('WP_ENV') !== 'production');
define('WP_DEBUG_DISPLAY', false);

// Handle SSL behind Reverse Proxy (Cloudflare)
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}

// Fix invalid host header issues
if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
    $_SERVER['HTTP_HOST'] = $_SERVER['HTTP_X_FORWARDED_HOST'];
}

// Define WordPress URL
if (isset($_SERVER['HTTP_HOST'])) {
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https://' : 'http://';
    define('WP_HOME', $protocol . $_SERVER['HTTP_HOST']);
    define('WP_SITEURL', $protocol . $_SERVER['HTTP_HOST']);
}

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

# Run initialization in background (waits for MySQL then configures WP)
nohup /usr/local/bin/init-wordpress > /var/log/nginx/init-wordpress.log 2>&1 &

# Execute the original command
exec "$@"
