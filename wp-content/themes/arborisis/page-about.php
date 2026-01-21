<?php
/**
 * Template Name: About
 * Description: À propos de la plateforme Arborisis
 */

get_header();
?>

<div class="py-12 bg-white dark:bg-dark-900 min-h-screen">
    <div class="container-custom max-w-4xl">

        <!-- Page Header -->
        <header class="mb-12 text-center">
            <h1 class="text-5xl md:text-6xl font-display font-bold text-dark-900 dark:text-dark-50 mb-6">
                À Propos d'Arborisis
            </h1>
            <p class="text-xl text-dark-600 dark:text-dark-400">
                Une bibliothèque collaborative de paysages sonores du monde entier
            </p>
        </header>

        <!-- Mission Section -->
        <article class="prose prose-lg dark:prose-invert max-w-none mb-16">
            <h2>Notre Mission</h2>
            <p>
                Arborisis est née d'une conviction profonde : les sons qui nous entourent racontent des histoires
                uniques sur nos environnements, nos cultures et notre époque. Nous créons une archive sonore mondiale
                accessible à tous, préservant la diversité acoustique de notre planète pour les générations futures.
            </p>
            <p>
                Que ce soit le chant d'un oiseau rare dans une forêt tropicale, l'ambiance d'un marché traditionnel,
                ou le bourdonnement d'une rue urbaine, chaque enregistrement contribue à documenter la richesse
                sonore de notre monde en constante évolution.
            </p>

            <h2>Une Communauté Mondiale</h2>
            <p>
                Arborisis rassemble des field recorders, des artistes sonores, des chercheurs et des passionnés
                du monde entier. Notre plateforme facilite le partage, la découverte et la collaboration autour
                du patrimoine sonore mondial.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 not-prose my-8">
                <div class="card text-center">
                    <div class="card-body">
                        <div class="text-4xl font-bold text-primary-600 dark:text-primary-400 mb-2">
                            <?php
                            $sound_count = wp_count_posts('sound');
                            echo number_format_i18n($sound_count->publish);
                            ?>
                        </div>
                        <div class="text-sm text-dark-600 dark:text-dark-400">
                            Enregistrements partagés
                        </div>
                    </div>
                </div>

                <div class="card text-center">
                    <div class="card-body">
                        <div class="text-4xl font-bold text-primary-600 dark:text-primary-400 mb-2">
                            <?php
                            $user_count = count_users();
                            echo number_format_i18n($user_count['total_users']);
                            ?>
                        </div>
                        <div class="text-sm text-dark-600 dark:text-dark-400">
                            Contributeurs actifs
                        </div>
                    </div>
                </div>

                <div class="card text-center">
                    <div class="card-body">
                        <div class="text-4xl font-bold text-primary-600 dark:text-primary-400 mb-2">
                            <?php
                            global $wpdb;
                            $countries = $wpdb->get_var("SELECT COUNT(DISTINCT pm.meta_value)
                                FROM {$wpdb->postmeta} pm
                                WHERE pm.meta_key = '_arb_location_name'
                                AND pm.meta_value != ''");
                            echo number_format_i18n($countries ?: 0);
                            ?>
                        </div>
                        <div class="text-sm text-dark-600 dark:text-dark-400">
                            Pays représentés
                        </div>
                    </div>
                </div>
            </div>

            <h2>Nos Valeurs</h2>

            <h3>🌍 Open Source & Open Data</h3>
            <p>
                Nous croyons que les connaissances sonores doivent être accessibles. Tous les enregistrements
                sont publiés sous licences Creative Commons, permettant leur réutilisation dans des projets
                éducatifs, artistiques et scientifiques.
            </p>

            <h3>🎧 Qualité & Authenticité</h3>
            <p>
                Nous privilégions des enregistrements de terrain authentiques, capturés dans leur contexte naturel.
                Chaque son est accompagné de métadonnées détaillées : localisation GPS, équipement utilisé,
                date d'enregistrement et description contextuelle.
            </p>

            <h3>🤝 Collaboration & Respect</h3>
            <p>
                Notre communauté valorise le respect mutuel, le partage de connaissances et la reconnaissance
                du travail de chacun. Nous encourageons les échanges constructifs et célébrons la diversité
                des approches de l'enregistrement sonore.
            </p>

            <h2>Technologie</h2>
            <p>
                Arborisis utilise des technologies modernes pour offrir une expérience optimale :
            </p>
            <ul>
                <li><strong>Stockage S3</strong> : Hébergement fiable et évolutif des fichiers audio</li>
                <li><strong>Recherche avancée</strong> : Indexation OpenSearch pour des résultats pertinents</li>
                <li><strong>Visualisation interactive</strong> : Cartes géospatiales et graphes de similarité</li>
                <li><strong>API REST</strong> : Accès programmatique aux données pour chercheurs et développeurs</li>
            </ul>

            <h2>Rejoignez-Nous</h2>
            <p>
                Que vous soyez un field recorder expérimenté ou simplement curieux d'explorer les sons du monde,
                vous êtes le bienvenu sur Arborisis.
            </p>
            <p>
                <a href="<?php echo esc_url(wp_registration_url()); ?>" class="btn btn-primary">
                    Créer un compte gratuit
                </a>
                <a href="<?php echo esc_url(home_url('/guidelines')); ?>" class="btn btn-secondary ml-4">
                    Lire le guide de contribution
                </a>
            </p>

            <h2>Contact</h2>
            <p>
                Des questions ? Des suggestions ? N'hésitez pas à nous contacter via notre
                <a href="<?php echo esc_url(home_url('/contact')); ?>">formulaire de contact</a>.
            </p>
        </article>

    </div>
</div>

<?php get_footer(); ?>
