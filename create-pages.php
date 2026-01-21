<?php
/**
 * Script de création des pages WordPress pour Arborisis
 *
 * Usage: php create-pages.php
 * ou placer ce fichier à la racine et l'exécuter via le navigateur
 */

// Chargement de WordPress
require_once(__DIR__ . '/wp-load.php');

// Vérification des permissions
if (!current_user_can('manage_options')) {
    die('Vous devez être administrateur pour exécuter ce script.');
}

// Liste des pages à créer
$pages = [
    [
        'post_title'    => 'Explorer',
        'post_name'     => 'explore',
        'post_content'  => '',
        'template'      => 'page-explore.php',
        'menu_order'    => 1
    ],
    [
        'post_title'    => 'Carte',
        'post_name'     => 'map',
        'post_content'  => '',
        'template'      => 'page-map.php',
        'menu_order'    => 2
    ],
    [
        'post_title'    => 'Graphe',
        'post_name'     => 'graph',
        'post_content'  => '',
        'template'      => 'page-graph.php',
        'menu_order'    => 3
    ],
    [
        'post_title'    => 'Statistiques',
        'post_name'     => 'stats',
        'post_content'  => '',
        'template'      => 'page-stats.php',
        'menu_order'    => 4
    ],
    [
        'post_title'    => 'À propos',
        'post_name'     => 'about',
        'post_content'  => '',
        'template'      => 'page-about.php',
        'menu_order'    => 5
    ],
    [
        'post_title'    => 'Contact',
        'post_name'     => 'contact',
        'post_content'  => '',
        'template'      => 'page-contact.php',
        'menu_order'    => 6
    ],
    [
        'post_title'    => 'FAQ',
        'post_name'     => 'faq',
        'post_content'  => '',
        'template'      => 'page-faq.php',
        'menu_order'    => 7
    ],
    [
        'post_title'    => 'Règles de la communauté',
        'post_name'     => 'guidelines',
        'post_content'  => '',
        'template'      => 'page-guidelines.php',
        'menu_order'    => 8
    ],
    [
        'post_title'    => 'Licences',
        'post_name'     => 'licenses',
        'post_content'  => '',
        'template'      => 'page-licenses.php',
        'menu_order'    => 9
    ],
    [
        'post_title'    => 'Confidentialité',
        'post_name'     => 'privacy',
        'post_content'  => '',
        'template'      => 'page-privacy.php',
        'menu_order'    => 10
    ],
    [
        'post_title'    => 'Conditions d\'utilisation',
        'post_name'     => 'terms',
        'post_content'  => '',
        'template'      => 'page-terms.php',
        'menu_order'    => 11
    ],
    [
        'post_title'    => 'Documentation API',
        'post_name'     => 'api-docs',
        'post_content'  => '',
        'template'      => 'page-api-docs.php',
        'menu_order'    => 12
    ],
    [
        'post_title'    => 'Mon profil',
        'post_name'     => 'profile',
        'post_content'  => '',
        'template'      => 'page-profile.php',
        'menu_order'    => 13
    ],
    [
        'post_title'    => 'Mes sons',
        'post_name'     => 'my-sounds',
        'post_content'  => '',
        'template'      => 'page-my-sounds.php',
        'menu_order'    => 14
    ],
    [
        'post_title'    => 'Favoris',
        'post_name'     => 'favorites',
        'post_content'  => '',
        'template'      => 'page-favorites.php',
        'menu_order'    => 15
    ],
    [
        'post_title'    => 'Notifications',
        'post_name'     => 'notifications',
        'post_content'  => '',
        'template'      => 'page-notifications.php',
        'menu_order'    => 16
    ],
    [
        'post_title'    => 'Paramètres',
        'post_name'     => 'settings',
        'post_content'  => '',
        'template'      => 'page-settings.php',
        'menu_order'    => 17
    ],
    [
        'post_title'    => 'Uploader un son',
        'post_name'     => 'upload',
        'post_content'  => '',
        'template'      => 'page-upload.php',
        'menu_order'    => 18
    ],
];

$results = [
    'created' => [],
    'exists' => [],
    'errors' => []
];

echo "<h1>Création des pages WordPress</h1>\n";
echo "<pre>\n";

foreach ($pages as $page_data) {
    $page_title = $page_data['post_title'];
    $page_slug = $page_data['post_name'];

    // Vérifier si la page existe déjà
    $existing_page = get_page_by_path($page_slug);

    if ($existing_page) {
        echo "⚠️  La page '{$page_title}' existe déjà (ID: {$existing_page->ID})\n";
        $results['exists'][] = $page_title;

        // Mettre à jour le template si nécessaire
        $current_template = get_post_meta($existing_page->ID, '_wp_page_template', true);
        if ($current_template !== $page_data['template']) {
            update_post_meta($existing_page->ID, '_wp_page_template', $page_data['template']);
            echo "   → Template mis à jour: {$page_data['template']}\n";
        }

        continue;
    }

    // Créer la page
    $page_args = [
        'post_title'    => $page_data['post_title'],
        'post_name'     => $page_data['post_name'],
        'post_content'  => $page_data['post_content'],
        'post_status'   => 'publish',
        'post_type'     => 'page',
        'post_author'   => get_current_user_id(),
        'menu_order'    => $page_data['menu_order'],
        'comment_status' => 'closed',
        'ping_status'   => 'closed'
    ];

    $page_id = wp_insert_post($page_args);

    if (is_wp_error($page_id)) {
        echo "❌ Erreur lors de la création de '{$page_title}': " . $page_id->get_error_message() . "\n";
        $results['errors'][] = $page_title;
    } else {
        // Assigner le template
        update_post_meta($page_id, '_wp_page_template', $page_data['template']);

        echo "✅ Page '{$page_title}' créée avec succès (ID: {$page_id})\n";
        echo "   → URL: " . get_permalink($page_id) . "\n";
        echo "   → Template: {$page_data['template']}\n";

        $results['created'][] = $page_title;
    }
}

echo "\n";
echo "========================================\n";
echo "RÉSUMÉ\n";
echo "========================================\n";
echo "Pages créées: " . count($results['created']) . "\n";
echo "Pages existantes: " . count($results['exists']) . "\n";
echo "Erreurs: " . count($results['errors']) . "\n";
echo "\n";

if (!empty($results['created'])) {
    echo "Pages créées:\n";
    foreach ($results['created'] as $page) {
        echo "  • {$page}\n";
    }
    echo "\n";
}

if (!empty($results['exists'])) {
    echo "Pages existantes:\n";
    foreach ($results['exists'] as $page) {
        echo "  • {$page}\n";
    }
    echo "\n";
}

if (!empty($results['errors'])) {
    echo "Erreurs:\n";
    foreach ($results['errors'] as $page) {
        echo "  • {$page}\n";
    }
    echo "\n";
}

echo "</pre>\n";
echo "<p><strong>Terminé!</strong> Vous pouvez maintenant <a href='/wp-admin/edit.php?post_type=page'>voir toutes les pages</a>.</p>\n";
