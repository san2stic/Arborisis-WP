<?php
/**
 * Template Name: Contact
 * Description: Formulaire de contact
 */

get_header();
?>

<div class="py-12 bg-white dark:bg-dark-900 min-h-screen">
    <div class="container-custom max-w-3xl">

        <!-- Page Header -->
        <header class="mb-12 text-center">
            <h1 class="text-5xl md:text-6xl font-display font-bold text-dark-900 dark:text-dark-50 mb-6">
                Contactez-Nous
            </h1>
            <p class="text-xl text-dark-600 dark:text-dark-400">
                Une question, une suggestion ou un problème ? Nous sommes là pour vous aider.
            </p>
        </header>

        <!-- Contact Form -->
        <div class="card">
            <div class="card-body">
                <form id="contact-form" class="space-y-6">

                    <!-- Name -->
                    <div>
                        <label for="contact-name" class="block text-sm font-medium text-dark-700 dark:text-dark-300 mb-2">
                            Nom complet <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="contact-name"
                            name="name"
                            required
                            class="input w-full"
                            placeholder="Votre nom"
                        />
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="contact-email" class="block text-sm font-medium text-dark-700 dark:text-dark-300 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="email"
                            id="contact-email"
                            name="email"
                            required
                            class="input w-full"
                            placeholder="votre@email.com"
                        />
                    </div>

                    <!-- Subject -->
                    <div>
                        <label for="contact-subject" class="block text-sm font-medium text-dark-700 dark:text-dark-300 mb-2">
                            Sujet <span class="text-red-500">*</span>
                        </label>
                        <select id="contact-subject" name="subject" required class="input w-full">
                            <option value="">Sélectionnez un sujet...</option>
                            <option value="question">Question générale</option>
                            <option value="bug">Signaler un bug</option>
                            <option value="feature">Suggestion de fonctionnalité</option>
                            <option value="copyright">Question sur les droits d'auteur</option>
                            <option value="moderation">Signaler un contenu</option>
                            <option value="account">Problème de compte</option>
                            <option value="other">Autre</option>
                        </select>
                    </div>

                    <!-- Message -->
                    <div>
                        <label for="contact-message" class="block text-sm font-medium text-dark-700 dark:text-dark-300 mb-2">
                            Message <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            id="contact-message"
                            name="message"
                            required
                            rows="8"
                            class="input w-full"
                            placeholder="Décrivez votre demande en détail..."
                        ></textarea>
                        <p class="text-xs text-dark-500 mt-2">
                            Minimum 20 caractères
                        </p>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button
                            type="submit"
                            id="submit-contact"
                            class="btn btn-primary w-full"
                        >
                            Envoyer le message
                        </button>
                    </div>

                    <!-- Status Messages -->
                    <div id="contact-status" class="hidden"></div>

                </form>
            </div>
        </div>

        <!-- Additional Contact Info -->
        <div class="mt-12 grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="card">
                <div class="card-body">
                    <h3 class="text-xl font-bold mb-4">Temps de réponse</h3>
                    <p class="text-dark-600 dark:text-dark-400">
                        Nous nous efforçons de répondre à tous les messages dans les 48 heures ouvrables.
                        Pour les questions urgentes concernant la modération, comptez 24 heures maximum.
                    </p>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h3 class="text-xl font-bold mb-4">Réseaux sociaux</h3>
                    <p class="text-dark-600 dark:text-dark-400 mb-4">
                        Suivez-nous pour rester informé des nouveautés et partager vos découvertes sonores.
                    </p>
                    <div class="flex gap-4">
                        <a href="#" class="text-primary-600 hover:text-primary-700" aria-label="Twitter">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"></path></svg>
                        </a>
                        <a href="#" class="text-primary-600 hover:text-primary-700" aria-label="Instagram">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
document.getElementById('contact-form').addEventListener('submit', async (e) => {
    e.preventDefault();

    const submitBtn = document.getElementById('submit-contact');
    const statusDiv = document.getElementById('contact-status');
    const form = e.target;

    // Validation
    const message = form.message.value.trim();
    if (message.length < 20) {
        statusDiv.className = 'p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg';
        statusDiv.innerHTML = '<p class="text-red-700 dark:text-red-400">Le message doit contenir au moins 20 caractères.</p>';
        statusDiv.classList.remove('hidden');
        return;
    }

    // Disable button
    submitBtn.disabled = true;
    submitBtn.innerHTML = 'Envoi en cours...';
    statusDiv.classList.add('hidden');

    try {
        const formData = {
            name: form.name.value,
            email: form.email.value,
            subject: form.subject.value,
            message: form.message.value
        };

        // Send to WordPress admin email (you can create a custom REST endpoint for this)
        const response = await fetch('/wp-json/arborisis/v1/contact', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        if (response.ok) {
            statusDiv.className = 'p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg';
            statusDiv.innerHTML = '<p class="text-green-700 dark:text-green-400"><strong>Message envoyé avec succès!</strong> Nous vous répondrons dans les meilleurs délais.</p>';
            form.reset();
        } else {
            throw new Error('Erreur serveur');
        }
    } catch (error) {
        console.error('Contact form error:', error);
        statusDiv.className = 'p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg';
        statusDiv.innerHTML = '<p class="text-red-700 dark:text-red-400">Une erreur est survenue. Veuillez réessayer ou nous contacter à contact@arborisis.org</p>';
    }

    statusDiv.classList.remove('hidden');
    submitBtn.disabled = false;
    submitBtn.innerHTML = 'Envoyer le message';
});
</script>

<?php get_footer(); ?>
