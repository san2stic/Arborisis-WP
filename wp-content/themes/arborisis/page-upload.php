<?php
/**
 * Template Name: Upload
 * Description: Upload new sound recording
 */

// Check if user is logged in and has upload permissions
if (!is_user_logged_in() || !current_user_can('upload_sounds')) {
    wp_redirect(wp_login_url(get_permalink()));
    exit;
}

get_header();
?>

<div class="py-8 bg-white dark:bg-dark-900 min-h-screen">
    <div class="container-custom max-w-4xl">

        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-4xl md:text-5xl font-display font-bold text-dark-900 dark:text-dark-50 mb-4">
                Uploader un Enregistrement
            </h1>
            <p class="text-lg text-dark-600 dark:text-dark-400">
                Partagez vos field recordings avec la communauté. Formats acceptés : MP3, WAV, FLAC, OGG (max 200 MB).
            </p>
        </div>

        <!-- Upload Form -->
        <form id="upload-form" class="space-y-6">

            <!-- File Upload -->
            <div class="card">
                <div class="card-body">
                    <h2 class="text-xl font-display font-bold mb-4">📁 Fichier Audio</h2>

                    <div id="drop-zone"
                        class="border-2 border-dashed border-dark-300 dark:border-dark-600 rounded-xl p-12 text-center cursor-pointer hover:border-primary-500 hover:bg-primary-50 dark:hover:bg-primary-900/10 transition-all">
                        <svg class="w-16 h-16 mx-auto mb-4 text-dark-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        <p class="text-lg font-medium mb-2">Glissez-déposez votre fichier ici</p>
                        <p class="text-sm text-dark-500 mb-4">ou cliquez pour sélectionner</p>
                        <input type="file" id="audio-file" accept="audio/mpeg,audio/wav,audio/flac,audio/ogg,audio/mp4"
                            class="hidden">
                        <button type="button" onclick="document.getElementById('audio-file').click()"
                            class="btn btn-primary">
                            Choisir un fichier
                        </button>
                    </div>

                    <!-- File Info (shown after selection) -->
                    <div id="file-info" class="hidden mt-4 p-4 bg-dark-50 dark:bg-dark-800 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <svg class="w-8 h-8 text-primary-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                </svg>
                                <div>
                                    <div id="file-name" class="font-medium"></div>
                                    <div id="file-size" class="text-sm text-dark-500"></div>
                                </div>
                            </div>
                            <button type="button" id="remove-file" class="btn btn-ghost btn-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Upload Progress -->
                        <div id="upload-progress" class="hidden mt-4">
                            <div class="flex items-center justify-between text-sm mb-2">
                                <span>Upload en cours...</span>
                                <span id="progress-percent">0%</span>
                            </div>
                            <div class="w-full h-2 bg-dark-200 dark:bg-dark-700 rounded-full overflow-hidden">
                                <div id="progress-bar" class="h-full bg-primary-600 transition-all duration-300"
                                    style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Basic Info -->
            <div class="card">
                <div class="card-body">
                    <h2 class="text-xl font-display font-bold mb-4">📝 Informations de Base</h2>

                    <div class="space-y-4">
                        <div>
                            <label for="title" class="block text-sm font-medium mb-2">Titre *</label>
                            <input type="text" id="title" name="title" required class="input"
                                placeholder="Ex: Forêt au petit matin">
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium mb-2">Description</label>
                            <textarea id="description" name="description" rows="4" class="input"
                                placeholder="Décrivez votre enregistrement, le contexte, les sons capturés..."></textarea>
                        </div>

                        <div>
                            <label for="tags" class="block text-sm font-medium mb-2">Tags</label>
                            <input type="text" id="tags" name="tags" class="input"
                                placeholder="nature, oiseaux, forêt (séparés par des virgules)">
                            <p class="text-xs text-dark-500 mt-1">Ajoutez des mots-clés pour faciliter la recherche</p>
                        </div>

                        <div>
                            <label for="license" class="block text-sm font-medium mb-2">Licence *</label>
                            <select id="license" name="license" required class="input">
                                <option value="">Sélectionnez une licence</option>
                                <option value="cc0">CC0 (Domaine Public)</option>
                                <option value="cc-by-4">CC BY 4.0</option>
                                <option value="cc-by-sa-4">CC BY-SA 4.0</option>
                                <option value="cc-by-nc-4">CC BY-NC 4.0</option>
                                <option value="all-rights-reserved">Tous droits réservés</option>
                            </select>
                            <p class="text-xs text-dark-500 mt-1">
                                <a href="https://creativecommons.org/licenses/" target="_blank"
                                    class="hover:text-primary-600">En savoir plus sur les licences Creative Commons</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Location -->
            <div class="card">
                <div class="card-body">
                    <h2 class="text-xl font-display font-bold mb-4">📍 Géolocalisation</h2>

                    <div class="space-y-4">
                        <div>
                            <label for="location-name" class="block text-sm font-medium mb-2">Nom du lieu</label>
                            <input type="text" id="location-name" name="location_name" class="input"
                                placeholder="Ex: Forêt de Fontainebleau, France">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="latitude" class="block text-sm font-medium mb-2">Latitude</label>
                                <input type="number" id="latitude" name="latitude" step="0.000001" class="input"
                                    placeholder="48.404722">
                            </div>
                            <div>
                                <label for="longitude" class="block text-sm font-medium mb-2">Longitude</label>
                                <input type="number" id="longitude" name="longitude" step="0.000001" class="input"
                                    placeholder="2.707778">
                            </div>
                        </div>

                        <button type="button" id="use-current-location" class="btn btn-ghost btn-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            </svg>
                            Utiliser ma position actuelle
                        </button>
                    </div>
                </div>
            </div>

            <!-- Recording Details -->
            <div class="card">
                <div class="card-body">
                    <h2 class="text-xl font-display font-bold mb-4">🎙️ Détails d'Enregistrement</h2>

                    <div class="space-y-4">
                        <div>
                            <label for="recorded-at" class="block text-sm font-medium mb-2">Date
                                d'enregistrement</label>
                            <input type="date" id="recorded-at" name="recorded_at" class="input">
                        </div>

                        <div>
                            <label for="equipment" class="block text-sm font-medium mb-2">Équipement utilisé</label>
                            <input type="text" id="equipment" name="equipment" class="input"
                                placeholder="Ex: Zoom H5, Rode NTG-2">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex items-center justify-between pt-4">
                <button type="button" onclick="window.history.back()" class="btn btn-ghost">
                    Annuler
                </button>
                <button type="submit" id="submit-btn" class="btn btn-primary btn-lg" disabled>
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    Publier l'Enregistrement
                </button>
            </div>

        </form>

    </div>
