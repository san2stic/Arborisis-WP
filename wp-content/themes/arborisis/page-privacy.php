<?php
/**
 * Template Name: Privacy Policy
 * Description: Politique de confidentialité RGPD
 */

get_header();
?>

<div class="py-12 bg-white dark:bg-dark-900 min-h-screen">
    <div class="container-custom max-w-4xl">

        <header class="mb-12">
            <h1 class="text-5xl font-display font-bold text-dark-900 dark:text-dark-50 mb-4">
                Politique de Confidentialité
            </h1>
            <p class="text-lg text-dark-600 dark:text-dark-400">
                Dernière mise à jour : <?php echo date('d/m/Y'); ?>
            </p>
        </header>

        <article class="prose prose-lg dark:prose-invert max-w-none">

            <h2>1. Introduction</h2>
            <p>
                Arborisis respecte votre vie privée et s'engage à protéger vos données personnelles.
                Cette politique explique comment nous collectons, utilisons et protégeons vos informations
                conformément au Règlement Général sur la Protection des Données (RGPD).
            </p>

            <h2>2. Données Collectées</h2>

            <h3>2.1 Données de compte</h3>
            <ul>
                <li>Nom d'utilisateur et nom d'affichage</li>
                <li>Adresse email</li>
                <li>Mot de passe (chiffré)</li>
                <li>Informations de profil (bio, site web, réseaux sociaux) - optionnel</li>
            </ul>

            <h3>2.2 Données d'utilisation</h3>
            <ul>
                <li>Adresse IP (hashée pour anonymisation)</li>
                <li>User-agent navigateur (hashé)</li>
                <li>Statistiques d'écoute et interactions (likes, plays)</li>
                <li>Dates et heures des actions</li>
            </ul>

            <h3>2.3 Données d'enregistrements sonores</h3>
            <ul>
                <li>Fichiers audio uploadés</li>
                <li>Métadonnées : titre, description, tags</li>
                <li>Données de géolocalisation (si fournies volontairement)</li>
                <li>Informations d'équipement (optionnel)</li>
            </ul>

            <h2>3. Utilisation des Données</h2>

            <p>Nous utilisons vos données pour :</p>
            <ul>
                <li><strong>Fournir le service</strong> : gestion de compte, hébergement des enregistrements</li>
                <li><strong>Améliorer l'expérience</strong> : recommandations, statistiques</li>
                <li><strong>Communication</strong> : notifications importantes, réponses à vos demandes</li>
                <li><strong>Sécurité</strong> : détection des abus, prévention de spam</li>
                <li><strong>Conformité légale</strong> : respect des obligations légales</li>
            </ul>

            <h2>4. Base Légale (RGPD)</h2>

            <table class="w-full">
                <thead>
                    <tr>
                        <th>Donnée</th>
                        <th>Base légale</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Compte utilisateur</td>
                        <td>Exécution du contrat</td>
                    </tr>
                    <tr>
                        <td>Statistiques d'usage</td>
                        <td>Intérêt légitime</td>
                    </tr>
                    <tr>
                        <td>Communication marketing</td>
                        <td>Consentement (opt-in)</td>
                    </tr>
                    <tr>
                        <td>Géolocalisation</td>
                        <td>Consentement explicite</td>
                    </tr>
                </tbody>
            </table>

            <h2>5. Partage des Données</h2>

            <p><strong>Nous ne vendons jamais vos données.</strong></p>

            <p>Vos données peuvent être partagées avec :</p>
            <ul>
                <li><strong>Fournisseurs de services</strong> : hébergement (AWS S3/MinIO), infrastructure</li>
                <li><strong>Utilisateurs de la plateforme</strong> : données publiques de profil et enregistrements</li>
                <li><strong>Autorités</strong> : en cas d'obligation légale uniquement</li>
            </ul>

            <h2>6. Stockage et Sécurité</h2>

            <h3>Localisation</h3>
            <p>
                Les données sont hébergées dans l'Union Européenne (serveurs conformes RGPD).
                Les fichiers audio sont stockés sur S3-compatible storage avec chiffrement.
            </p>

            <h3>Mesures de sécurité</h3>
            <ul>
                <li>Chiffrement SSL/TLS pour toutes les connexions</li>
                <li>Mots de passe hashés avec bcrypt</li>
                <li>Anonymisation des adresses IP dans les statistiques</li>
                <li>Sauvegardes quotidiennes chiffrées</li>
                <li>Accès restreint aux données sensibles</li>
            </ul>

            <h2>7. Vos Droits (RGPD)</h2>

            <p>Vous disposez des droits suivants :</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 not-prose my-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="font-bold mb-2">✅ Droit d'accès</h4>
                        <p class="text-sm">Demander une copie de vos données</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="font-bold mb-2">✏️ Droit de rectification</h4>
                        <p class="text-sm">Corriger vos informations</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="font-bold mb-2">🗑️ Droit à l'effacement</h4>
                        <p class="text-sm">Supprimer votre compte et données</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="font-bold mb-2">📦 Droit à la portabilité</h4>
                        <p class="text-sm">Exporter vos données</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="font-bold mb-2">⛔ Droit d'opposition</h4>
                        <p class="text-sm">Refuser certains traitements</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="font-bold mb-2">⏸️ Droit de limitation</h4>
                        <p class="text-sm">Restreindre le traitement</p>
                    </div>
                </div>
            </div>

            <p>
                Pour exercer vos droits, contactez-nous via notre
                <a href="<?php echo esc_url(home_url('/contact')); ?>">formulaire de contact</a>
                ou à l'adresse : privacy@arborisis.org
            </p>

            <h2>8. Cookies</h2>

            <p>Nous utilisons les cookies suivants :</p>

            <h3>Cookies essentiels (obligatoires)</h3>
            <ul>
                <li><code>wordpress_logged_in</code> : session utilisateur</li>
                <li><code>PHPSESSID</code> : session PHP</li>
            </ul>

            <h3>Cookies fonctionnels (avec consentement)</h3>
            <ul>
                <li><code>arborisis_theme</code> : préférence thème clair/sombre</li>
                <li><code>arborisis_volume</code> : réglage volume lecteur</li>
            </ul>

            <p>
                Vous pouvez gérer vos préférences cookies dans les paramètres de votre navigateur.
            </p>

            <h2>9. Mineurs</h2>

            <p>
                Arborisis est accessible aux utilisateurs de 16 ans et plus. Si vous avez moins de 16 ans,
                vous devez obtenir l'autorisation parentale avant de créer un compte.
            </p>

            <h2>10. Modifications</h2>

            <p>
                Nous pouvons mettre à jour cette politique. Les modifications importantes seront notifiées
                par email et affichées sur le site 30 jours avant leur entrée en vigueur.
            </p>

            <h2>11. Contact</h2>

            <p>
                <strong>Responsable du traitement :</strong> Arborisis<br>
                <strong>Email :</strong> privacy@arborisis.org<br>
                <strong>Délégué à la protection des données :</strong> dpo@arborisis.org
            </p>

            <p>
                <strong>Autorité de contrôle :</strong> CNIL (France) -
                <a href="https://www.cnil.fr" target="_blank" rel="noopener">www.cnil.fr</a>
            </p>

        </article>

    </div>
</div>

<?php get_footer(); ?>
