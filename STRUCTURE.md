# Structure du projet Arborisis WordPress

## 📁 Architecture complète

```
arborisis-wordpress/
├── wp-config.php                      # Configuration WordPress avec env vars
├── composer.json                      # Dépendances PHP
├── .env.example                       # Template variables d'environnement
├── .gitignore                         # Fichiers à ignorer
├── opensearch-mapping.json            # Mapping OpenSearch
├── README.md                          # Documentation complète
├── INSTALLATION.md                    # Guide d'installation rapide
├── STRUCTURE.md                       # Ce fichier
│
└── wp-content/
    │
    ├── plugins/
    │   │
    │   ├── arborisis-core/           # 🔷 Plugin principal
    │   │   ├── arborisis-core.php
    │   │   └── includes/
    │   │       ├── class-sound-cpt.php          # CPT Sound + taxonomies
    │   │       ├── class-roles.php              # Rôles custom
    │   │       ├── class-rest-sounds.php        # API REST sons
    │   │       ├── class-rest-users.php         # API REST users
    │   │       └── helpers.php                  # Fonctions utilitaires
    │   │
    │   ├── arborisis-audio/          # 🎵 Upload S3
    │   │   ├── arborisis-audio.php
    │   │   └── includes/
    │   │       ├── class-s3-client.php          # Client S3
    │   │       ├── class-rest-upload.php        # API presign/finalize
    │   │       ├── class-metadata-extractor.php # Extraction ffprobe
    │   │       └── class-cli.php                # WP-CLI metadata
    │   │
    │   ├── arborisis-search/         # 🔍 OpenSearch
    │   │   ├── arborisis-search.php
    │   │   └── includes/
    │   │       ├── class-opensearch-client.php  # Client OpenSearch
    │   │       ├── class-indexer.php            # Indexation
    │   │       ├── class-rest-search.php        # API search + fallback
    │   │       └── class-cli.php                # WP-CLI reindex
    │   │
    │   ├── arborisis-geo/            # 🗺️ Map + clustering
    │   │   ├── arborisis-geo.php
    │   │   └── includes/
    │   │       ├── class-geo-indexer.php        # Index géospatial
    │   │       ├── class-clustering.php         # Algorithme clustering
    │   │       ├── class-rest-map.php           # API map bbox
    │   │       └── class-cli.php                # WP-CLI geo
    │   │
    │   ├── arborisis-stats/          # 📊 Stats + plays + likes
    │   │   ├── arborisis-stats.php
    │   │   └── includes/
    │   │       ├── class-plays-tracker.php      # Tracking plays
    │   │       ├── class-likes-manager.php      # Gestion likes
    │   │       ├── class-aggregator.php         # Agrégation stats
    │   │       ├── class-rest-stats.php         # API stats
    │   │       └── class-cli.php                # WP-CLI aggregation
    │   │
    │   └── arborisis-graph/          # 🕸️ Graph explore
    │       ├── arborisis-graph.php
    │       └── includes/
    │           ├── class-graph-builder.php      # Algorithme graph
    │           └── class-rest-graph.php         # API graph
    │
    └── themes/
        └── arborisis/                # 🎨 Thème (à créer)
            ├── functions.php
            ├── front-page.php
            ├── single-sound.php
            ├── page-map.php
            ├── page-graph.php
            ├── package.json           # Vite + Tailwind
            ├── vite.config.js
            └── src/
                ├── main.js
                ├── map.js
                ├── graph.js
                └── components/
```

## 📦 Plugins créés (6)

### 1. arborisis-core
**Rôle** : Base du système
- ✅ CPT `sound` avec support title, editor, author, thumbnail, comments
- ✅ Taxonomies : `sound_tag` (tags), `sound_license` (licences)
- ✅ Rôles : `uploader`, `moderator` + capabilities
- ✅ REST API : `/sounds` (list/detail/update/delete)
- ✅ REST API : `/users/{username}`, `/users/me`
- ✅ Helpers : Redis, cache, geohash, distance

**Fichiers** : 5 classes PHP

### 2. arborisis-audio
**Rôle** : Upload direct S3
- ✅ Client S3 avec AWS SDK
- ✅ Endpoint `/upload/presign` → URL pré-signée
- ✅ Endpoint `/upload/finalize` → création post sound
- ✅ Extraction metadata (ffprobe) via WP-CLI
- ✅ Validation MIME + rate limiting
- ✅ Sécurité : anti-spam, timeouts

**Fichiers** : 4 classes PHP

### 3. arborisis-search
**Rôle** : Recherche full-text
- ✅ Client OpenSearch (opensearch-php)
- ✅ Indexation automatique (hooks save_post)
- ✅ Mapping complet (text, keyword, geo_point, scoring)
- ✅ Endpoint `/search` avec fallback WordPress
- ✅ Queries : fulltext, tags, geo distance, trending
- ✅ WP-CLI : reindex, process queue

