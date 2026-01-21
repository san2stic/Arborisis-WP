# Arborisis - Field Recording Platform

Complete WordPress implementation of the Arborisis field recording platform with full-featured audio management, geolocation, search, and analytics.

## Features

- **Sound Management**: Custom post type with full CRUD via REST API
- **S3 Direct Upload**: Presigned URLs for client-side uploads to S3/MinIO
- **OpenSearch Integration**: Full-text search with geolocation and scoring
- **Interactive Map**: Clustering-based map visualization with bbox queries
- **Graph Explore**: Network graph of related sounds based on tags, location, and popularity
- **Statistics & Analytics**: Plays tracking, likes, trending scores, leaderboards
- **User Profiles**: Public profiles with bio, social links, and sound collections
- **Redis Caching**: High-performance caching for maps, search, and stats

## Architecture

### Plugins
- **arborisis-core**: CPT, taxonomies, roles, REST API base
- **arborisis-audio**: S3 upload, metadata extraction
- **arborisis-search**: OpenSearch integration with fallback
- **arborisis-geo**: Map clustering, geospatial indexing
- **arborisis-stats**: Plays, likes, comments, leaderboards
- **arborisis-graph**: Graph explore algorithm

### Theme
- **arborisis**: Custom theme with Vite build system (Tailwind CSS)

## Requirements

- PHP 8.2+
- WordPress 6.0+
- MySQL/MariaDB 8.0+
- Redis 6.0+
- OpenSearch 2.0+
- S3-compatible storage (AWS S3, MinIO, Infomaniak Object Storage)
- Composer
- Node.js 18+ (for theme build)

## Installation

### 1. Clone and Install Dependencies

```bash
# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env

# Edit .env with your configuration
nano .env
```

### 2. Configure WordPress

The `wp-config.php` is already configured to use environment variables. Generate WordPress salts:

```bash
curl https://api.wordpress.org/secret-key/1.1/salt/
```

Add them to your `.env` file.

### 3. Install WordPress

```bash
# Standard WordPress installation
wp core install \
  --url="https://your-domain.com" \
  --title="Arborisis" \
  --admin_user="admin" \
  --admin_password="secure_password" \
  --admin_email="admin@example.com"
```

### 4. Activate Plugins

```bash
wp plugin activate arborisis-core
wp plugin activate arborisis-audio
wp plugin activate arborisis-search
wp plugin activate arborisis-geo
wp plugin activate arborisis-stats
wp plugin activate arborisis-graph
```

### 5. Create Database Tables

Tables are created automatically on plugin activation, but you can verify:

```bash
wp db query "SHOW TABLES LIKE 'wp_arb_%';"
```

### 6. Create OpenSearch Index

```bash
# Create index with proper mapping
curl -X PUT "https://opensearch-host:9200/arborisis_sounds" \
  -u admin:password \
  -H 'Content-Type: application/json' \
  -d @opensearch-mapping.json
```

### 7. Initial Reindex

```bash
wp arborisis reindex
```

### 8. Build Theme

```bash
cd wp-content/themes/arborisis
npm install
npm run build
```

Activate the theme:

```bash
wp theme activate arborisis
```

## Configuration

### Environment Variables

All configuration is done via environment variables. See `.env.example` for complete reference.

#### Critical Settings

- **WP_ENV**: Set to `production` on live server
- **DISABLE_WP_CRON**: Set to `true` in production (use system cron)
- **S3_ENDPOINT**: Your S3-compatible storage endpoint
- **OPENSEARCH_HOST**: OpenSearch server address
- **REDIS_HOST**: Redis server address

### Redis Object Cache

Install Redis object cache drop-in:

```bash
wp redis enable
```

### Cron Jobs (Production)

Disable WP-Cron and use system cron:

```cron
# Every 5 minutes: Process OpenSearch queue (if async)
*/5 * * * * cd /path/to/wp && wp arborisis process-opensearch-queue --allow-root

# Every hour: Aggregate plays
0 * * * * cd /path/to/wp && wp arborisis aggregate-plays --allow-root

# Every hour: Compute trending scores
15 * * * * cd /path/to/wp && wp arborisis compute-trending --allow-root

# Every 6 hours: Warm cache
0 */6 * * * cd /path/to/wp && wp arborisis warm-cache --allow-root

# Daily 3am: Cleanup old plays events
0 3 * * * cd /path/to/wp && wp arborisis cleanup-plays --allow-root
```

## API Endpoints

### Sounds

```
GET    /wp-json/arborisis/v1/sounds
GET    /wp-json/arborisis/v1/sounds/{id}
PUT    /wp-json/arborisis/v1/sounds/{id}
DELETE /wp-json/arborisis/v1/sounds/{id}
```

### Upload

```
POST /wp-json/arborisis/v1/upload/presign
POST /wp-json/arborisis/v1/upload/finalize
```

### Interactions

