<?php
/**
 * Plays Tracker
 */

if (!defined('ABSPATH')) exit;

class ARB_Plays_Tracker {

    /**
     * Track a play
     */
    public static function track($sound_id) {
        global $wpdb;

        // Get fingerprint
        $ip = arb_get_client_ip();
        $user_agent = arb_get_user_agent();
        $ip_hash = arb_generate_hash($ip);
        $user_agent_hash = arb_generate_hash($user_agent);
        $user_id = is_user_logged_in() ? get_current_user_id() : null;

        // Anti-spam: check if same fingerprint played this sound in last 5 minutes
        $table = $wpdb->prefix . 'arb_plays';
        $recent_play = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$table}
             WHERE sound_id = %d
               AND ip_hash = %s
               AND user_agent_hash = %s
               AND created_at > DATE_SUB(NOW(), INTERVAL 5 MINUTE)
             LIMIT 1",
            $sound_id,
            $ip_hash,
            $user_agent_hash
        ));

        if ($recent_play) {
            return new WP_REST_Response([
                'success' => false,
                'message' => __('Play already counted recently', 'arborisis-stats'),
            ], 200);
        }

        // Insert play event
        $inserted = $wpdb->insert($table, [
            'sound_id'        => $sound_id,
            'user_id'         => $user_id,
            'ip_hash'         => $ip_hash,
            'user_agent_hash' => $user_agent_hash,
            'created_at'      => current_time('mysql'),
        ]);

        if (!$inserted) {
            return new WP_Error('db_error', __('Failed to track play', 'arborisis-stats'), ['status' => 500]);
        }

        // Increment cached count
        $current_count = (int) get_post_meta($sound_id, '_arb_plays_count', true);
        update_post_meta($sound_id, '_arb_plays_count', $current_count + 1);

        // Invalidate cache
        arb_redis_delete_pattern('arb:stats:*');
        arb_redis_delete_pattern('arb:map:*');

        return new WP_REST_Response([
            'success' => true,
            'plays'   => $current_count + 1,
        ], 200);
    }

    /**
     * Get plays count for sound
     */
    public static function get_count($sound_id) {
        return (int) get_post_meta($sound_id, '_arb_plays_count', true);
    }

    /**
     * Get plays timeline for sound
     */
    public static function get_timeline($sound_id, $days = 30) {
        global $wpdb;
        $table = $wpdb->prefix . 'arb_plays_daily';

        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT day, plays_count
             FROM {$table}
             WHERE sound_id = %d
               AND day >= DATE_SUB(CURDATE(), INTERVAL %d DAY)
             ORDER BY day ASC",
            $sound_id,
            $days
        ));

        return array_map(function($row) {
            return [
                'date'  => $row->day,
                'plays' => (int) $row->plays_count,
            ];
        }, $results);
    }
}