**Fichiers** : 4 classes PHP

### 4. arborisis-geo
**Rôle** : Carte interactive
- ✅ Table `arb_geo_index` (lat, lon, geohash)
- ✅ Clustering serveur basé sur geohash
- ✅ Endpoint `/map/sounds` avec bbox + zoom
- ✅ Adaptation précision geohash selon zoom
- ✅ Cache Redis 5min
- ✅ WP-CLI : reindex-geo

**Fichiers** : 4 classes PHP

### 5. arborisis-stats
**Rôle** : Analytics complètes
- ✅ Tables : `arb_likes`, `arb_plays`, `arb_plays_daily`
- ✅ Tracking plays (anti-spam fingerprinting)
- ✅ Gestion likes (toggle like/unlike)
- ✅ Agrégation daily plays
- ✅ Calcul trending scores
- ✅ Endpoints : `/sounds/{id}/play`, `/sounds/{id}/like`
- ✅ Endpoints : `/stats/global`, `/stats/user/{id}`, `/stats/leaderboards`
- ✅ WP-CLI : aggregate-plays, compute-trending, cleanup-plays, warm-cache

**Fichiers** : 5 classes PHP

### 6. arborisis-graph
**Rôle** : Exploration graphe
- ✅ Algorithme expansion par voisinage
- ✅ Similarité : tags (Jaccard) + geo (Haversine) + popularité
- ✅ Endpoint `/graph/explore` avec depth + max_nodes
- ✅ Cache Redis 10min
- ✅ Format nodes/edges pour D3.js
- ✅ Invalidation automatique

**Fichiers** : 2 classes PHP

## 📊 Base de données

### Tables custom (5)

```sql
-- Geo index
wp_arb_geo_index (sound_id, latitude, longitude, geohash)
  Indexes: PRIMARY, idx_geohash, idx_lat, idx_lon

-- Likes
wp_arb_likes (id, user_id, sound_id, created_at)
  Indexes: idx_user, idx_sound, unique_like

-- Plays events
wp_arb_plays (id, sound_id, user_id, ip_hash, user_agent_hash, created_at)
  Indexes: idx_sound, idx_created, idx_fingerprint

-- Plays aggregation
wp_arb_plays_daily (sound_id, day, plays_count)
  Indexes: PRIMARY (sound_id, day), idx_day

-- OpenSearch queue (optionnel)
wp_arb_opensearch_queue (id, sound_id, action, created_at, processed_at)
  Indexes: idx_pending
```

### Post meta (`sound`)

| Meta key               | Type   | Description                    |
|------------------------|--------|--------------------------------|
| `_arb_audio_url`       | string | URL publique S3                |
| `_arb_audio_key`       | string | Clé S3                         |
| `_arb_duration`        | float  | Durée en secondes              |
| `_arb_format`          | string | Format (mp3, wav, flac, ogg)   |
| `_arb_filesize`        | int    | Taille en bytes                |
| `_arb_latitude`        | float  | Latitude                       |
| `_arb_longitude`       | float  | Longitude                      |
| `_arb_location_name`   | string | Nom du lieu                    |
| `_arb_recorded_at`     | string | Date d'enregistrement          |
| `_arb_equipment`       | string | Matériel utilisé               |
| `_arb_waveform_data`   | json   | Peaks waveform                 |
| `_arb_plays_count`     | int    | Cache total plays              |
| `_arb_likes_count`     | int    | Cache total likes              |
| `_arb_trending_score`  | float  | Score tendance                 |

### User meta

| Meta key           | Type   | Description        |
|--------------------|--------|--------------------|
| `_arb_bio`         | text   | Bio publique       |
| `_arb_website`     | string | URL site           |
| `_arb_twitter`     | string | Handle Twitter     |
| `_arb_instagram`   | string | Handle Instagram   |
| `_arb_total_plays` | int    | Cache plays user   |
| `_arb_total_likes` | int    | Cache likes user   |

## 🔌 API REST complète

### Base URL
`/wp-json/arborisis/v1/`

### Endpoints (15)

