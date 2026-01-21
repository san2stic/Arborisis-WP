<?php
/**
 * WP-CLI Commands for Geo
 */

if (!defined('ABSPATH')) exit;

class ARB_Geo_CLI {

    /**
     * Reindex all sounds geo data
     *
     * ## EXAMPLES
     *
     *     wp arborisis reindex-geo
     */
    public static function reindex($args, $assoc_args) {
        WP_CLI::line('Starting geo reindex...');

        $total = ARB_Geo_Indexer::reindex_all();

        WP_CLI::success("Reindexed {$total} sounds");
    }
}
