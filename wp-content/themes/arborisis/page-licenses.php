<?php
/**
 * Template Name: Licenses
 * Description: Guide des licences Creative Commons
 */

get_header();
?>

<div class="py-12 bg-white dark:bg-dark-900 min-h-screen">
    <div class="container-custom max-w-4xl">

        <header class="mb-12 text-center">
            <h1 class="text-5xl font-display font-bold text-dark-900 dark:text-dark-50 mb-4">
                Guide des Licences
            </h1>
            <p class="text-xl text-dark-600 dark:text-dark-400">
                Comprendre les licences Creative Commons sur Arborisis
            </p>
        </header>

        <article class="prose prose-lg dark:prose-invert max-w-none">

            <p class="lead">
                Les licences Creative Commons permettent aux créateurs de partager leurs œuvres tout en
                conservant certains droits. Sur Arborisis, tous les enregistrements sont publiés sous l'une
                de ces licences ouvertes.
            </p>

            <h2>Licences Disponibles</h2>

            <!-- CC0 -->
            <div class="bg-white dark:bg-dark-800 border border-dark-200 dark:border-dark-700 rounded-lg p-6 my-8 not-prose">
                <div class="flex items-start gap-4">
                    <div class="text-4xl">🌍</div>
                    <div class="flex-1">
                        <h3 class="text-2xl font-bold mb-2">CC0 - Domaine Public</h3>
                        <p class="text-dark-600 dark:text-dark-400 mb-4">
                            <strong>La plus permissive</strong> - Renonciation totale aux droits
                        </p>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <h4 class="font-bold text-green-600 mb-2">✅ Autorisé</h4>
                                <ul class="text-sm space-y-1">
                                    <li>Usage commercial</li>
                                    <li>Modification libre</li>
                                    <li>Redistribution</li>
                                    <li>Aucune attribution requise</li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="font-bold text-red-600 mb-2">❌ Interdit</h4>
                                <ul class="text-sm space-y-1">
                                    <li><em>Aucune restriction</em></li>
                                </ul>
                            </div>
                        </div>

                        <div class="bg-dark-50 dark:bg-dark-900 p-4 rounded">
                            <p class="text-sm"><strong>Idéal pour :</strong> Maximiser l'impact et l'utilisation, projets scientifiques, éducation</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CC-BY -->
            <div class="bg-white dark:bg-dark-800 border border-dark-200 dark:border-dark-700 rounded-lg p-6 my-8 not-prose">
                <div class="flex items-start gap-4">
                    <div class="text-4xl">👤</div>
                    <div class="flex-1">
                        <h3 class="text-2xl font-bold mb-2">CC-BY - Attribution</h3>
                        <p class="text-dark-600 dark:text-dark-400 mb-4">
                            <strong>Très permissive</strong> - Usage libre avec crédit
                        </p>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <h4 class="font-bold text-green-600 mb-2">✅ Autorisé</h4>
                                <ul class="text-sm space-y-1">
                                    <li>Usage commercial</li>
                                    <li>Modification libre</li>
                                    <li>Redistribution</li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="font-bold text-orange-600 mb-2">⚠️ Obligation</h4>
                                <ul class="text-sm space-y-1">
                                    <li>Attribution de l'auteur</li>
                                    <li>Lien vers la licence</li>
                                    <li>Mention des modifications</li>
                                </ul>
                            </div>
                        </div>

                        <div class="bg-dark-50 dark:bg-dark-900 p-4 rounded">
                            <p class="text-sm"><strong>Idéal pour :</strong> Reconnaissance de votre travail tout en permettant une large diffusion</p>
                            <p class="text-xs mt-2">
                                <strong>Exemple d'attribution :</strong><br>
                                "Chant de rossignol" par Jean Dupont (Arborisis) - CC-BY 4.0
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CC-BY-SA -->
            <div class="bg-white dark:bg-dark-800 border border-dark-200 dark:border-dark-700 rounded-lg p-6 my-8 not-prose">
                <div class="flex items-start gap-4">
                    <div class="text-4xl">🔄</div>
                    <div class="flex-1">
                        <h3 class="text-2xl font-bold mb-2">CC-BY-SA - Attribution - Partage dans les Mêmes Conditions</h3>
                        <p class="text-dark-600 dark:text-dark-400 mb-4">
                            <strong>Permissive avec réciprocité</strong> - Les œuvres dérivées doivent garder la même licence
                        </p>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <h4 class="font-bold text-green-600 mb-2">✅ Autorisé</h4>
                                <ul class="text-sm space-y-1">
                                    <li>Usage commercial</li>
                                    <li>Modification</li>
                                    <li>Redistribution</li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="font-bold text-orange-600 mb-2">⚠️ Obligations</h4>
                                <ul class="text-sm space-y-1">
                                    <li>Attribution de l'auteur</li>
                                    <li>Même licence pour dérivés</li>
                                    <li>Lien vers la licence</li>
                                </ul>
                            </div>
                        </div>

                        <div class="bg-dark-50 dark:bg-dark-900 p-4 rounded">
                            <p class="text-sm"><strong>Idéal pour :</strong> Garantir que votre travail reste libre (philosophie open source)</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CC-BY-NC -->
            <div class="bg-white dark:bg-dark-800 border border-dark-200 dark:border-dark-700 rounded-lg p-6 my-8 not-prose">
                <div class="flex items-start gap-4">
                    <div class="text-4xl">🚫💰</div>
                    <div class="flex-1">
                        <h3 class="text-2xl font-bold mb-2">CC-BY-NC - Attribution - Pas d'Utilisation Commerciale</h3>
                        <p class="text-dark-600 dark:text-dark-400 mb-4">
                            <strong>Restrictive sur le commercial</strong> - Usage non commercial uniquement
                        </p>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <h4 class="font-bold text-green-600 mb-2">✅ Autorisé</h4>
                                <ul class="text-sm space-y-1">
                                    <li>Usage personnel</li>
                                    <li>Usage éducatif</li>
                                    <li>Usage artistique non lucratif</li>
                                    <li>Modification</li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="font-bold text-red-600 mb-2">❌ Interdit</h4>
                                <ul class="text-sm space-y-1">
                                    <li>Usage commercial</li>
                                    <li>Vente</li>
                                    <li>Publicité</li>
                                    <li>Production commerciale</li>
                                </ul>
                            </div>
                        </div>

                        <div class="bg-dark-50 dark:bg-dark-900 p-4 rounded">
                            <p class="text-sm"><strong>Idéal pour :</strong> Partager tout en gardant le contrôle commercial</p>
                            <p class="text-xs mt-2 text-orange-600">
                                ⚠️ Limite la diffusion (beaucoup de projets refusent les licences NC)
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <h2>Tableau Comparatif</h2>

            <table class="w-full">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>CC0</th>
                        <th>CC-BY</th>
                        <th>CC-BY-SA</th>
                        <th>CC-BY-NC</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Télécharger</td>
                        <td>✅</td>
                        <td>✅</td>
                        <td>✅</td>
                        <td>✅</td>
                    </tr>
                    <tr>
                        <td>Modifier</td>
                        <td>✅</td>
                        <td>✅</td>
                        <td>✅</td>
                        <td>✅</td>
                    </tr>
                    <tr>
                        <td>Usage commercial</td>
                        <td>✅</td>
                        <td>✅</td>
                        <td>✅</td>
                        <td>❌</td>
                    </tr>
                    <tr>
                        <td>Attribution requise</td>
                        <td>❌</td>
                        <td>✅</td>
                        <td>✅</td>
                        <td>✅</td>
                    </tr>
                    <tr>
                        <td>Même licence pour dérivés</td>
                        <td>❌</td>
                        <td>❌</td>
                        <td>✅</td>
                        <td>❌</td>
                    </tr>
                </tbody>
            </table>

            <h2>Questions Fréquentes</h2>

            <h3>Puis-je changer de licence après publication ?</h3>
            <p>
                <strong>Non.</strong> Une fois qu'un enregistrement est publié sous une licence, celle-ci est définitive.
                Vous ne pouvez pas révoquer les droits déjà accordés. Choisissez avec soin !
            </p>

            <h3>Qu'est-ce qu'un usage "commercial" ?</h3>
            <p>
                Un usage est considéré comme commercial si :
            </p>
            <ul>
                <li>Il génère directement un revenu (vente, location)</li>
                <li>Il est utilisé dans de la publicité</li>
                <li>Il est intégré dans un produit commercial</li>
            </ul>
            <p>
                <strong>Ne sont généralement PAS commerciaux :</strong> usage éducatif, documentaires non lucratifs,
                projets artistiques personnels, recherche académique.
            </p>

            <h3>Comment créditer correctement un auteur (CC-BY) ?</h3>
            <p>
                Format recommandé :
            </p>
            <pre class="bg-dark-100 dark:bg-dark-800 p-4 rounded text-sm overflow-x-auto">