</div>

<script>
    let selectedFile = null;
    let uploadedS3Key = null;

    // File selection
    const fileInput = document.getElementById('audio-file');
    const dropZone = document.getElementById('drop-zone');
    const fileInfo = document.getElementById('file-info');
    const submitBtn = document.getElementById('submit-btn');

    fileInput.addEventListener('change', (e) => {
        handleFileSelect(e.target.files[0]);
    });

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/10');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/10');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/10');
        handleFileSelect(e.dataTransfer.files[0]);
    });

    document.getElementById('remove-file').addEventListener('click', () => {
        selectedFile = null;
        uploadedS3Key = null;
        fileInput.value = '';
        fileInfo.classList.add('hidden');
        submitBtn.disabled = true;
    });

    function handleFileSelect(file) {
        if (!file) return;

        // Validate file type
        const allowedTypes = ['audio/mpeg', 'audio/wav', 'audio/flac', 'audio/ogg', 'audio/mp4'];
        if (!allowedTypes.includes(file.type)) {
            alert('Type de fichier non supporté. Utilisez MP3, WAV, FLAC ou OGG.');
            return;
        }

        // Validate file size (200 MB max)
        const maxSize = 200 * 1024 * 1024;
        if (file.size > maxSize) {
            alert('Fichier trop volumineux. Taille maximale : 200 MB.');
            return;
        }

        selectedFile = file;

        // Show file info
        document.getElementById('file-name').textContent = file.name;
        document.getElementById('file-size').textContent = formatFileSize(file.size);
        fileInfo.classList.remove('hidden');

        // Auto-fill title from filename
        if (!document.getElementById('title').value) {
            const title = file.name.replace(/\.[^/.]+$/, '').replace(/[-_]/g, ' ');
            document.getElementById('title').value = title;
        }

        // Enable submit
        submitBtn.disabled = false;

        // Start upload to S3
        uploadToS3(file);
    }

    async function uploadToS3(file) {
        try {
            // Get presigned URL
            const presignResponse = await fetch('/wp-json/arborisis/v1/upload/presign', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': window.arborisData?.nonce || '',
                },
                body: JSON.stringify({
                    filename: file.name,
                    content_type: file.type,
                    filesize: file.size,
                }),
            });

            if (!presignResponse.ok) {
                throw new Error('Failed to get presigned URL');
            }

            const { upload_url, s3_key } = await presignResponse.json();
            uploadedS3Key = s3_key;

            // Upload to S3 with progress
            const uploadProgress = document.getElementById('upload-progress');
            const progressBar = document.getElementById('progress-bar');
            const progressPercent = document.getElementById('progress-percent');

            uploadProgress.classList.remove('hidden');

            const xhr = new XMLHttpRequest();

            xhr.upload.addEventListener('progress', (e) => {
                if (e.lengthComputable) {
                    const percent = Math.round((e.loaded / e.total) * 100);
                    progressBar.style.width = percent + '%';
                    progressPercent.textContent = percent + '%';
                }
            });

            xhr.addEventListener('load', () => {
                if (xhr.status === 200) {
                    progressPercent.textContent = '✓ Upload terminé';
                    progressBar.classList.add('bg-green-600');
                } else {
                    throw new Error('Upload failed');
                }
            });

            xhr.addEventListener('error', () => {
                throw new Error('Upload error');
            });

            xhr.open('PUT', upload_url);
            xhr.setRequestHeader('Content-Type', file.type);
            xhr.send(file);

        } catch (error) {
            console.error('Upload failed:', error);
            alert('Échec de l\'upload. Veuillez réessayer.');
            selectedFile = null;
            uploadedS3Key = null;
            submitBtn.disabled = true;
        }
    }

    // Form submission
    document.getElementById('upload-form').addEventListener('submit', async (e) => {
        e.preventDefault();

        if (!uploadedS3Key) {
            alert('Veuillez attendre la fin de l\'upload du fichier.');
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Publication...';

        try {
            const formData = {
                s3_key: uploadedS3Key,
                title: document.getElementById('title').value,
                description: document.getElementById('description').value,
                tags: document.getElementById('tags').value.split(',').map(t => t.trim()).filter(t => t),
                license: document.getElementById('license').value,
                location_name: document.getElementById('location-name').value,
                latitude: parseFloat(document.getElementById('latitude').value) || null,
                longitude: parseFloat(document.getElementById('longitude').value) || null,
                recorded_at: document.getElementById('recorded-at').value,
                equipment: document.getElementById('equipment').value,
            };

            const response = await fetch('/wp-json/arborisis/v1/upload/finalize', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': window.arborisData?.nonce || '',
                },
                body: JSON.stringify(formData),
            });

            if (!response.ok) {
                throw new Error('Finalization failed');
            }

            const result = await response.json();

            // Redirect to sound page
            window.location.href = `/sound/${result.sound_id}`;

        } catch (error) {
            console.error('Publication failed:', error);
            alert('Échec de la publication. Veuillez réessayer.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Publier l\'Enregistrement';
        }
    });

    // Use current location
    document.getElementById('use-current-location').addEventListener('click', () => {
        if (!navigator.geolocation) {
            alert('La géolocalisation n\'est pas supportée par votre navigateur.');
            return;
        }

        navigator.geolocation.getCurrentPosition((position) => {
            document.getElementById('latitude').value = position.coords.latitude.toFixed(6);
            document.getElementById('longitude').value = position.coords.longitude.toFixed(6);
        }, (error) => {
            alert('Impossible de récupérer votre position.');
        });
    });

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }
</script>

<?php get_footer(); ?>