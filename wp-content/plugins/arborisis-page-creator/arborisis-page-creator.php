<?php
/**
 * Plugin Name: Arborisis Page Creator
 * Description: Crée automatiquement toutes les pages nécessaires pour le thème Arborisis
 * Version: 1.0.0
 * Author: Arborisis
 */

// Empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

// Ajouter un élément au menu admin
add_action('admin_menu', 'arborisis_page_creator_menu');

function arborisis_page_creator_menu() {
    add_management_page(
        'Créer les pages Arborisis',
        'Créer les pages',
        'manage_options',
        'arborisis-page-creator',
        'arborisis_page_creator_page'
    );
}

function arborisis_page_creator_page() {
    // Vérifier les permissions
    if (!current_user_can('manage_options')) {
        wp_die('Vous n\'avez pas les permissions nécessaires.');
    }

    // Traiter la création si le formulaire est soumis
    $results = null;
    if (isset($_POST['create_pages']) && check_admin_referer('arborisis_create_pages')) {
        $results = arborisis_create_all_pages();
    }

    ?>
    <div class="wrap">
        <h1>Créer les pages Arborisis</h1>

        <?php if ($results): ?>
            <div class="notice notice-success is-dismissible">
                <p><strong>Pages créées avec succès!</strong></p>
                <ul>
                    <li>✅ Créées: <?php echo count($results['created']); ?></li>
                    <li>⚠️ Existantes: <?php echo count($results['exists']); ?></li>
                    <li>❌ Erreurs: <?php echo count($results['errors']); ?></li>
                </ul>
            </div>

            <?php if (!empty($results['created'])): ?>
                <h2>Pages créées</h2>
                <table class="wp-list-table widefat striped">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>URL</th>
                            <th>Template</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results['created'] as $page): ?>
                            <tr>
                                <td><?php echo esc_html($page['title']); ?></td>
                                <td><a href="<?php echo esc_url($page['url']); ?>" target="_blank"><?php echo esc_html($page['url']); ?></a></td>
                                <td><?php echo esc_html($page['template']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <?php if (!empty($results['exists'])): ?>
                <h2>Pages déjà existantes</h2>
                <table class="wp-list-table widefat striped">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>URL</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results['exists'] as $page): ?>
                            <tr>
                                <td><?php echo esc_html($page['title']); ?></td>
                                <td><a href="<?php echo esc_url($page['url']); ?>" target="_blank"><?php echo esc_html($page['url']); ?></a></td>
                                <td><a href="<?php echo admin_url('post.php?post=' . $page['id'] . '&action=edit'); ?>">Modifier</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        <?php endif; ?>

        <div class="card" style="max-width: 800px; margin-top: 20px;">
            <h2>À propos</h2>
            <p>Ce plugin va créer automatiquement toutes les pages nécessaires pour le thème Arborisis avec leurs templates associés.</p>

            <h3>Pages qui seront créées :</h3>
            <ul style="columns: 2;">
                <li>Explorer</li>
                <li>Carte</li>
                <li>Graphe</li>
                <li>Statistiques</li>
                <li>À propos</li>
                <li>Contact</li>
                <li>FAQ</li>
                <li>Règles de la communauté</li>
                <li>Licences</li>
                <li>Confidentialité</li>
                <li>Conditions d'utilisation</li>
                <li>Documentation API</li>
                <li>Mon profil</li>
                <li>Mes sons</li>
                <li>Favoris</li>
                <li>Notifications</li>
                <li>Paramètres</li>
                <li>Uploader un son</li>
            </ul>

            <form method="post" action="">
                <?php wp_nonce_field('arborisis_create_pages'); ?>
                <p>
                    <button type="submit" name="create_pages" class="button button-primary button-hero">
                        🚀 Créer toutes les pages
                    </button>
                </p>
            </form>

            <p style="color: #666; font-size: 12px;">
                Note : Les pages existantes ne seront pas modifiées. Seules les pages manquantes seront créées.
            </p>
        </div>
    </div>
    <?php
}

function arborisis_create_all_pages() {
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

    foreach ($pages as $page_data) {
        $page_title = $page_data['post_title'];
        $page_slug = $page_data['post_name'];

        // Vérifier si la page existe déjà
        $existing_page = get_page_by_path($page_slug);

        if ($existing_page) {
            $results['exists'][] = [
                'title' => $page_title,
                'url' => get_permalink($existing_page->ID),
                'id' => $existing_page->ID
            ];

            // Mettre à jour le template si nécessaire
            $current_template = get_post_meta($existing_page->ID, '_wp_page_template', true);
            if ($current_template !== $page_data['template']) {
                update_post_meta($existing_page->ID, '_wp_page_template', $page_data['template']);
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
            $results['errors'][] = [
                'title' => $page_title,
                'error' => $page_id->get_error_message()
            ];
        } else {
            // Assigner le template
            update_post_meta($page_id, '_wp_page_template', $page_data['template']);

            $results['created'][] = [
                'title' => $page_title,
                'url' => get_permalink($page_id),
                'template' => $page_data['template']
            ];
        }
    }

    return $results;
}
