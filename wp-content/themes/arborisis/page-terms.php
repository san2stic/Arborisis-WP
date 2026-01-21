<?php
/**
 * Template Name: Terms of Service
 * Description: Conditions générales d'utilisation
 */

get_header();
?>

<div class="py-12 bg-white dark:bg-dark-900 min-h-screen">
    <div class="container-custom max-w-4xl">

        <header class="mb-12">
            <h1 class="text-5xl font-display font-bold text-dark-900 dark:text-dark-50 mb-4">
                Conditions Générales d'Utilisation
            </h1>
            <p class="text-lg text-dark-600 dark:text-dark-400">
                En vigueur depuis le <?php echo date('d/m/Y'); ?>
            </p>
        </header>

        <article class="prose prose-lg dark:prose-invert max-w-none">

            <h2>1. Acceptation des Conditions</h2>
            <p>
                En accédant et en utilisant Arborisis, vous acceptez d'être lié par ces conditions générales d'utilisation.
                Si vous n'acceptez pas ces conditions, veuillez ne pas utiliser la plateforme.
            </p>

            <h2>2. Description du Service</h2>
            <p>
                Arborisis est une plateforme collaborative de partage d'enregistrements sonores de terrain (field recordings).
                Le service permet aux utilisateurs de :
            </p>
            <ul>
                <li>Uploader et partager des enregistrements sonores</li>
                <li>Découvrir des sons du monde entier</li>
                <li>Interagir avec la communauté (likes, commentaires)</li>
                <li>Utiliser les sons selon leurs licences respectives</li>
            </ul>

            <h2>3. Création de Compte</h2>
            <h3>3.1 Éligibilité</h3>
            <p>
                Vous devez avoir au moins 16 ans pour créer un compte. Si vous avez entre 16 et 18 ans,
                vous devez avoir l'autorisation parentale.
            </p>

            <h3>3.2 Responsabilité du compte</h3>
            <ul>
                <li>Vous êtes responsable de la confidentialité de votre mot de passe</li>
                <li>Vous êtes responsable de toutes activités sous votre compte</li>
                <li>Vous devez notifier immédiatement toute utilisation non autorisée</li>
                <li>Un compte par personne (pas de multi-comptes)</li>
            </ul>

            <h2>4. Contenu Utilisateur</h2>

            <h3>4.1 Propriété intellectuelle</h3>
            <p>
                Vous conservez tous les droits sur les enregistrements que vous uploadez. En publiant sur Arborisis,
                vous accordez :
            </p>
            <ul>
                <li>Une licence mondiale, non-exclusive et gratuite à Arborisis pour héberger, distribuer et afficher votre contenu</li>
                <li>Une licence aux autres utilisateurs selon la licence Creative Commons que vous choisissez</li>
            </ul>

            <h3>4.2 Contenu autorisé</h3>
            <p><strong>Vous pouvez uploader :</strong></p>
            <ul>
                <li>Enregistrements sonores de terrain réalisés par vous</li>
                <li>Ambiances naturelles (oiseaux, insectes, eau, vent, etc.)</li>
                <li>Sons urbains (trafic, marchés, foules, etc.)</li>
                <li>Enregistrements culturels (musiques traditionnelles, cérémonies publiques)</li>
                <li>Sons industriels et machines</li>
            </ul>

            <h3>4.3 Contenu interdit</h3>
            <p><strong>Vous NE POUVEZ PAS uploader :</strong></p>
            <ul>
                <li>❌ Contenus protégés par droits d'auteur sans autorisation</li>
                <li>❌ Musique commerciale ou enregistrements studio</li>
                <li>❌ Contenus violents, haineux ou illégaux</li>
                <li>❌ Contenus pornographiques ou à caractère sexuel</li>
                <li>❌ Enregistrements privés sans consentement (conversations, etc.)</li>
                <li>❌ Spam, publicités ou contenus promotionnels</li>
                <li>❌ Malware ou fichiers corrompus</li>
            </ul>

            <h3>4.4 Modération</h3>
            <p>
                Arborisis se réserve le droit de :
            </p>
            <ul>
                <li>Supprimer tout contenu violant ces conditions</li>
                <li>Suspendre ou fermer les comptes en infraction</li>
                <li>Refuser la publication de contenu sans justification</li>
            </ul>

            <h2>5. Licences Creative Commons</h2>

            <p>Lors de l'upload, vous devez choisir une licence :</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 not-prose my-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="font-bold">CC0 - Domaine Public</h4>
                        <p class="text-sm">Aucune restriction, usage commercial autorisé</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="font-bold">CC-BY - Attribution</h4>
                        <p class="text-sm">Usage libre avec mention de l'auteur</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="font-bold">CC-BY-SA - Attribution Partage</h4>
                        <p class="text-sm">Attribution + même licence pour dérivés</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="font-bold">CC-BY-NC - Attribution Non Commercial</h4>
                        <p class="text-sm">Usage non commercial uniquement</p>
                    </div>
                </div>
            </div>

            <p>
                <strong>Important :</strong> Le choix de licence est définitif après publication.
                Pour plus d'informations, consultez notre <a href="<?php echo esc_url(home_url('/licenses')); ?>">page licences</a>.
            </p>

            <h2>6. Utilisation Acceptable</h2>

            <h3>Comportements interdits</h3>
            <ul>
                <li>Harcèlement, intimidation ou menaces envers d'autres utilisateurs</li>
                <li>Usurpation d'identité</li>
                <li>Manipulation des statistiques (fake plays/likes)</li>
                <li>Scraping automatisé sans autorisation</li>
                <li>Contournement des systèmes de sécurité</li>
                <li>Surcharge volontaire des serveurs (DoS)</li>
            </ul>

            <h2>7. Propriété Intellectuelle d'Arborisis</h2>

            <p>
                Le code, le design, le logo et les marques d'Arborisis sont protégés.
                Vous ne pouvez pas :
            </p>
            <ul>
                <li>Copier le design ou le code sans autorisation</li>
                <li>Utiliser les marques Arborisis sans licence</li>
                <li>Créer des services concurrents utilisant nos ressources</li>
            </ul>

            <h2>8. Limitation de Responsabilité</h2>

            <p>
                Arborisis est fourni "tel quel" sans garantie d'aucune sorte. Nous ne garantissons pas :
            </p>
            <ul>
                <li>La disponibilité ininterrompue du service</li>
                <li>L'absence d'erreurs ou de bugs</li>
                <li>La pérennité du stockage des fichiers</li>
                <li>L'exactitude des métadonnées fournies par les utilisateurs</li>
            </ul>

            <p>
                <strong>Nous ne sommes pas responsables :</strong>
            </p>
            <ul>
                <li>Des dommages directs ou indirects liés à l'utilisation du service</li>
                <li>De la perte de données due à des pannes techniques</li>
                <li>Du contenu uploadé par les utilisateurs</li>
                <li>Des litiges entre utilisateurs</li>
            </ul>

            <h2>9. Résiliation</h2>

            <h3>9.1 Par vous</h3>
            <p>
                Vous pouvez supprimer votre compte à tout moment via les <a href="<?php echo esc_url(home_url('/settings')); ?>">paramètres</a>.
                Vos enregistrements seront supprimés dans les 30 jours.
            </p>

            <h3>9.2 Par Arborisis</h3>
            <p>
                Nous pouvons suspendre ou fermer votre compte si :
            </p>
            <ul>
                <li>Vous violez ces conditions</li>
                <li>Votre compte est inactif depuis plus de 2 ans</li>
                <li>Nous sommes légalement obligés de le faire</li>
            </ul>

            <h2>10. Modifications des Conditions</h2>

            <p>
                Nous pouvons modifier ces conditions à tout moment. Les modifications importantes seront
                notifiées par email 30 jours avant leur entrée en vigueur. Continuer à utiliser le service
                après cette période constitue une acceptation des nouvelles conditions.
            </p>

            <h2>11. Loi Applicable</h2>

            <p>
                Ces conditions sont régies par le droit français. Tout litige sera soumis aux tribunaux
                compétents de Paris, France.
            </p>

            <h2>12. Contact</h2>

            <p>
                Pour toute question concernant ces conditions :
            </p>
            <ul>
                <li>Email : legal@arborisis.org</li>
                <li>Formulaire : <a href="<?php echo esc_url(home_url('/contact')); ?>">Page contact</a></li>
            </ul>

            <div class="bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800 rounded-lg p-6 my-8">
                <p class="font-bold mb-2">📜 Dernière révision</p>
                <p class="text-sm">
                    Ces conditions ont été mises à jour pour la dernière fois le <?php echo date('d/m/Y'); ?>.
                    Version 1.0
                </p>
            </div>

        </article>

    </div>
</div>

<?php get_footer(); ?>
