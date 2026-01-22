<?php
/**
 * REST API Endpoints for Stats
 */

if (!defined('ABSPATH'))
    exit;

class ARB_REST_Stats
{

    /**
     * Register REST routes
     */
    public static function register_routes()
    {
        // Track play
        register_rest_route('arborisis/v1', '/sounds/(?P<id>\d+)/play', [
            'methods' => 'POST',
            'callback' => [__CLASS__, 'track_play'],
            'permission_callback' => '__return_true',
        ]);

        // Toggle like
        register_rest_route('arborisis/v1', '/sounds/(?P<id>\d+)/like', [
            'methods' => 'POST',
            'callback' => [__CLASS__, 'toggle_like'],
            'permission_callback' => 'is_user_logged_in',
        ]);

        // Sound stats
        register_rest_route('arborisis/v1', '/sounds/(?P<id>\d+)/stats', [
            'methods' => 'GET',
            'callback' => [__CLASS__, 'sound_stats'],
            'permission_callback' => '__return_true',
        ]);

        // Global stats
        register_rest_route('arborisis/v1', '/stats/global', [
            'methods' => 'GET',
            'callback' => [__CLASS__, 'global_stats'],
            'permission_callback' => '__return_true',
        ]);

        // User stats
        register_rest_route('arborisis/v1', '/stats/user/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [__CLASS__, 'user_stats'],
            'permission_callback' => '__return_true',
        ]);

        // Leaderboards
        register_rest_route('arborisis/v1', '/stats/leaderboards', [
            'methods' => 'GET',
            'callback' => [__CLASS__, 'leaderboards'],
            'permission_callback' => '__return_true',
            'args' => [
                'type' => [
                    'default' => 'sounds',
                    'enum' => ['sounds', 'users'],
                ],
                'period' => [
                    'default' => 'all',
                    'enum' => ['7d', '30d', 'all'],
                ],
            ],
        ]);
    }

    /**
     * Track play
     */
    public static function track_play($request)
    {
        $sound_id = (int) $request['id'];
        return ARB_Plays_Tracker::track($sound_id);
    }

    /**
     * Toggle like
     */
    public static function toggle_like($request)
    {
        $sound_id = (int) $request['id'];
        return ARB_Likes_Manager::toggle($sound_id);
    }

    /**
     * Sound stats
     */
    public static function sound_stats($request)
    {
        $sound_id = (int) $request['id'];

        $plays_7d = ARB_Aggregator::get_plays_last_n_days($sound_id, 7);
        $plays_30d = ARB_Aggregator::get_plays_last_n_days($sound_id, 30);

        return new WP_REST_Response([
            'plays_total' => ARB_Plays_Tracker::get_count($sound_id),
            'plays_last_7d' => $plays_7d,
            'plays_last_30d' => $plays_30d,
            'likes_total' => ARB_Likes_Manager::get_count($sound_id),
            'timeline' => ARB_Plays_Tracker::get_timeline($sound_id, 30),
        ], 200);
    }

    /**
     * Global stats
     */
    public static function global_stats($request)
    {
        $cache_key = 'arb:stats:global';

        return arb_cache_get_or_compute($cache_key, 3600, function () {
            global $wpdb;

            $total_plays = (int) $wpdb->get_var(
                "SELECT SUM(plays_count) FROM {$wpdb->prefix}arb_plays_daily"
            );

            // Count unique countries from geo data
            $geo_table = $wpdb->prefix . 'arb_geo_index';
            $countries_count = 0;
            if ($wpdb->get_var("SHOW TABLES LIKE '{$geo_table}'") === $geo_table) {
                // Estimate countries from geohash first 2 chars (rough continent/region)
                $countries_count = (int) $wpdb->get_var(
                    "SELECT COUNT(DISTINCT LEFT(geohash, 3)) FROM {$geo_table}"
                );
            }

            return [
                'total_sounds' => wp_count_posts('sound')->publish,
                'total_plays' => $total_plays,
                'total_users' => count_users()['total_users'],
                'countries_count' => $countries_count,
                'top_sounds' => ARB_Aggregator::top_sounds(10),
                'top_users' => ARB_Aggregator::top_users(10),
                'timeline' => ARB_Aggregator::plays_timeline(30),
            ];
        });
    }

    /**
     * User stats
     */
    public static function user_stats($request)
    {
        $user_id = (int) $request['id'];
        $cache_key = "arb:stats:user:{$user_id}";

        return arb_cache_get_or_compute($cache_key, 1800, function () use ($user_id) {
            global $wpdb;

            $sounds = get_posts([
                'post_type' => 'sound',
                'post_status' => 'publish',
                'author' => $user_id,
                'posts_per_page' => -1,
                'fields' => 'ids',
            ]);

            $total_plays = 0;
            $total_likes = 0;
            $top_sounds = [];

            foreach ($sounds as $sound_id) {
                $plays = ARB_Plays_Tracker::get_count($sound_id);
                $likes = ARB_Likes_Manager::get_count($sound_id);

                $total_plays += $plays;
                $total_likes += $likes;

                $top_sounds[] = [
                    'id' => $sound_id,
                    'title' => get_the_title($sound_id),
                    'plays' => $plays,
                    'likes' => $likes,
                ];
            }

            // Sort top sounds by plays
            usort($top_sounds, fn($a, $b) => $b['plays'] <=> $a['plays']);
            $top_sounds = array_slice($top_sounds, 0, 10);

            return [
                'user_id' => $user_id,
                'total_sounds' => count($sounds),
                'total_plays' => $total_plays,
                'total_likes' => $total_likes,
                'top_sounds' => $top_sounds,
                'plays_timeline' => [], // Could aggregate user's sounds timeline
            ];
        });
    }

    /**
     * Leaderboards
     */
    public static function leaderboards($request)
    {
        $type = $request->get_param('type');
        $period = $request->get_param('period');

        $cache_key = "arb:leaderboards:{$type}:{$period}";

        return arb_cache_get_or_compute($cache_key, 3600, function () use ($type, $period) {
            if ($type === 'sounds') {
                $items = ARB_Aggregator::top_sounds(50, $period);
            } else {
                $items = ARB_Aggregator::top_users(50);
            }

            // Add rank
            $rank = 1;
            foreach ($items as &$item) {
                $item['rank'] = $rank++;
            }

            // Return with key matching the type for frontend compatibility
            $key = $type === 'sounds' ? 'sounds' : 'users';
            return [
                $key => $items,
                'total' => count($items),
            ];
        });
    }
}
