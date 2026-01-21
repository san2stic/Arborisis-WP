<?php
/**
 * Seeding script for Arborisis
 * Run with: wp eval-file scripts/seed-data.php
 */

if (!defined('ABSPATH')) {
    die('Please run this script within the WordPress environment.');
}

echo "🌱 Starting data seeding...\n";

// Clear existing sounds?
// $cleanup = true;
// if ($cleanup) { ... }

$tags_list = ['Nature', 'Urbain', 'Oiseaux', 'Eau', 'Vent', 'Forêt', 'Pluie', 'Transport', 'Humain'];
$authors = get_users();
if (empty($authors)) {
    // create a dummy author if needed, or just use admin (ID 1)
    $author_id = 1;
} else {
    $author_id = $authors[0]->ID;
}

// France Bounding Box roughly
$min_lat = 42.0;
$max_lat = 51.0;
$min_lon = -4.0;
$max_lon = 8.0;

$count = 50;

for ($i = 0; $i < $count; $i++) {
    $title = 'Son ' . ($i + 1) . ' - ' . $tags_list[array_rand($tags_list)];

    // Create Post
    $post_id = wp_insert_post([
        'post_title' => $title,
        'post_content' => 'Description générée automatiquement pour le son ' . ($i + 1),
        'post_status' => 'publish',
        'post_type' => 'sound',
        'post_author' => $author_id,
    ]);

    if (is_wp_error($post_id)) {
        echo "Error creating post: " . $post_id->get_error_message() . "\n";
        continue;
    }

    // Assign Random Tags
    $random_tags = array_rand(array_flip($tags_list), rand(1, 3));
    if (!is_array($random_tags))
        $random_tags = [$random_tags];
    wp_set_object_terms($post_id, $random_tags, 'sound_tag');

    // Assign Meta
    $lat = $min_lat + (mt_rand() / mt_getrandmax()) * ($max_lat - $min_lat);
    $lon = $min_lon + (mt_rand() / mt_getrandmax()) * ($max_lon - $min_lon);

    update_post_meta($post_id, '_arb_latitude', $lat);
    update_post_meta($post_id, '_arb_longitude', $lon);

    // Stats
    update_post_meta($post_id, '_arb_plays_count', rand(0, 5000));
    update_post_meta($post_id, '_arb_likes_count', rand(0, 500));
    update_post_meta($post_id, '_arb_duration', rand(30, 300));

    // Indexing
    if (class_exists('ARB_Geo_Indexer')) {
        ARB_Geo_Indexer::index_sound($post_id);
    }

    echo "Created Sound #{$post_id}: {$title} ({$lat}, {$lon})\n";
}

echo "✅ Seeding complete! Created {$count} sounds.\n";

// Force cache clearing just in case
if (function_exists('wp_cache_flush')) {
    wp_cache_flush();
}
