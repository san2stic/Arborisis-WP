<?php
/**
 * Template Name: Settings
 * Description: Paramètres du compte utilisateur
 */

if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(home_url('/settings')));
    exit;
}

get_header();

$current_user = wp_get_current_user();
$bio = get_user_meta($current_user->ID, '_arb_bio', true);
$website = get_user_meta($current_user->ID, '_arb_website', true);
$twitter = get_user_meta($current_user->ID, '_arb_twitter', true);
$instagram = get_user_meta($current_user->ID, '_arb_instagram', true);
?>

<div class="py-12 bg-white dark:bg-dark-900 min-h-screen">
    <div class="container-custom max-w-4xl">

        <h1 class="text-4xl font-display font-bold text-dark-900 dark:text-dark-50 mb-8">
            Paramètres du Compte
        </h1>

        <!-- Tabs -->
        <div class="border-b border-dark-200 dark:border-dark-700 mb-8">
            <nav class="flex gap-4">
                <button class="tab-btn active" data-tab="profile">Profil</button>
                <button class="tab-btn" data-tab="account">Compte</button>
                <button class="tab-btn" data-tab="notifications">Notifications</button>
                <button class="tab-btn" data-tab="privacy">Confidentialité</button>
            </nav>
        </div>

        <!-- Profile Tab -->
        <div id="tab-profile" class="tab-content">
            <div class="card">
                <div class="card-body">
                    <h2 class="text-2xl font-bold mb-6">Informations de Profil</h2>

                    <form id="profile-form" class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium mb-2">Nom d'affichage</label>
                            <input type="text" name="display_name" value="<?php echo esc_attr($current_user->display_name); ?>" class="input w-full" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">Biographie</label>
                            <textarea name="bio" rows="4" class="input w-full"><?php echo esc_textarea($bio); ?></textarea>
                            <p class="text-xs text-dark-500 mt-1">Présentez-vous en quelques lignes</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">Site web</label>
                            <input type="url" name="website" value="<?php echo esc_attr($website); ?>" class="input w-full" placeholder="https://example.com" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-2">Twitter</label>
                                <input type="text" name="twitter" value="<?php echo esc_attr($twitter); ?>" class="input w-full" placeholder="@username" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Instagram</label>
                                <input type="text" name="instagram" value="<?php echo esc_attr($instagram); ?>" class="input w-full" placeholder="@username" />
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Account Tab -->
        <div id="tab-account" class="tab-content hidden">
            <div class="card">
                <div class="card-body">
                    <h2 class="text-2xl font-bold mb-6">Paramètres du Compte</h2>

                    <div class="space-y-8">
                        <div>
                            <h3 class="text-lg font-bold mb-4">Email</h3>
                            <p class="text-dark-600 dark:text-dark-400 mb-4"><?php echo esc_html($current_user->user_email); ?></p>
                            <button class="btn btn-secondary btn-sm">Changer l'email</button>
                        </div>

                        <div class="border-t border-dark-200 dark:border-dark-700 pt-8">
                            <h3 class="text-lg font-bold mb-4">Mot de passe</h3>
                            <button class="btn btn-secondary btn-sm">Changer le mot de passe</button>
                        </div>

                        <div class="border-t border-dark-200 dark:border-dark-700 pt-8">
                            <h3 class="text-lg font-bold mb-4 text-red-600">Zone de Danger</h3>
                            <p class="text-dark-600 dark:text-dark-400 mb-4">
                                La suppression de votre compte est définitive et supprimera tous vos enregistrements.
                            </p>
                            <button onclick="deleteAccount()" class="btn bg-red-600 hover:bg-red-700 text-white">
                                Supprimer mon compte
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifications Tab -->
        <div id="tab-notifications" class="tab-content hidden">
            <div class="card">
                <div class="card-body">
                    <h2 class="text-2xl font-bold mb-6">Préférences de Notifications</h2>

                    <form class="space-y-4">
                        <label class="flex items-center gap-3 p-4 border border-dark-200 dark:border-dark-700 rounded-lg cursor-pointer hover:bg-dark-50 dark:hover:bg-dark-800">
                            <input type="checkbox" checked class="w-5 h-5" />
                            <div>
                                <div class="font-medium">Nouveaux likes</div>
                                <div class="text-sm text-dark-600 dark:text-dark-400">Recevoir une notification quand quelqu'un like votre son</div>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-4 border border-dark-200 dark:border-dark-700 rounded-lg cursor-pointer hover:bg-dark-50 dark:hover:bg-dark-800">
                            <input type="checkbox" checked class="w-5 h-5" />
                            <div>
                                <div class="font-medium">Nouveaux commentaires</div>
                                <div class="text-sm text-dark-600 dark:text-dark-400">Être notifié des commentaires sur vos enregistrements</div>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-4 border border-dark-200 dark:border-dark-700 rounded-lg cursor-pointer hover:bg-dark-50 dark:hover:bg-dark-800">
                            <input type="checkbox" class="w-5 h-5" />
                            <div>
                                <div class="font-medium">Newsletter</div>
                                <div class="text-sm text-dark-600 dark:text-dark-400">Recevoir les actualités et nouveautés d'Arborisis</div>
                            </div>
                        </label>

                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Privacy Tab -->
        <div id="tab-privacy" class="tab-content hidden">
            <div class="card">
                <div class="card-body">
                    <h2 class="text-2xl font-bold mb-6">Confidentialité</h2>

                    <div class="space-y-6">
                        <div>
                            <h3 class="font-bold mb-4">Visibilité du profil</h3>
                            <label class="flex items-center gap-3 p-4 border border-dark-200 dark:border-dark-700 rounded-lg cursor-pointer">
                                <input type="radio" name="profile_visibility" value="public" checked />
                                <div>
                                    <div class="font-medium">Public</div>
                                    <div class="text-sm text-dark-600 dark:text-dark-400">Votre profil est visible par tous</div>
                                </div>
                            </label>
                        </div>

                        <div>
                            <h3 class="font-bold mb-4">Données personnelles</h3>
                            <button class="btn btn-secondary btn-sm">Télécharger mes données (RGPD)</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
// Tabs
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const tab = btn.dataset.tab;

        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));

        btn.classList.add('active');
        document.getElementById(`tab-${tab}`).classList.remove('hidden');
    });
});

// Profile form
document.getElementById('profile-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);

    try {
        const response = await fetch('/wp-json/arborisis/v1/user/profile', {
            method: 'POST',
            headers: {
                'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>'
            },
            body: formData
        });

        if (response.ok) {
            alert('Profil mis à jour !');
        }
    } catch (error) {
        console.error('Update error:', error);
        alert('Erreur lors de la mise à jour.');
    }
});

function deleteAccount() {
    if (confirm('Êtes-vous absolument sûr ? Cette action est IRRÉVERSIBLE.')) {
        if (confirm('Tous vos enregistrements seront supprimés. Confirmer la suppression ?')) {
            alert('Contactez-nous via le formulaire de contact pour supprimer votre compte.');
        }
    }
}
</script>

<style>
.tab-btn {
    padding: 0.75rem 1.5rem;
    border-bottom: 2px solid transparent;
    font-weight: 500;
    color: #6b7280;
}
.tab-btn.active {
    color: #6366f1;
    border-bottom-color: #6366f1;
}
</style>

<?php get_footer(); ?>
