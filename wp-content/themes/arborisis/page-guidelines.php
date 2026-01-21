<?php
/**
 * Template Name: Community Guidelines
 * Description: Règles de la communauté
 */

get_header();
?>

<div class="py-12 bg-white dark:bg-dark-900 min-h-screen">
    <div class="container-custom max-w-4xl">

        <header class="mb-12 text-center">
            <h1 class="text-5xl font-display font-bold text-dark-900 dark:text-dark-50 mb-4">
                Guide de Contribution
            </h1>
            <p class="text-xl text-dark-600 dark:text-dark-400">
                Bonnes pratiques pour partager des enregistrements de qualité
            </p>
        </header>

        <article class="prose prose-lg dark:prose-invert max-w-none">

            <div class="bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800 rounded-lg p-6 my-8 not-prose">
                <h3 class="text-xl font-bold mb-3">🎯 Philosophie d'Arborisis</h3>
                <p>
                    Nous privilégions les <strong>enregistrements de terrain authentiques</strong> capturés
                    dans leur contexte naturel. L'objectif est de documenter les paysages sonores du monde
                    avec qualité et respect.
                </p>
            </div>

            <h2>1. Types d'Enregistrements Acceptés</h2>

            <h3>✅ Enregistrements souhaités</h3>
            <ul>
                <li><strong>Nature</strong> : oiseaux, insectes, mammifères, amphibiens, environnements naturels</li>
                <li><strong>Géophonie</strong> : vent, pluie, tonnerre, eau (rivières, océan, cascade)</li>
                <li><strong>Ambiances urbaines</strong> : rues, marchés, gares, ports, espaces publics</li>
                <li><strong>Sons culturels</strong> : musiques traditionnelles jouées dans l'espace public, cérémonies</li>
                <li><strong>Industriel</strong> : machines, usines, chantiers (avec autorisation)</li>
                <li><strong>Transport</strong> : trains, bateaux, avions, circulation</li>
            </ul>

            <h3>❌ Contenus non acceptés</h3>
            <ul>
                <li>Musique studio ou enregistrements commerciaux</li>
                <li>Synthèses sonores ou créations artificielles</li>
                <li>Effets sonores de banques de son</li>
                <li>Enregistrements privés sans consentement</li>
                <li>Contenus violents, offensants ou illégaux</li>
            </ul>

            <h2>2. Qualité Technique</h2>

            <h3>Format audio</h3>
            <ul>
                <li><strong>Formats acceptés</strong> : MP3, WAV, FLAC, OGG</li>
                <li><strong>Qualité minimale</strong> : 16-bit / 44.1 kHz (CD quality)</li>
                <li><strong>Qualité recommandée</strong> : 24-bit / 48 kHz ou plus</li>
                <li><strong>Taille maximale</strong> : 200 MB par fichier</li>
            </ul>

            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 my-6 not-prose">
                <p class="text-sm">
                    <strong>💡 Astuce :</strong> Pour les enregistrements de plus de 10 minutes, privilégiez
                    MP3 320kbps ou FLAC pour un bon compromis qualité/taille.
                </p>
            </div>

            <h3>Qualité sonore</h3>
            <ul>
                <li>Éviter la surmodulation (clipping)</li>
                <li>Minimiser le bruit de manipulation du micro</li>
                <li>Privilégier les enregistrements en stéréo quand possible</li>
                <li>Pas de normalisation excessive (préserver la dynamique naturelle)</li>
            </ul>

            <h2>3. Métadonnées Essentielles</h2>

            <h3>Titre</h3>
            <ul>
                <li>Descriptif et précis (ex: "Chant de rossignol philomèle à l'aube")</li>
                <li>Éviter les titres génériques ("Audio 001", "Recording")</li>
                <li>Mentionner l'espèce si identifiable</li>
            </ul>

            <h3>Description</h3>
            <p>Une bonne description inclut :</p>
            <ul>
                <li><strong>Contexte</strong> : où, quand, pourquoi</li>
                <li><strong>Détails</strong> : heure, météo, saison</li>
                <li><strong>Espèces/sons</strong> : identification précise si possible</li>
                <li><strong>Équipement</strong> : micro, enregistreur (optionnel mais apprécié)</li>
            </ul>

            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6 my-6 not-prose">
                <h4 class="font-bold mb-3">📝 Exemple de bonne description</h4>
                <p class="text-sm italic">
                    "Enregistré à 6h30 le 15 mai 2024 dans la forêt de Fontainebleau (Seine-et-Marne, France).
                    Chœur d'aube avec rossignol philomèle dominant, accompagné de merle noir, fauvette à tête noire
                    et pinson des arbres. Météo claire, température 12°C. Équipement : Zoom H5 + microphones Rode NT5."
                </p>
            </div>

            <h3>Géolocalisation</h3>
            <ul>
                <li><strong>Précision recommandée</strong> : commune ou lieu-dit</li>
                <li><strong>Espèces rares</strong> : flouter la position exacte pour protection</li>
                <li><strong>Lieux privés</strong> : ne pas divulguer l'adresse exacte</li>
            </ul>

            <h3>Tags</h3>
            <ul>
                <li>3 à 10 tags pertinents</li>
                <li>Inclure : type d'environnement, espèce, saison</li>
                <li>Utiliser les tags existants quand possible (autocomplétion)</li>
                <li>Exemples : <code>forêt</code>, <code>oiseaux</code>, <code>printemps</code>, <code>aube</code></li>
            </ul>

            <h2>4. Licences Creative Commons</h2>

            <p>Choisissez la licence adaptée à vos souhaits :</p>

            <table class="w-full">
                <thead>
                    <tr>
                        <th>Licence</th>
                        <th>Usage commercial</th>
                        <th>Modifications</th>
                        <th>Attribution</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>CC0</td>
                        <td>✅ Oui</td>
                        <td>✅ Oui</td>
                        <td>❌ Non requise</td>
                    </tr>
                    <tr>
                        <td>CC-BY</td>
                        <td>✅ Oui</td>
                        <td>✅ Oui</td>
                        <td>✅ Requise</td>
                    </tr>
                    <tr>
                        <td>CC-BY-SA</td>
                        <td>✅ Oui</td>
                        <td>✅ Oui (même licence)</td>
                        <td>✅ Requise</td>
                    </tr>
                    <tr>
                        <td>CC-BY-NC</td>
                        <td>❌ Non</td>
                        <td>✅ Oui</td>
                        <td>✅ Requise</td>
                    </tr>
                </tbody>
            </table>

            <p>
                ⚠️ <strong>Important :</strong> La licence est définitive après publication. Choisissez avec soin !
            </p>

            <h2>5. Respect et Éthique</h2>

            <h3>Respect des espèces</h3>
            <ul>
                <li>Ne jamais stresser ou déranger les animaux pour obtenir un enregistrement</li>
                <li>Respecter les distances de sécurité</li>
                <li>Éviter les périodes sensibles (nidification, mise-bas)</li>
                <li>Ne pas utiliser de playback près des nids</li>
            </ul>

            <h3>Respect des personnes</h3>
            <ul>
                <li>Obtenir le consentement pour les conversations privées</li>
                <li>Respecter les interdictions de photographier/enregistrer</li>
                <li>Être discret et non intrusif en milieu urbain</li>
                <li>Respecter les lieux de culte et cérémonies</li>
            </ul>

            <h3>Respect des propriétés</h3>
            <ul>
                <li>Demander l'autorisation pour enregistrer sur propriété privée</li>
                <li>Respecter les zones interdites (militaires, industrielles)</li>
                <li>Se renseigner sur les règles des parcs nationaux</li>
            </ul>

            <h2>6. Interaction Communautaire</h2>

            <h3>Commentaires</h3>
            <ul>
                <li>Soyez constructif et respectueux</li>
                <li>Partagez vos connaissances (identification d'espèces, contexte)</li>
                <li>Posez des questions pertinentes</li>
                <li>Évitez les critiques destructrices</li>
            </ul>

            <h3>Crédits et attributions</h3>
            <ul>
                <li>Toujours créditer l'auteur original lors de réutilisation</li>
                <li>Format recommandé : "Titre" par Auteur (Arborisis) - Licence CC-XX</li>
                <li>Inclure un lien vers la page du son si possible</li>
            </ul>

            <h2>7. Signalement</h2>

            <p>Si vous constatez un contenu problématique :</p>
            <ul>
                <li>Utilisez le bouton "Signaler" sur la page du son</li>
                <li>Précisez la nature du problème</li>
                <li>Notre équipe examinera sous 48h</li>
            </ul>

            <p>
                Contenus signalables : violation de droits d'auteur, contenu offensant, spam,
                informations fausses, atteinte à la vie privée.
            </p>

            <h2>8. Ressources Utiles</h2>

            <ul>
                <li><a href="<?php echo home_url('/licenses'); ?>">Guide des licences Creative Commons</a></li>
                <li><a href="<?php echo home_url('/faq'); ?>">FAQ - Questions fréquentes</a></li>
                <li><a href="<?php echo home_url('/contact'); ?>">Contacter l'équipe de modération</a></li>
                <li><a href="<?php echo home_url('/api-docs'); ?>">Documentation API pour développeurs</a></li>
            </ul>

            <div class="bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800 rounded-lg p-6 my-8 not-prose">
                <h3 class="text-xl font-bold mb-3">🚀 Prêt à contribuer ?</h3>
                <p class="mb-4">
                    Vous avez des enregistrements de qualité à partager ? Rejoignez la communauté !
                </p>
                <a href="<?php echo esc_url(home_url('/upload')); ?>" class="btn btn-primary">
                    Uploader un enregistrement
                </a>
            </div>

        </article>

    </div>
</div>

<?php get_footer(); ?>
