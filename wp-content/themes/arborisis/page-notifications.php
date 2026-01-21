<?php
/**
 * Template Name: Notifications
 * Description: Centre de notifications utilisateur
 */

if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(home_url('/notifications')));
    exit;
}

get_header();
?>

<div class="py-8 bg-white dark:bg-dark-900 min-h-screen">
    <div class="container-custom max-w-4xl">

        <div class="flex justify-between items-center mb-8">
            <h1 class="text-4xl font-display font-bold text-dark-900 dark:text-dark-50">
                Notifications
            </h1>
            <button id="mark-all-read" class="btn btn-sm btn-secondary">
                Tout marquer comme lu
            </button>
        </div>

        <!-- Filters -->
        <div class="flex gap-4 mb-6">
            <button class="filter-btn active" data-filter="all">Toutes</button>
            <button class="filter-btn" data-filter="likes">Likes</button>
            <button class="filter-btn" data-filter="comments">Commentaires</button>
            <button class="filter-btn" data-filter="follows">Abonnements</button>
        </div>

        <!-- Notifications List -->
        <div id="notifications-list" class="space-y-4">
            <!-- Demo notifications - Dans un vrai projet, ces données viendraient de la BDD -->
            <div class="notification-item unread" data-type="like">
                <div class="card">
                    <div class="card-body flex items-start gap-4">
                        <div class="w-12 h-12 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center text-2xl">
                            ❤️
                        </div>
                        <div class="flex-1">
                            <p class="mb-1">
                                <strong>Marie Dubois</strong> a liké votre enregistrement
                                <a href="#" class="text-primary-600 hover:underline">"Chant de rossignol"</a>
                            </p>
                            <p class="text-sm text-dark-500">Il y a 2 heures</p>
                        </div>
                        <div class="w-3 h-3 rounded-full bg-primary-600"></div>
                    </div>
                </div>
            </div>

            <div class="notification-item" data-type="comment">
                <div class="card">
                    <div class="card-body flex items-start gap-4">
                        <div class="w-12 h-12 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center text-2xl">
                            💬
                        </div>
                        <div class="flex-1">
                            <p class="mb-1">
                                <strong>Pierre Martin</strong> a commenté
                                <a href="#" class="text-primary-600 hover:underline">"Ambiance de marché"</a>
                            </p>
                            <p class="text-sm text-dark-600 dark:text-dark-400 italic mb-2">
                                "Superbe enregistrement ! Quelle ville ?"
                            </p>
                            <p class="text-sm text-dark-500">Il y a 5 heures</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="notification-item" data-type="follow">
                <div class="card">
                    <div class="card-body flex items-start gap-4">
                        <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center text-2xl">
                            👤
                        </div>
                        <div class="flex-1">
                            <p class="mb-1">
                                <strong>Sophie Leroy</strong> s'est abonné(e) à votre profil
                            </p>
                            <p class="text-sm text-dark-500">Il y a 1 jour</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="notification-item" data-type="like">
                <div class="card">
                    <div class="card-body flex items-start gap-4">
                        <div class="w-12 h-12 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center text-2xl">
                            ❤️
                        </div>
                        <div class="flex-1">
                            <p class="mb-1">
                                <strong>3 personnes</strong> ont liké votre enregistrement
                                <a href="#" class="text-primary-600 hover:underline">"Pluie en forêt"</a>
                            </p>
                            <p class="text-sm text-dark-500">Il y a 2 jours</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty state -->
            <div id="empty-state" class="hidden text-center py-12">
                <p class="text-dark-500 text-lg mb-4">Aucune notification pour le moment</p>
                <a href="<?php echo home_url('/explore'); ?>" class="btn btn-primary">
                    Explorer les sons
                </a>
            </div>
        </div>

        <!-- Load More -->
        <div class="text-center mt-8">
            <button id="load-more" class="btn btn-secondary">
                Charger plus
            </button>
        </div>

    </div>
</div>

<script>
// Filters
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const filter = btn.dataset.filter;

        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        document.querySelectorAll('.notification-item').forEach(item => {
            if (filter === 'all' || item.dataset.type === filter) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        });
    });
});

// Mark all as read
document.getElementById('mark-all-read').addEventListener('click', () => {
    document.querySelectorAll('.notification-item.unread').forEach(item => {
        item.classList.remove('unread');
        item.querySelector('.bg-primary-600')?.remove();
    });
});

// Load more (demo)
document.getElementById('load-more').addEventListener('click', () => {
    alert('Fonctionnalité à implémenter : charger plus de notifications depuis la BDD');
});
</script>

<style>
.filter-btn {
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-weight: 500;
    color: #6b7280;
    background: transparent;
}
.filter-btn.active {
    background: #6366f1;
    color: white;
}
.notification-item.unread {
    position: relative;
}
</style>

<?php get_footer(); ?>
