#!/bin/bash
set -e

echo "Starting WordPress initialization..."

# Wait for MySQL
echo "Waiting for MySQL to be ready..."
while ! mysqladmin ping -h"$DB_HOST" --silent; do
    sleep 2
done

echo "MySQL is ready!"

# Check if WordPress is installed
if ! wp core is-installed --allow-root 2>/dev/null; then
    echo "WordPress not installed, running installation..."
    
    wp core install \
        --url="${WP_HOME}" \
        --title="Arborisis - Field Recording Platform" \
        --admin_user="${WP_ADMIN_USER:-admin}" \
        --admin_password="${WP_ADMIN_PASSWORD:-changeme}" \
        --admin_email="${WP_ADMIN_EMAIL:-admin@example.com}" \
        --skip-email \
        --allow-root
    
    echo "WordPress installed successfully!"
else
    echo "WordPress already installed, skipping installation"
fi

# Activate plugins
echo "Activating Arborisis plugins..."
wp plugin activate arborisis-core --allow-root || echo "Plugin arborisis-core already activated or not found"
wp plugin activate arborisis-audio --allow-root || echo "Plugin arborisis-audio already activated or not found"
wp plugin activate arborisis-search --allow-root || echo "Plugin arborisis-search already activated or not found"
wp plugin activate arborisis-geo --allow-root || echo "Plugin arborisis-geo already activated or not found"
wp plugin activate arborisis-stats --allow-root || echo "Plugin arborisis-stats already activated or not found"
wp plugin activate arborisis-graph --allow-root || echo "Plugin arborisis-graph already activated or not found"

# Activate theme
echo "Activating Arborisis theme..."
wp theme activate arborisis --allow-root || echo "Theme arborisis already activated or not found"

# Enable Redis Object Cache
echo "Enabling Redis object cache..."
wp redis enable --allow-root 2>/dev/null || echo "Redis already enabled or plugin not installed"

# Flush rewrite rules
echo "Flushing rewrite rules..."
wp rewrite flush --allow-root

# Update permalinks to pretty URLs
echo "Setting permalink structure..."
wp rewrite structure '/%postname%/' --allow-root

echo "WordPress initialization complete!"

# Create cron jobs info file
cat > /var/www/html/CRON-INFO.txt << 'EOF'
# Arborisis Cron Jobs
# Add these to your host system cron (crontab -e):

# Every 5 minutes: Process OpenSearch queue
*/5 * * * * docker exec arborisis-wordpress wp arborisis process-opensearch-queue --allow-root

# Every hour: Aggregate plays
0 * * * * docker exec arborisis-wordpress wp arborisis aggregate-plays --allow-root

# Every hour: Compute trending scores
15 * * * * docker exec arborisis-wordpress wp arborisis compute-trending --allow-root

# Every 6 hours: Warm cache
0 */6 * * * docker exec arborisis-wordpress wp arborisis warm-cache --allow-root

# Daily 3am: Cleanup old plays events
0 3 * * * docker exec arborisis-wordpress wp arborisis cleanup-plays --allow-root
EOF

echo "Cron jobs info saved to /var/www/html/CRON-INFO.txt"
