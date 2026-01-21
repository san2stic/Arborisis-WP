<!DOCTYPE html>
<html <?php language_attributes(); ?> class="scroll-smooth">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="min-h-screen flex flex-col">

    <header id="site-header" class="site-header">
        <div class="container-custom">
            <nav class="flex items-center justify-between py-4">

                <!-- Logo -->
                <div class="flex items-center">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="flex items-center gap-3 no-underline">
                        <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-600 rounded-lg flex items-center justify-center shadow-lg shadow-primary-500/30">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                            </svg>
                        </div>
                        <span class="text-xl font-display font-bold text-dark-900 dark:text-dark-50">
                            <?php bloginfo('name'); ?>
                        </span>
                    </a>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center gap-2 nav-menu">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="nav-link <?php echo is_front_page() ? 'active' : ''; ?>">
                        Accueil
                    </a>
                    <a href="<?php echo esc_url(home_url('/explore')); ?>" class="nav-link <?php echo is_page('explore') ? 'active' : ''; ?>">
                        Explorer
                    </a>
                    <a href="<?php echo esc_url(home_url('/map')); ?>" class="nav-link <?php echo is_page('map') ? 'active' : ''; ?>">
                        Carte
                    </a>
                    <a href="<?php echo esc_url(home_url('/graph')); ?>" class="nav-link <?php echo is_page('graph') ? 'active' : ''; ?>">
                        Graphe
                    </a>
                    <a href="<?php echo esc_url(home_url('/stats')); ?>" class="nav-link <?php echo is_page('stats') ? 'active' : ''; ?>">
                        Stats
                    </a>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-3">

                    <!-- Search Button -->
                    <button id="search-toggle" class="btn-ghost btn-sm" aria-label="Rechercher">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>

                    <!-- Dark Mode Toggle -->
                    <button id="theme-toggle" class="btn-ghost btn-sm" aria-label="Basculer le thème">
                        <svg id="theme-icon-light" class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <svg id="theme-icon-dark" class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                    </button>

                    <?php if (is_user_logged_in()) : ?>
                        <!-- User Menu -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center gap-2 btn-ghost btn-sm">
                                <?php echo get_avatar(get_current_user_id(), 32, '', '', ['class' => 'rounded-full']); ?>
                                <span class="hidden lg:inline"><?php echo wp_get_current_user()->display_name; ?></span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white dark:bg-dark-800 rounded-lg shadow-lg border border-dark-200 dark:border-dark-700 py-2 z-50">
                                <a href="<?php echo esc_url(home_url('/profile/' . wp_get_current_user()->user_login)); ?>" class="block px-4 py-2 hover:bg-dark-100 dark:hover:bg-dark-700">
                                    Mon profil
                                </a>
                                <?php if (current_user_can('upload_sounds')) : ?>
                                    <a href="<?php echo esc_url(home_url('/upload')); ?>" class="block px-4 py-2 hover:bg-dark-100 dark:hover:bg-dark-700">
                                        Uploader un son
                                    </a>
                                <?php endif; ?>
                                <a href="<?php echo esc_url(home_url('/my-sounds')); ?>" class="block px-4 py-2 hover:bg-dark-100 dark:hover:bg-dark-700">
                                    Mes sons
                                </a>
                                <a href="<?php echo esc_url(home_url('/favorites')); ?>" class="block px-4 py-2 hover:bg-dark-100 dark:hover:bg-dark-700">
                                    Favoris
                                </a>
                                <hr class="my-2 border-dark-200 dark:border-dark-700">
                                <a href="<?php echo wp_logout_url(home_url('/')); ?>" class="block px-4 py-2 hover:bg-dark-100 dark:hover:bg-dark-700 text-red-600 dark:text-red-400">
                                    Déconnexion
                                </a>
                            </div>
                        </div>
                    <?php else : ?>
                        <!-- Login/Register -->
                        <a href="<?php echo wp_login_url(); ?>" class="btn-ghost btn-sm">
                            Connexion
                        </a>
                        <a href="<?php echo wp_registration_url(); ?>" class="btn-primary btn-sm">
                            Inscription
                        </a>
                    <?php endif; ?>

                    <!-- Mobile Menu Toggle -->
                    <button id="mobile-menu-toggle" class="md:hidden btn-ghost btn-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>

            </nav>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="md:hidden hidden border-t border-dark-200 dark:border-dark-700 py-4">
                <div class="flex flex-col gap-2">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="nav-link">Accueil</a>
                    <a href="<?php echo esc_url(home_url('/explore')); ?>" class="nav-link">Explorer</a>
                    <a href="<?php echo esc_url(home_url('/map')); ?>" class="nav-link">Carte</a>
                    <a href="<?php echo esc_url(home_url('/graph')); ?>" class="nav-link">Graphe</a>
                    <a href="<?php echo esc_url(home_url('/stats')); ?>" class="nav-link">Stats</a>
                </div>
            </div>

        </div>
    </header>

    <!-- Search Modal -->
    <div id="search-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-start justify-center pt-20">
        <div class="bg-white dark:bg-dark-800 rounded-xl shadow-2xl w-full max-w-2xl mx-4">
            <div class="p-4">
                <input
                    type="search"
                    id="global-search"
                    class="input"
                    placeholder="Rechercher des sons, tags, lieux..."
                    autocomplete="off"
                >
            </div>
            <div id="search-results" class="max-h-96 overflow-y-auto custom-scrollbar">
                <!-- Results populated by JS -->
            </div>
        </div>
    </div>

    <main id="main" class="flex-1">
