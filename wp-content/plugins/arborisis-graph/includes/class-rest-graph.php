<?php
/**
 * REST API Endpoints for Graph
 */

if (!defined('ABSPATH')) exit;

class ARB_REST_Graph {

    /**
     * Register REST routes
     */
    public static function register_routes() {
        register_rest_route('arborisis/v1', '/graph/explore', [
            'methods'             => 'GET',
            'callback'            => [__CLASS__, 'explore'],
            'permission_callback' => '__return_true',
            'args'                => self::get_graph_params(),
        ]);
    }

    /**
     * Graph explore endpoint
     */
    public static function explore($request) {
        $seed_id = (int) $request->get_param('seed_id');
        $depth = min((int) $request->get_param('depth') ?: 2, 3);
        $max_nodes = min((int) $request->get_param('max_nodes') ?: 50, 100);

        if (!$seed_id) {
            return new WP_Error(
                'missing_seed',
                __('seed_id parameter is required', 'arborisis-graph'),
                ['status' => 400]
            );
        }

        // Verify seed sound exists
        $seed = get_post($seed_id);
        if (!$seed || $seed->post_type !== 'sound') {
            return new WP_Error(
                'invalid_seed',
                __('Invalid seed sound ID', 'arborisis-graph'),
                ['status' => 404]
            );
        }

        // Check cache
        $cache_key = "arb:graph:{$seed_id}:{$depth}:{$max_nodes}";

        $data = arb_cache_get_or_compute($cache_key, 600, function() use ($seed_id, $depth, $max_nodes) {
            return ARB_Graph_Builder::build($seed_id, $depth, $max_nodes);
        });

        return new WP_REST_Response([
            'nodes' => $data['nodes'],
            'edges' => $data['edges'],
            'meta'  => [
                'seed_id'   => $seed_id,
                'depth'     => $depth,
                'max_nodes' => $max_nodes,
                'node_count' => count($data['nodes']),
                'edge_count' => count($data['edges']),
            ],
        ], 200);
    }

    /**
     * Graph parameters
     */
    private static function get_graph_params() {
        return [
            'seed_id' => [
                'description' => __('Starting sound ID', 'arborisis-graph'),
                'type'        => 'integer',
                'required'    => true,
            ],
            'depth' => [
                'description' => __('Graph depth (max 3)', 'arborisis-graph'),
                'type'        => 'integer',
                'default'     => 2,
                'minimum'     => 1,
                'maximum'     => 3,
            ],
            'max_nodes' => [
                'description' => __('Maximum nodes (max 100)', 'arborisis-graph'),
                'type'        => 'integer',
                'default'     => 50,
                'minimum'     => 10,
                'maximum'     => 100,
            ],
        ];
    }
}
