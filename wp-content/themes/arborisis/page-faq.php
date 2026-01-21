<?php
/**
 * Template Name: FAQ
 * Description: Questions fréquentes
 */

get_header();
?>

<div class="py-12 bg-white dark:bg-dark-900 min-h-screen">
    <div class="container-custom max-w-4xl">

        <header class="mb-12 text-center">
            <h1 class="text-5xl font-display font-bold text-dark-900 dark:text-dark-50 mb-4">
                Questions Fréquentes
            </h1>
            <p class="text-xl text-dark-600 dark:text-dark-400">
                Tout ce que vous devez savoir sur Arborisis
            </p>
        </header>

        <div class="space-y-4">

            <!-- General -->
            <details class="card group">
                <summary class="card-body cursor-pointer flex justify-between items-center">
                    <h3 class="text-xl font-bold">Qu'est-ce qu'Arborisis ?</h3>
                    <span class="text-2xl group-open:rotate-180 transition-transform">▼</span>
                </summary>
                <div class="px-6 pb-6 prose dark:prose-invert">
                    <p>
                        Arborisis est une plateforme collaborative de partage d'enregistrements sonores de terrain
                        (field recordings). Nous permettons aux passionnés, chercheurs et artistes sonores de documenter
                        et partager les paysages sonores du monde entier.
                    </p>
                </div>
            </details>

            <details class="card group">
                <summary class="card-body cursor-pointer flex justify-between items-center">
                    <h3 class="text-xl font-bold">Est-ce gratuit ?</h3>
                    <span class="text-2xl group-open:rotate-180 transition-transform">▼</span>
                </summary>
                <div class="px-6 pb-6 prose dark:prose-invert">
                    <p>
                        <strong>Oui, totalement gratuit.</strong> La création de compte, l'upload et le téléchargement
                        de sons sont entièrement gratuits. Arborisis est un projet open source financé par des dons
                        et sans publicité.
                    </p>
                </div>
            </details>

            <!-- Upload -->
            <details class="card group">
                <summary class="card-body cursor-pointer flex justify-between items-center">
                    <h3 class="text-xl font-bold">Comment uploader un enregistrement ?</h3>
                    <span class="text-2xl group-open:rotate-180 transition-transform">▼</span>
                </summary>
                <div class="px-6 pb-6 prose dark:prose-invert">
                    <ol>
                        <li>Créez un compte (ou connectez-vous)</li>
                        <li>Allez sur la page <a href="<?php echo home_url('/upload'); ?>">Upload</a></li>
                        <li>Glissez-déposez votre fichier audio (MP3, WAV, FLAC, OGG)</li>
                        <li>Remplissez les métadonnées (titre, description, tags)</li>
                        <li>Choisissez une licence Creative Commons</li>
                        <li>Publiez !</li>
                    </ol>
                </div>
            </details>

            <details class="card group">
                <summary class="card-body cursor-pointer flex justify-between items-center">
                    <h3 class="text-xl font-bold">Quelle est la taille maximale des fichiers ?</h3>
                    <span class="text-2xl group-open:rotate-180 transition-transform">▼</span>
                </summary>
                <div class="px-6 pb-6 prose dark:prose-invert">
                    <p>
                        <strong>200 MB par fichier.</strong> Pour les enregistrements longs (>10 minutes), nous recommandons
                        le format MP3 320kbps ou FLAC pour un bon compromis qualité/taille.
                    </p>
                </div>
            </details>

            <details class="card group">
                <summary class="card-body cursor-pointer flex justify-between items-center">
                    <h3 class="text-xl font-bold">Quels formats audio sont acceptés ?</h3>
                    <span class="text-2xl group-open:rotate-180 transition-transform">▼</span>
                </summary>
                <div class="px-6 pb-6 prose dark:prose-invert">
                    <ul>
                        <li><strong>MP3</strong> : Recommandé 320kbps minimum</li>
                        <li><strong>WAV</strong> : 16-bit ou 24-bit</li>
                        <li><strong>FLAC</strong> : Compression sans perte</li>
                        <li><strong>OGG</strong> : Format ouvert</li>
                    </ul>
                    <p>Qualité minimale : 16-bit / 44.1 kHz (CD quality)</p>
                </div>
            </details>

            <!-- Licenses -->
            <details class="card group">
                <summary class="card-body cursor-pointer flex justify-between items-center">
                    <h3 class="text-xl font-bold">Quelle licence choisir ?</h3>
                    <span class="text-2xl group-open:rotate-180 transition-transform">▼</span>
                </summary>
                <div class="px-6 pb-6 prose dark:prose-invert">
                    <ul>
                        <li><strong>CC0</strong> : Maximum d'impact, aucune restriction</li>
                        <li><strong>CC-BY</strong> : Recommandé - usage libre avec crédit</li>
                        <li><strong>CC-BY-SA</strong> : Les dérivés gardent la même licence</li>
                        <li><strong>CC-BY-NC</strong> : Pas d'usage commercial</li>
                    </ul>
                    <p>
                        Voir notre <a href="<?php echo home_url('/licenses'); ?>">guide des licences</a> pour plus de détails.
                    </p>
                </div>
            </details>

            <details class="card group">
                <summary class="card-body cursor-pointer flex justify-between items-center">
                    <h3 class="text-xl font-bold">Puis-je changer la licence après publication ?</h3>
                    <span class="text-2xl group-open:rotate-180 transition-transform">▼</span>
                </summary>
                <div class="px-6 pb-6 prose dark:prose-invert">
                    <p>
                        <strong>Non.</strong> Une fois publiée, la licence est définitive. Vous ne pouvez pas révoquer
                        les droits déjà accordés aux utilisateurs qui ont téléchargé votre son.
                    </p>
                </div>
            </details>

            <!-- Usage -->
            <details class="card group">
                <summary class="card-body cursor-pointer flex justify-between items-center">
                    <h3 class="text-xl font-bold">Comment télécharger un son ?</h3>
                    <span class="text-2xl group-open:rotate-180 transition-transform">▼</span>
                </summary>
                <div class="px-6 pb-6 prose dark:prose-invert">
                    <p>
                        Sur la page d'un enregistrement, cliquez sur le bouton "Télécharger". Le fichier original
                        sera téléchargé dans le format uploadé par l'auteur. Assurez-vous de respecter la licence !
                    </p>
                </div>
            </details>

            <details class="card group">
                <summary class="card-body cursor-pointer flex justify-between items-center">
                    <h3 class="text-xl font-bold">Puis-je utiliser les sons dans mes projets ?</h3>
                    <span class="text-2xl group-open:rotate-180 transition-transform">▼</span>
                </summary>
                <div class="px-6 pb-6 prose dark:prose-invert">
                    <p>
                        <strong>Oui</strong>, selon la licence choisie par l'auteur :
                    </p>
                    <ul>
                        <li><strong>CC0 / CC-BY / CC-BY-SA</strong> : Usage commercial autorisé</li>
                        <li><strong>CC-BY-NC</strong> : Uniquement usage non commercial</li>
                    </ul>
                    <p>
                        Pour CC-BY et variantes, vous devez créditer l'auteur. Consultez chaque page de son
                        pour voir la licence exacte.
                    </p>
                </div>
            </details>

            <!-- Account -->
            <details class="card group">
                <summary class="card-body cursor-pointer flex justify-between items-center">
                    <h3 class="text-xl font-bold">Comment supprimer mon compte ?</h3>
                    <span class="text-2xl group-open:rotate-180 transition-transform">▼</span>
                </summary>
                <div class="px-6 pb-6 prose dark:prose-invert">
                    <p>
                        Allez dans vos <a href="<?php echo home_url('/settings'); ?>">Paramètres</a>, section "Compte",
                        et cliquez sur "Supprimer mon compte". Vos enregistrements seront supprimés sous 30 jours.
                    </p>
                    <p class="text-red-600">
                        ⚠️ <strong>Attention :</strong> Cette action est irréversible !
                    </p>
                </div>
            </details>

            <!-- Technical -->
            <details class="card group">
                <summary class="card-body cursor-pointer flex justify-between items-center">
                    <h3 class="text-xl font-bold">Où sont stockés les fichiers audio ?</h3>
                    <span class="text-2xl group-open:rotate-180 transition-transform">▼</span>
                </summary>
                <div class="px-6 pb-6 prose dark:prose-invert">
                    <p>
                        Les fichiers sont stockés sur un stockage S3-compatible (AWS S3 ou MinIO) avec chiffrement.
                        Les serveurs sont situés dans l'Union Européenne pour conformité RGPD.
                    </p>
                </div>
            </details>

            <details class="card group">
                <summary class="card-body cursor-pointer flex justify-between items-center">
                    <h3 class="text-xl font-bold">Y a-t-il une API pour développeurs ?</h3>
                    <span class="text-2xl group-open:rotate-180 transition-transform">▼</span>
                </summary>
                <div class="px-6 pb-6 prose dark:prose-invert">
                    <p>
                        <strong>Oui !</strong> Arborisis propose une API REST complète pour accéder aux sons,
                        statistiques et métadonnées. Consultez notre <a href="<?php echo home_url('/api-docs'); ?>">documentation API</a>.
                    </p>
                </div>
            </details>

            <!-- Community -->
            <details class="card group">
                <summary class="card-body cursor-pointer flex justify-between items-center">
                    <h3 class="text-xl font-bold">Comment signaler un contenu inapproprié ?</h3>
                    <span class="text-2xl group-open:rotate-180 transition-transform">▼</span>
                </summary>
                <div class="px-6 pb-6 prose dark:prose-invert">
                    <p>
                        Sur la page du son, cliquez sur le bouton "⋯" puis "Signaler". Précisez la raison
                        (copyright, contenu offensant, spam, etc.). Notre équipe examine tous les signalements
                        sous 48 heures.
                    </p>
                </div>
            </details>

            <details class="card group">
                <summary class="card-body cursor-pointer flex justify-between items-center">
                    <h3 class="text-xl font-bold">Puis-je modifier ou supprimer mes sons après publication ?</h3>
                    <span class="text-2xl group-open:rotate-180 transition-transform">▼</span>
                </summary>
                <div class="px-6 pb-6 prose dark:prose-invert">
                    <p>
                        <strong>Oui.</strong> Allez sur la page <a href="<?php echo home_url('/my-sounds'); ?>">Mes Sons</a>,
                        puis cliquez sur "Éditer" ou "Supprimer" pour le son concerné. Vous pouvez modifier les métadonnées
                        (titre, description, tags) mais pas le fichier audio ni la licence.
                    </p>
                </div>
            </details>

        </div>

        <!-- Contact CTA -->
        <div class="card mt-12">
            <div class="card-body text-center">
                <h3 class="text-2xl font-bold mb-4">Vous ne trouvez pas votre réponse ?</h3>
                <p class="text-dark-600 dark:text-dark-400 mb-6">
                    Notre équipe est là pour vous aider
                </p>
                <a href="<?php echo home_url('/contact'); ?>" class="btn btn-primary">
                    Nous Contacter
                </a>
            </div>
        </div>

    </div>
</div>

<style>
details summary {
    list-style: none;
}
details summary::-webkit-details-marker {
    display: none;
}
</style>

<?php get_footer(); ?>
