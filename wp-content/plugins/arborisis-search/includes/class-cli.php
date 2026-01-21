<?php
/**
 * WP-CLI Commands for Search
 */

if (!defined('ABSPATH')) exit;

class ARB_Search_CLI {

    /**
     * Reindex all sounds in OpenSearch
     *
     * ## OPTIONS
     *
     * [--batch-size=<size>]
     * : Number of sounds to index per batch
     * ---
     * default: 100
     * ---
     *
     * ## EXAMPLES
     *
     *     wp arborisis reindex
     *     wp arborisis reindex --batch-size=50
     */
    public static function reindex($args, $assoc_args) {
        $batch_size = isset($assoc_args['batch-size']) ? (int) $assoc_args['batch-size'] : 100;
        $page = 1;
        $total = 0;

        // Check OpenSearch availability
        if (!ARB_OpenSearch_Client::is_available()) {
            WP_CLI::error('OpenSearch is not available');
            return;
        }

        // Check if index exists
        if (!ARB_OpenSearch_Client::index_exists()) {
            WP_CLI::line('Index does not exist. Creating...');
            if (!ARB_OpenSearch_Client::create_index()) {
                WP_CLI::error('Failed to create index');
                return;
            }
            WP_CLI::success('Index created');
        }

        WP_CLI::line('Starting reindex...');

        while (true) {
            $sounds = get_posts([
                'post_type'      => 'sound',
                'post_status'    => 'publish',
                'posts_per_page' => $batch_size,
                'paged'          => $page,
                'orderby'        => 'ID',
                'order'          => 'ASC',
            ]);

            if (empty($sounds)) {
                break;
            }

            $sound_ids = wp_list_pluck($sounds, 'ID');
            $success = ARB_Indexer::bulk_index($sound_ids);

            if ($success) {
                $total += count($sound_ids);
                WP_CLI::line("Indexed {$total} sounds...");
            } else {
                WP_CLI::warning("Failed to index batch at page {$page}");
            }

            $page++;
            sleep(1); // Throttle to avoid overwhelming OpenSearch
        }

        WP_CLI::success("Reindexed {$total} sounds");
    }

    /**
     * Process OpenSearch queue (for async indexing)
     *
     * ## OPTIONS
     *
     * [--limit=<limit>]
     * : Maximum number of items to process
     * ---
     * default: 100
     * ---
     *
     * ## EXAMPLES
     *
     *     wp arborisis process-opensearch-queue
     *     wp arborisis process-opensearch-queue --limit=50
     */
    public static function process_queue($args, $assoc_args) {
        global $wpdb;

        $limit = isset($assoc_args['limit']) ? (int) $assoc_args['limit'] : 100;
        $table = $wpdb->prefix . 'arb_opensearch_queue';

        // Get pending items
        $items = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} WHERE processed_at IS NULL ORDER BY created_at ASC LIMIT %d",
            $limit
        ));

        if (empty($items)) {
            WP_CLI::line('No items in queue');
            return;
        }

        WP_CLI::line(sprintf('Processing %d items...', count($items)));

        $processed = 0;
        foreach ($items as $item) {
            $success = false;

            if ($item->action === 'index') {
                $success = ARB_Indexer::index_sound_direct($item->sound_id);
            } elseif ($item->action === 'delete') {
                ARB_Indexer::delete_sound($item->sound_id);
                $success = true;
            }

            if ($success) {
                $wpdb->update(
                    $table,
                    ['processed_at' => current_time('mysql')],
                    ['id' => $item->id]
                );
                $processed++;
            }
        }

        WP_CLI::success("Processed {$processed} items");
    }
}