"[Titre du son]" par [Nom de l'auteur]
Source : Arborisis ([lien vers le son])
Licence : CC-BY 4.0 (https://creativecommons.org/licenses/by/4.0/)</pre>

            <h3>Puis-je utiliser CC-BY dans un projet sous copyright ?</h3>
            <p>
                <strong>Oui.</strong> Tant que vous créditez l'auteur, vous pouvez intégrer un son CC-BY dans une œuvre
                protégée par copyright (film, podcast commercial, etc.).
            </p>

            <h3>Quelle licence choisir ?</h3>

            <div class="bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800 rounded-lg p-6 my-6 not-prose">
                <ul class="space-y-2">
                    <li>
                        <strong>CC0 :</strong> Si vous souhaitez un impact maximal sans restriction
                    </li>
                    <li>
                        <strong>CC-BY :</strong> Si vous voulez être crédité (recommandé pour la plupart)
                    </li>
                    <li>
                        <strong>CC-BY-SA :</strong> Si vous souhaitez que les dérivés restent libres
                    </li>
                    <li>
                        <strong>CC-BY-NC :</strong> Si vous voulez éviter l'usage commercial
                    </li>
                </ul>
            </div>

            <h2>Ressources Externes</h2>

            <ul>
                <li><a href="https://creativecommons.org/licenses/" target="_blank" rel="noopener">Creative Commons - Site officiel</a></li>
                <li><a href="https://chooser-beta.creativecommons.org/" target="_blank" rel="noopener">Outil de choix de licence CC</a></li>
                <li><a href="https://wiki.creativecommons.org/wiki/Best_practices_for_attribution" target="_blank" rel="noopener">Bonnes pratiques d'attribution</a></li>
            </ul>

            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6 my-8 not-prose">
                <h3 class="text-xl font-bold mb-3">💡 Besoin d'aide ?</h3>
                <p class="mb-4">
                    Des questions sur le choix de licence pour votre enregistrement ?
                </p>
                <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btn btn-primary">
                    Contactez-nous
                </a>
            </div>

        </article>

    </div>
</div>

<?php get_footer(); ?>
