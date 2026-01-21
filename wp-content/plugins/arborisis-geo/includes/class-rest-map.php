<?php
/**
 * REST API Endpoints for Map
 */

if (!defined('ABSPATH')) exit;

class ARB_REST_Map {

    /**
     * Register REST routes
     */
    public static function register_routes() {
        register_rest_route('arborisis/v1', '/map/sounds', [
            'methods'             => 'GET',
            'callback'            => [__CLASS__, 'map_sounds'],
            'permission_callback' => '__return_true',
            'args'                => self::get_map_params(),
        ]);
    }

    /**
     * Map sounds endpoint
     */
    public static function map_sounds($request) {
        $bbox_param = $request->get_param('bbox');
        $zoom = (int) $request->get_param('zoom') ?: 10;
        $page = (int) $request->get_param('page') ?: 1;
        $per_page = min((int) $request->get_param('per_page') ?: 100, 500);

        if (empty($bbox_param)) {
            return new WP_Error(
                'missing_bbox',
                __('bbox parameter is required', 'arborisis-geo'),
                ['status' => 400]
            );
        }

        // Parse bbox: "lat1,lon1,lat2,lon2"
        $bbox = array_map('floatval', explode(',', $bbox_param));

        if (count($bbox) !== 4) {
            return new WP_Error(
                'invalid_bbox',
                __('bbox format must be: lat1,lon1,lat2,lon2', 'arborisis-geo'),
                ['status' => 400]
            );
        }

        // Check cache
        $cache_key = "arb:map:{$zoom}:" . md5($bbox_param) . ":{$page}";
        $cached = wp_cache_get($cache_key);

        if ($cached !== false) {
            return new WP_REST_Response($cached, 200);
        }

        // Get clustered data
        $data = ARB_Clustering::cluster_sounds($bbox, $zoom, $page, $per_page);

        $response = [
            'clusters' => $data['clusters'],
            'total'    => $data['total'],
            'meta'     => [
                'zoom'     => $zoom,
                'page'     => $page,
                'per_page' => $per_page,
            ],
        ];

        // Cache for 5 minutes
        wp_cache_set($cache_key, $response, '', 300);

        return new WP_REST_Response($response, 200);
    }

    /**
     * Map parameters
     */
    private static function get_map_params() {
        return [
            'bbox' => [
                'description' => __('Bounding box (lat1,lon1,lat2,lon2)', 'arborisis-geo'),
                'type'        => 'string',
                'required'    => true,
            ],
            'zoom' => [
                'description' => __('Map zoom level', 'arborisis-geo'),
                'type'        => 'integer',
                'default'     => 10,
                'minimum'     => 1,
                'maximum'     => 20,
            ],
            'page' => [
                'description' => __('Page number', 'arborisis-geo'),
                'type'        => 'integer',
                'default'     => 1,
                'minimum'     => 1,
            ],
            'per_page' => [
                'description' => __('Results per page', 'arborisis-geo'),
                'type'        => 'integer',
                'default'     => 100,
                'minimum'     => 1,
                'maximum'     => 500,
            ],
        ];
    }
}