| Endpoint                        | Méthode | Auth      | Description                      |
|---------------------------------|---------|-----------|----------------------------------|
| `/sounds`                       | GET     | public    | Liste sons (filtres)             |
| `/sounds`                       | POST    | uploader+ | Créer son (via finalize)         |
| `/sounds/{id}`                  | GET     | public    | Détail son                       |
| `/sounds/{id}`                  | PUT     | owner/mod | Modifier son                     |
| `/sounds/{id}`                  | DELETE  | owner/mod | Supprimer son                    |
| `/upload/presign`               | POST    | uploader+ | URL pré-signée S3                |
| `/upload/finalize`              | POST    | uploader+ | Finaliser upload                 |
| `/sounds/{id}/play`             | POST    | public    | Tracker play                     |
| `/sounds/{id}/like`             | POST    | user+     | Toggle like                      |
| `/sounds/{id}/stats`            | GET     | public    | Stats son                        |
| `/map/sounds`                   | GET     | public    | Sons + clusters bbox             |
| `/search`                       | GET     | public    | Search fulltext + geo            |
| `/graph/explore`                | GET     | public    | Graph explore                    |
| `/stats/global`                 | GET     | public    | Stats globales                   |
| `/stats/user/{id}`              | GET     | public    | Stats utilisateur                |
| `/stats/leaderboards`           | GET     | public    | Top sons/users                   |
| `/users/{username}`             | GET     | public    | Profil public                    |
| `/users/me`                     | PUT     | user+     | Modifier profil                  |

## 🛠️ WP-CLI Commands

```bash
# OpenSearch
wp arborisis reindex [--batch-size=100]
wp arborisis process-opensearch-queue [--limit=100]

# Geo
wp arborisis reindex-geo

# Stats
wp arborisis aggregate-plays [--all] [--date=YYYY-MM-DD]
wp arborisis compute-trending
wp arborisis cleanup-plays [--days=90]
wp arborisis warm-cache

# Audio
wp arborisis extract-metadata <sound_id>
```

## 📝 Fichiers de configuration

| Fichier                     | Description                           |
|-----------------------------|---------------------------------------|
| `wp-config.php`             | Config WordPress + env vars           |
| `.env`                      | Variables d'environnement             |
| `composer.json`             | Dépendances PHP                       |
| `opensearch-mapping.json`   | Mapping index OpenSearch              |
| `.gitignore`                | Fichiers ignorés par Git              |
| `README.md`                 | Documentation complète                |
| `INSTALLATION.md`           | Guide installation rapide             |

## 🔒 Sécurité

- ✅ Validation MIME types (audio uniquement)
- ✅ Rate limiting uploads (configurable)
- ✅ Anti-spam plays (fingerprinting IP + user agent)
- ✅ Presigned URLs expirées (15min)
- ✅ Permissions granulaires (capabilities)
- ✅ Sanitization inputs (tous les endpoints)
- ✅ CSRF protection (nonces WP)
- ✅ SQL injection protection (prepared statements)

## 🚀 Performance

### Caching Redis

| Pattern              | TTL    | Invalidation                    |
|----------------------|--------|---------------------------------|
| `arb:map:*`          | 5min   | new sound + geo                 |
| `arb:search:*`       | 2min   | new sound                       |
| `arb:graph:*`        | 10min  | new sound, tags changed         |
| `arb:stats:global`   | 1h     | cron aggregation                |
| `arb:stats:user:*`   | 30min  | user play/like event            |
| `arb:leaderboards:*` | 1h     | cron aggregation                |
| `arb:sound:*:detail` | 1h     | sound updated                   |

### Indexes DB

- ✅ `arb_geo_index` : geohash, lat, lon
- ✅ `arb_likes` : user_id, sound_id, unique(user+sound)
- ✅ `arb_plays` : sound_id, created_at, fingerprint
- ✅ `arb_plays_daily` : (sound_id, day), day

### OpenSearch

- 2 shards, 1 replica
- Refresh interval: 1s
- Custom analyzer (lowercase, asciifolding, stop)
- Function score : trending + recency

## 📈 Statistiques code

| Type           | Nombre | Lignes (approx) |
|----------------|--------|-----------------|
| Plugins        | 6      | -               |
| Classes PHP    | 24     | ~4000           |
| Endpoints REST | 18     | -               |
| Tables DB      | 5      | -               |
| Commands CLI   | 8      | -               |

## ✅ Fonctionnalités implémentées (100%)

- [x] CPT Sound + taxonomies
- [x] Rôles customisés (uploader, moderator)
- [x] Upload direct S3 (presign/finalize)
- [x] Extraction metadata audio (ffprobe)
- [x] OpenSearch full-text search
- [x] Fallback WordPress search
- [x] Index géospatial
- [x] Clustering map serveur
- [x] Tracking plays (anti-spam)
- [x] Système likes
- [x] Agrégation stats daily
- [x] Trending scores
- [x] Leaderboards
- [x] Graph explore (similarité)
- [x] Cache Redis multi-niveaux
- [x] WP-CLI commands complets
- [x] API REST complète
- [x] Documentation installation

## 🎯 Prochaines étapes

1. **Thème frontend** : Vite + Tailwind + composants UI
2. **Tests** : PHPUnit + tests d'intégration
3. **CI/CD** : GitHub Actions
4. **Monitoring** : Logs + métriques
5. **Documentation API** : Swagger/OpenAPI
6. **Mobile** : API optimisée + PWA

## 📞 Support

Documentation : `README.md` + `INSTALLATION.md`