```
POST /wp-json/arborisis/v1/sounds/{id}/play
POST /wp-json/arborisis/v1/sounds/{id}/like
GET  /wp-json/arborisis/v1/sounds/{id}/comments
POST /wp-json/arborisis/v1/sounds/{id}/comments
```

### Map

```
GET /wp-json/arborisis/v1/map/sounds?bbox=lat1,lon1,lat2,lon2&zoom=10
```

### Search

```
GET /wp-json/arborisis/v1/search?q=nature&tags=birds&lat=48.8&lon=2.3&radius=50
```

### Graph

```
GET /wp-json/arborisis/v1/graph/explore?seed_id=123&depth=2&max_nodes=50
```

### Stats

```
GET /wp-json/arborisis/v1/stats/global
GET /wp-json/arborisis/v1/stats/user/{id}
GET /wp-json/arborisis/v1/stats/leaderboards?type=sounds&period=7d
```

### Users

```
GET /wp-json/arborisis/v1/users/{username}
PUT /wp-json/arborisis/v1/users/me
```

## Deployment (Jelastic Infomaniak)

### 1. Create Environment

Create a Jelastic environment with:
- 1× Nginx + PHP 8.2 node (8 cloudlets minimum)
- 1× MariaDB node
- 1× Redis node
- 1× OpenSearch node (8 cloudlets minimum)

### 2. Configure Environment Variables

In Jelastic dashboard, add all variables from `.env.example` to your WordPress node.

### 3. Set Up Persistent Volume

Mount persistent volume for uploads:
```
/var/www/webroot/ROOT/wp-content/uploads
```

### 4. Deploy Code

```bash
# SSH into WordPress node
git clone <your-repo> /tmp/arborisis
cp -r /tmp/arborisis/* /var/www/webroot/ROOT/
cd /var/www/webroot/ROOT
composer install --no-dev --optimize-autoloader
```

### 5. Configure PHP

Edit `/etc/php.ini`:
```ini
memory_limit = 256M
upload_max_filesize = 200M
post_max_size = 200M
max_execution_time = 300
opcache.enable = 1
opcache.memory_consumption = 128
```

### 6. Set Up Cron

Use Jelastic cron scheduler (dashboard → Cron) with commands from "Cron Jobs" section above.

### 7. SSL Certificate

Use Jelastic Let's Encrypt add-on for automatic SSL.

## Development

### Local Development Setup

```bash
# Install dependencies
composer install
cd wp-content/themes/arborisis && npm install

# Start local services with Docker
docker-compose up -d

# Build theme in watch mode
npm run dev
```

### Running Tests

```bash
# PHP tests
composer test

# JavaScript tests
cd wp-content/themes/arborisis
npm test
```

## Performance

### Caching Strategy

- **Redis**: Object cache + endpoint caching (map, search, graph, stats)
- **TTLs**:
  - Map: 5 minutes
  - Search: 2 minutes
  - Graph: 10 minutes
  - Stats: 1 hour
  - Sound detail: 1 hour

### Database Indexes

All custom tables include optimized indexes. Run this to verify:

```bash
wp db query "SHOW INDEX FROM wp_arb_geo_index;"
wp db query "SHOW INDEX FROM wp_arb_plays;"
wp db query "SHOW INDEX FROM wp_arb_likes;"
```

### OpenSearch Performance

- Index shards: 2
- Replicas: 1 (production)
- Refresh interval: 1s (can increase to 30s for write-heavy workloads)

## Monitoring

### Health Check Endpoint

```
GET /wp-json/arborisis/v1/health
```

Returns status of DB, Redis, OpenSearch.

### Logs

- PHP errors: `/var/log/php/error.log`
- Nginx: `/var/log/nginx/error.log`
- WordPress debug: `wp-content/debug.log` (if `WP_DEBUG_LOG=true`)

## Security

- File editing disabled in admin (`DISALLOW_FILE_EDIT`)
- SSL enforced in production (`FORCE_SSL_ADMIN`)
- Rate limiting on uploads (configurable)
- Anti-spam plays tracking (fingerprinting)
- Presigned URLs expire after 15 minutes

## Troubleshooting

### OpenSearch Connection Issues

```bash
# Test connection
curl -u admin:password https://opensearch-host:9200/_cluster/health

# Check index
curl -u admin:password https://opensearch-host:9200/arborisis_sounds/_count
```

### Redis Connection Issues

```bash
# Test connection
redis-cli -h redis-host -p 6379 -a password PING

# Check keys
redis-cli -h redis-host -p 6379 -a password --scan --pattern 'arb:*'
```

### S3 Upload Issues

```bash
# Test S3 connection
wp eval 'var_dump(ARB_S3_Client::get()->listBuckets());'
```

### Reindex Everything

```bash
# OpenSearch
wp arborisis reindex

# Geo index
wp arborisis reindex-geo

# Stats aggregation
wp arborisis aggregate-plays --all
```

## License

GPL-2.0-or-later

## Support

For issues, please open a GitHub issue or contact the development team.
