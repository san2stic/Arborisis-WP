<?php
/**
 * Template Name: API Documentation
 * Description: Documentation de l'API REST Arborisis
 */

get_header();
?>

<div class="py-12 bg-white dark:bg-dark-900 min-h-screen">
    <div class="container-custom max-w-6xl">

        <header class="mb-12">
            <h1 class="text-5xl font-display font-bold text-dark-900 dark:text-dark-50 mb-4">
                Documentation API
            </h1>
            <p class="text-xl text-dark-600 dark:text-dark-400">
                API REST Arborisis v1 - Accès programmatique aux enregistrements sonores
            </p>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

            <!-- Sidebar Navigation -->
            <nav class="lg:col-span-1">
                <div class="card sticky top-4">
                    <div class="card-body">
                        <h3 class="font-bold mb-4">Navigation</h3>
                        <ul class="space-y-2 text-sm">
                            <li><a href="#intro" class="text-primary-600 hover:underline">Introduction</a></li>
                            <li><a href="#sounds" class="text-primary-600 hover:underline">Sounds</a></li>
                            <li><a href="#users" class="text-primary-600 hover:underline">Users</a></li>
                            <li><a href="#stats" class="text-primary-600 hover:underline">Stats</a></li>
                            <li><a href="#search" class="text-primary-600 hover:underline">Search</a></li>
                            <li><a href="#map" class="text-primary-600 hover:underline">Map</a></li>
                            <li><a href="#graph" class="text-primary-600 hover:underline">Graph</a></li>
                            <li><a href="#errors" class="text-primary-600 hover:underline">Errors</a></li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <article class="lg:col-span-3 prose prose-lg dark:prose-invert max-w-none">

                <section id="intro">
                    <h2>Introduction</h2>
                    <p>
                        L'API Arborisis permet un accès programmatique aux enregistrements sonores, métadonnées,
                        statistiques et fonctionnalités de recherche. Toutes les requêtes utilisent HTTPS et
                        retournent du JSON.
                    </p>

                    <h3>Base URL</h3>
                    <pre><code><?php echo home_url('/wp-json/arborisis/v1'); ?></code></pre>

                    <h3>Authentication</h3>
                    <p>
                        Les endpoints publics (lecture) ne nécessitent pas d'authentification.
                        Pour uploader ou modifier des sons, utilisez WordPress Nonce ou Application Passwords.
                    </p>

                    <h3>Rate Limiting</h3>
                    <p>
                        Actuellement : <strong>Aucune limite</strong> stricte. Utilisez de manière raisonnable
                        (max 60 req/min recommandé). Des limites seront appliquées en cas d'abus.
                    </p>
                </section>

                <section id="sounds" class="mt-16">
                    <h2>Sounds</h2>

                    <!-- GET /sounds -->
                    <div class="api-endpoint">
                        <h3>
                            <span class="badge-get">GET</span>
                            /sounds
                        </h3>
                        <p>Liste les enregistrements sonores avec filtres et pagination.</p>

                        <h4>Paramètres Query</h4>
                        <table>
                            <thead>
                                <tr>
                                    <th>Paramètre</th>
                                    <th>Type</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>per_page</code></td>
                                    <td>integer</td>
                                    <td>Nombre de résultats (défaut: 10, max: 100)</td>
                                </tr>
                                <tr>
                                    <td><code>page</code></td>
                                    <td>integer</td>
                                    <td>Numéro de page (défaut: 1)</td>
                                </tr>
                                <tr>
                                    <td><code>orderby</code></td>
                                    <td>string</td>
                                    <td>Tri: <code>date</code>, <code>recent</code>, <code>popular</code>, <code>trending</code></td>
                                </tr>
                                <tr>
                                    <td><code>tag</code></td>
                                    <td>string</td>
                                    <td>Filtrer par tag (slug)</td>
                                </tr>
                                <tr>
                                    <td><code>author</code></td>
                                    <td>integer</td>
                                    <td>Filtrer par auteur (user ID)</td>
                                </tr>
                            </tbody>
                        </table>

                        <h4>Exemple</h4>
                        <pre><code>curl <?php echo home_url('/wp-json/arborisis/v1/sounds?orderby=trending&per_page=8'); ?></code></pre>

                        <h4>Réponse</h4>
                        <pre><code>{
  "sounds": [
    {
      "id": 123,
      "title": "Chant de rossignol philomèle",
      "description": "Enregistré à l'aube...",
      "audioUrl": "https://s3.../audio.mp3",
      "thumbnail": "https://.../thumb.jpg",
      "duration": 185.4,
      "plays_count": 1240,
      "likes_count": 89,
      "author": "Jean Dupont",
      "author_username": "jean-dupont",
      "date": "2024-05-15T06:30:00",
      "tags": ["oiseau", "forêt", "aube"],
      "license": "CC-BY",
      "latitude": 48.4084,
      "longitude": 2.6964,
      "location_name": "Forêt de Fontainebleau"
    }
  ],
  "total": 156,
  "pages": 20
}</code></pre>
                    </div>

                    <!-- GET /sounds/{id} -->
                    <div class="api-endpoint mt-8">
                        <h3>
                            <span class="badge-get">GET</span>
                            /sounds/{id}
                        </h3>
                        <p>Récupère un enregistrement spécifique.</p>

                        <h4>Exemple</h4>
                        <pre><code>curl <?php echo home_url('/wp-json/arborisis/v1/sounds/123'); ?></code></pre>
                    </div>

                    <!-- POST /sounds/{id}/like -->
                    <div class="api-endpoint mt-8">
                        <h3>
                            <span class="badge-post">POST</span>
                            /sounds/{id}/like
                        </h3>
                        <p>Toggle like sur un enregistrement (authentification requise).</p>

                        <h4>Réponse</h4>
                        <pre><code>{
  "success": true,
  "liked": true,
  "likes_count": 90
}</code></pre>
                    </div>

                    <!-- POST /sounds/{id}/play -->
                    <div class="api-endpoint mt-8">
                        <h3>
                            <span class="badge-post">POST</span>
                            /sounds/{id}/play
                        </h3>
                        <p>Enregistre une écoute (avec anti-spam par fingerprint).</p>

                        <h4>Réponse</h4>
                        <pre><code>{
  "success": true,
  "plays_count": 1241
}</code></pre>
                    </div>

                </section>

                <section id="users" class="mt-16">
                    <h2>Users</h2>

                    <!-- GET /users/{username} -->
                    <div class="api-endpoint">
                        <h3>
                            <span class="badge-get">GET</span>
                            /users/{username}
                        </h3>
                        <p>Récupère le profil public d'un utilisateur.</p>

                        <h4>Exemple</h4>
                        <pre><code>curl <?php echo home_url('/wp-json/arborisis/v1/users/jean-dupont'); ?></code></pre>

                        <h4>Réponse</h4>
                        <pre><code>{
  "id": 42,
  "username": "jean-dupont",
  "name": "Jean Dupont",
  "avatar": "https://.../avatar.jpg",
  "bio": "Field recorder passionné...",
  "website": "https://example.com",
  "twitter": "@jeandupont",
  "sounds_count": 28,
  "total_plays": 45230,
  "total_likes": 1890,
  "joined": "2023-01-15T10:00:00"
}</code></pre>
                    </div>

                    <!-- GET /users/{username}/sounds -->
                    <div class="api-endpoint mt-8">
                        <h3>
                            <span class="badge-get">GET</span>
                            /users/{username}/sounds
                        </h3>
                        <p>Liste les enregistrements d'un utilisateur (même format que /sounds).</p>
                    </div>
                </section>

                <section id="stats" class="mt-16">
                    <h2>Stats</h2>

                    <!-- GET /stats/global -->
                    <div class="api-endpoint">
                        <h3>
                            <span class="badge-get">GET</span>
                            /stats/global
                        </h3>
                        <p>Statistiques globales de la plateforme.</p>

                        <h4>Réponse</h4>
                        <pre><code>{
  "total_sounds": 12845,
  "total_plays": 3420567,
  "total_users": 1234,
  "countries_count": 87,
  "timeline": [
    {"date": "2024-01-01", "plays": 12450},
    {"date": "2024-01-02", "plays": 13890}
  ]
}</code></pre>
                    </div>

                    <!-- GET /stats/leaderboards -->
                    <div class="api-endpoint mt-8">
                        <h3>
                            <span class="badge-get">GET</span>
                            /stats/leaderboards
                        </h3>
                        <p>Classements (top sons ou top users).</p>

                        <h4>Paramètres</h4>
                        <table>
                            <thead>
                                <tr>
                                    <th>Paramètre</th>
                                    <th>Type</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>type</code></td>
                                    <td>string</td>
                                    <td><code>sounds</code> ou <code>users</code></td>
                                </tr>
                                <tr>
                                    <td><code>period</code></td>
                                    <td>string</td>
                                    <td><code>7d</code>, <code>30d</code>, <code>all</code></td>
                                </tr>
                            </tbody>
                        </table>

                        <h4>Exemple</h4>
                        <pre><code>curl <?php echo home_url('/wp-json/arborisis/v1/stats/leaderboards?type=sounds&period=30d'); ?></code></pre>
                    </div>
                </section>

                <section id="search" class="mt-16">
                    <h2>Search</h2>

                    <!-- GET /search -->
                    <div class="api-endpoint">
                        <h3>
                            <span class="badge-get">GET</span>
                            /search
                        </h3>
                        <p>Recherche full-text dans les enregistrements (OpenSearch ou WordPress).</p>

                        <h4>Paramètres</h4>
                        <table>
                            <thead>
                                <tr>
                                    <th>Paramètre</th>
                                    <th>Type</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>q</code></td>
                                    <td>string</td>
                                    <td>Requête de recherche</td>
                                </tr>
                                <tr>
                                    <td><code>per_page</code></td>
                                    <td>integer</td>
                                    <td>Résultats par page</td>
                                </tr>
                            </tbody>
                        </table>

                        <h4>Exemple</h4>
                        <pre><code>curl <?php echo home_url('/wp-json/arborisis/v1/search?q=rossignol&per_page=20'); ?></code></pre>
                    </div>
                </section>

                <section id="map" class="mt-16">
                    <h2>Map</h2>

                    <!-- GET /map/sounds -->
                    <div class="api-endpoint">
                        <h3>
                            <span class="badge-get">GET</span>
                            /map/sounds
                        </h3>
                        <p>Récupère les enregistrements géolocalisés dans une zone (avec clustering).</p>

                        <h4>Paramètres</h4>
                        <table>
                            <thead>
                                <tr>
                                    <th>Paramètre</th>
                                    <th>Type</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>bbox</code></td>
                                    <td>string</td>
                                    <td>Bounding box: "minLat,minLon,maxLat,maxLon"</td>
                                </tr>
                                <tr>
                                    <td><code>zoom</code></td>
                                    <td>integer</td>
                                    <td>Niveau de zoom (1-18)</td>
                                </tr>
                            </tbody>
                        </table>

                        <h4>Exemple</h4>
                        <pre><code>curl "<?php echo home_url('/wp-json/arborisis/v1/map/sounds?bbox=48.8,2.3,48.9,2.4&zoom=12'); ?>"</code></pre>

                        <h4>Réponse</h4>
                        <pre><code>{
  "clusters": [
    {
      "latitude": 48.8566,
      "longitude": 2.3522,
      "count": 15,
      "sounds": [...]
    }
  ]
}</code></pre>
                    </div>
                </section>

                <section id="graph" class="mt-16">
                    <h2>Graph</h2>

                    <!-- GET /graph/explore -->
                    <div class="api-endpoint">
                        <h3>
                            <span class="badge-get">GET</span>
                            /graph/explore
                        </h3>
                        <p>Graphe d'exploration basé sur la similarité (tags, géo, popularité).</p>

                        <h4>Paramètres</h4>
                        <table>
                            <thead>
                                <tr>
                                    <th>Paramètre</th>
                                    <th>Type</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>sound_id</code></td>
                                    <td>integer</td>
                                    <td>Son de départ</td>
                                </tr>
                                <tr>
                                    <td><code>depth</code></td>
                                    <td>integer</td>
                                    <td>Profondeur (défaut: 2)</td>
                                </tr>
                                <tr>
                                    <td><code>max_nodes</code></td>
                                    <td>integer</td>
                                    <td>Nodes max (défaut: 50)</td>
                                </tr>
                            </tbody>
                        </table>

                        <h4>Réponse</h4>
                        <pre><code>{
  "nodes": [
    {"id": 123, "title": "...", "plays": 1240}
  ],
  "edges": [
    {"source": 123, "target": 456, "weight": 0.85}
  ]
}</code></pre>
                    </div>
                </section>

                <section id="errors" class="mt-16">
                    <h2>Codes d'Erreur</h2>

                    <table>
                        <thead>
                            <tr>
                                <th>Code HTTP</th>
                                <th>Signification</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>200</code></td>
                                <td>Succès</td>
                            </tr>
                            <tr>
                                <td><code>400</code></td>
                                <td>Bad Request - Paramètres invalides</td>
                            </tr>
                            <tr>
                                <td><code>401</code></td>
                                <td>Unauthorized - Authentification requise</td>
                            </tr>
                            <tr>
                                <td><code>403</code></td>
                                <td>Forbidden - Permissions insuffisantes</td>
                            </tr>
                            <tr>
                                <td><code>404</code></td>
                                <td>Not Found - Ressource inexistante</td>
                            </tr>
                            <tr>
                                <td><code>429</code></td>
                                <td>Too Many Requests - Rate limit dépassé</td>
                            </tr>
                            <tr>
                                <td><code>500</code></td>
                                <td>Internal Server Error</td>
                            </tr>
                        </tbody>
                    </table>

                    <h3>Format d'Erreur</h3>
                    <pre><code>{
  "code": "rest_invalid_param",
  "message": "Invalid parameter(s): per_page",
  "data": {
    "status": 400,
    "params": {"per_page": "Must be between 1 and 100"}
  }
}</code></pre>
                </section>

                <section id="examples" class="mt-16">
                    <h2>Exemples d'Utilisation</h2>

                    <h3>JavaScript (Fetch API)</h3>
                    <pre><code>const sounds = await fetch('<?php echo home_url('/wp-json/arborisis/v1/sounds?orderby=trending'); ?>')
  .then(res => res.json());

console.log(sounds.sounds);</code></pre>

                    <h3>Python</h3>
                    <pre><code>import requests

response = requests.get('<?php echo home_url('/wp-json/arborisis/v1/sounds'); ?>', params={
    'orderby': 'trending',
    'per_page': 20
})

sounds = response.json()['sounds']</code></pre>

                    <h3>PHP</h3>
                    <pre><code>$response = wp_remote_get('<?php echo home_url('/wp-json/arborisis/v1/sounds?orderby=recent'); ?>');
$sounds = json_decode(wp_remote_retrieve_body($response), true)['sounds'];</code></pre>
                </section>

            </article>
        </div>

    </div>
</div>

<style>
.badge-get {
    display: inline-block;
    background: #10b981;
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: bold;
}
.badge-post {
    display: inline-block;
    background: #3b82f6;
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: bold;
}
.api-endpoint {
    border-left: 4px solid #6366f1;
    padding-left: 1.5rem;
    margin-top: 2rem;
}
</style>

<?php get_footer(); ?>
