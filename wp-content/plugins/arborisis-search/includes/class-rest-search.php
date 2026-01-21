<?php
/**
 * REST API Endpoints for Search
 */

if (!defined('ABSPATH')) exit;

class ARB_REST_Search {

    /**
     * Register REST routes
     */
    public static function register_routes() {
        register_rest_route('arborisis/v1', '/search', [
            'methods'             => 'GET',
            'callback'            => [__CLASS__, 'search'],
            'permission_callback' => '__return_true',
            'args'                => self::get_search_params(),
        ]);
    }

    /**
     * Search endpoint
     */
    public static function search($request) {
        $params = [
            'q'        => $request->get_param('q'),
            'tags'     => $request->get_param('tags'),
            'lat'      => $request->get_param('lat'),
            'lon'      => $request->get_param('lon'),
            'radius'   => $request->get_param('radius') ?: 50,
            'page'     => $request->get_param('page') ?: 1,
            'per_page' => min($request->get_param('per_page') ?: 20, 100),
        ];

        // Try OpenSearch first
        $result = self::opensearch_search($params);

        // Fallback to WordPress search
        if ($result === null) {
            $result = self::fallback_search($params);
        }

        return new WP_REST_Response([
            'sounds' => array_map([__CLASS__, 'format_result'], $result['hits']),
            'total'  => $result['total'],
            'meta'   => [
                'engine'   => $result['engine'],
                'took_ms'  => $result['took'],
                'fallback' => $result['engine'] === 'fallback',
            ],
        ], 200);
    }

    /**
     * OpenSearch search
     */
    private static function opensearch_search($params) {
        $client = ARB_OpenSearch_Client::get();

        if (!$client || !ARB_OpenSearch_Client::is_available()) {
            return null;
        }

        $query = ['bool' => ['must' => [], 'filter' => []]];

        // Text search
        if (!empty($params['q'])) {
            $query['bool']['must'][] = [
                'multi_match' => [
                    'query'     => $params['q'],
                    'fields'    => ['title^3', 'content', 'location_name'],
                    'type'      => 'best_fields',
                    'fuzziness' => 'AUTO',
                ],
            ];
        }

        // Tag filter
        if (!empty($params['tags'])) {
            $tags = array_map('trim', explode(',', $params['tags']));
            $query['bool']['filter'][] = ['terms' => ['tags' => $tags]];
        }

        // Geo filter
        if (!empty($params['lat']) && !empty($params['lon'])) {
            $query['bool']['filter'][] = [
                'geo_distance' => [
                    'distance' => $params['radius'] . 'km',
                    'location' => [
                        'lat' => (float) $params['lat'],
                        'lon' => (float) $params['lon'],
                    ],
                ],
            ];
        }

        // Function score for relevance
        $function_score = [
            'query'     => $query,
            'functions' => [
                [
                    'field_value_factor' => [
                        'field'   => 'trending_score',
                        'factor'  => 1.2,
                        'missing' => 1,
                    ],
                ],
                [
                    'gauss' => [
                        'created_at' => [
                            'origin' => 'now',
                            'scale'  => '30d',
                            'decay'  => 0.5,
                        ],
                    ],
                ],
            ],
            'score_mode'  => 'multiply',
            'boost_mode'  => 'multiply',
        ];

        $body = [
            'query' => $function_score,
            'from'  => ($params['page'] - 1) * $params['per_page'],
            'size'  => $params['per_page'],
        ];

        try {
            $start = microtime(true);
            $response = $client->search([
                'index' => ARB_OpenSearch_Client::get_index(),
                'body'  => $body,
            ]);
            $took = round((microtime(true) - $start) * 1000);

            return [
                'hits'   => $response['hits']['hits'],
                'total'  => $response['hits']['total']['value'],
                'took'   => $took,
                'engine' => 'opensearch',
            ];
        } catch (Exception $e) {
            error_log("OpenSearch search error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Fallback WordPress search
     */
    private static function fallback_search($params) {
        $args = [
            'post_type'      => 'sound',
            'post_status'    => 'publish',
            'posts_per_page' => $params['per_page'],
            'paged'          => $params['page'],
        ];

        // Text search
        if (!empty($params['q'])) {
            $args['s'] = sanitize_text_field($params['q']);
        }

        // Tag filter
        if (!empty($params['tags'])) {
            $tags = array_map('trim', explode(',', $params['tags']));
            $args['tax_query'] = [[
                'taxonomy' => 'sound_tag',
                'field'    => 'slug',
                'terms'    => $tags,
            ]];
        }

        // Geo filter (simple bbox approximation)
        if (!empty($params['lat']) && !empty($params['lon']) && !empty($params['radius'])) {
            $lat_delta = $params['radius'] / 111; // rough km to degrees
            $lon_delta = $params['radius'] / (111 * cos(deg2rad($params['lat'])));

            $args['meta_query'] = [
                'relation' => 'AND',
                [
                    'key'     => '_arb_latitude',
                    'value'   => [$params['lat'] - $lat_delta, $params['lat'] + $lat_delta],
                    'type'    => 'DECIMAL(10,8)',
                    'compare' => 'BETWEEN',
                ],
                [
                    'key'     => '_arb_longitude',
                    'value'   => [$params['lon'] - $lon_delta, $params['lon'] + $lon_delta],
                    'type'    => 'DECIMAL(11,8)',
                    'compare' => 'BETWEEN',
                ],
            ];
        }

        $query = new WP_Query($args);

        $hits = array_map(function($post) {
            return ['_source' => ARB_Indexer::sound_to_doc($post->ID)];
        }, $query->posts);

        return [
            'hits'   => $hits,
            'total'  => $query->found_posts,
            'took'   => 0,
            'engine' => 'fallback',
        ];
    }

    /**
     * Format search result
     */
    private static function format_result($hit) {
        $source = $hit['_source'];

        return [
            'id'         => $source['id'],
            'title'      => $source['title'],
            'snippet'    => wp_trim_words($source['content'], 20),
            'score'      => $hit['_score'] ?? 0,
            'author'     => $source['author_name'],
            'tags'       => $source['tags'],
            'plays'      => $source['plays'],
            'likes'      => $source['likes'],
            'created_at' => $source['created_at'],
        ];
    }

    /**
     * Search parameters
     */
    private static function get_search_params() {
        return [
            'q' => [
                'description' => __('Search query', 'arborisis-search'),
                'type'        => 'string',
            ],
            'tags' => [
                'description' => __('Filter by tags (comma-separated)', 'arborisis-search'),
                'type'        => 'string',
            ],
            'lat' => [
                'description' => __('Latitude for geo search', 'arborisis-search'),
                'type'        => 'number',
            ],
            'lon' => [
                'description' => __('Longitude for geo search', 'arborisis-search'),
                'type'        => 'number',
            ],
            'radius' => [
                'description' => __('Radius in km for geo search', 'arborisis-search'),
                'type'        => 'integer',
                'default'     => 50,
            ],
            'page' => [
                'description' => __('Page number', 'arborisis-search'),
                'type'        => 'integer',
                'default'     => 1,
                'minimum'     => 1,
            ],
            'per_page' => [
                'description' => __('Results per page', 'arborisis-search'),
                'type'        => 'integer',
                'default'     => 20,
                'minimum'     => 1,
                'maximum'     => 100,
            ],
        ];
    }
}
