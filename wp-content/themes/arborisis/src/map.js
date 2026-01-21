/**
 * Interactive Map with Leaflet
 * Sound markers and clustering
 */

import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

class ArbMap {
    constructor(config) {
        this.config = {
            defaultCenter: [46.2276, 2.2137],
            defaultZoom: 6,
            minZoom: 2,
            maxZoom: 18,
            apiEndpoint: '/wp-json/arborisis/v1/map/sounds',
            ...config,
        };

        this.map = null;
        this.markers = [];
        this.currentFilter = 'all';
        this.currentBounds = null;

        this.init();
    }

    init() {
        try {
            if (!document.getElementById('map')) {
                console.error('Map container not found');
                return;
            }

            this.createMap();
            this.addEventListeners();
            this.loadSounds();

        } catch (error) {
            console.error('Error initializing map:', error);
            // Show error in map container
            const mapContainer = document.getElementById('map');
            if (mapContainer) {
                mapContainer.innerHTML = `
                    <div class="flex items-center justify-center w-full h-full text-red-500">
                        <div class="text-center">
                            <p class="font-bold">Erreur de chargement de la carte</p>
                            <p class="text-sm">${error.message}</p>
                        </div>
                    </div>
                `;
            }
        } finally {
            // Hide loading overlay independently of success/failure
            setTimeout(() => {
                document.getElementById('map-loading')?.classList.add('hidden');
            }, 500);
        }
    }

    createMap() {
        // Check if map container has size
        const mapEl = document.getElementById('map');
        if (!mapEl.clientHeight) {
            mapEl.style.minHeight = '600px';
        }

        // Initialize map
        this.map = L.map('map', {
            center: this.config.defaultCenter,
            zoom: this.config.defaultZoom,
            minZoom: this.config.minZoom,
            maxZoom: this.config.maxZoom,
            zoomControl: false,
        });

        // Add zoom control to top right
        L.control.zoom({
            position: 'topright'
        }).addTo(this.map);

        // Add tile layer (OpenStreetMap)
        this.tileLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19,
        }).addTo(this.map);

        // Fix map size invalidation
        setTimeout(() => {
            this.map.invalidateSize();
        }, 100);
    }

    addEventListeners() {
        // Reload sounds when map moves
        this.map.on('moveend', () => {
            this.loadSounds();
        });

        this.map.on('zoomend', () => {
            this.loadSounds();
        });
    }

    async loadSounds() {
        const bounds = this.map.getBounds();
        const zoom = this.map.getZoom();

        const bbox = [
            bounds.getSouth(),
            bounds.getWest(),
            bounds.getNorth(),
            bounds.getEast(),
        ];

        try {
            const response = await fetch(
                `${this.config.apiEndpoint}?bbox=${bbox.join(',')}&zoom=${zoom}${this.currentFilter !== 'all' ? '&tag=' + this.currentFilter : ''}`
            );
            const data = await response.json();

            this.clearMarkers();
            this.renderMarkers(data);

            // Update visible count
            const totalSounds = data.sounds?.length || 0 + (data.clusters?.reduce((sum, c) => sum + c.count, 0) || 0);
            document.getElementById('visible-count').textContent = totalSounds;

        } catch (error) {
            console.error('Failed to load sounds:', error);
        }
    }

    clearMarkers() {
        this.markers.forEach(marker => marker.remove());
        this.markers = [];
    }

    renderMarkers(data) {
        // Render individual sounds
        if (data.sounds) {
            data.sounds.forEach(sound => {
                const marker = this.createSoundMarker(sound);
                this.markers.push(marker);
            });
        }

        // Render clusters
        if (data.clusters) {
            data.clusters.forEach(cluster => {
                const marker = this.createClusterMarker(cluster);
                this.markers.push(marker);
            });
        }
    }

    createSoundMarker(sound) {
        const icon = L.divIcon({
            className: 'custom-marker',
            html: `
                <div class="map-marker" style="background-color: #16a34a;">
                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                    </svg>
                </div>
            `,
            iconSize: [32, 32],
            iconAnchor: [16, 32],
        });

        const marker = L.marker([sound.latitude, sound.longitude], { icon })
            .addTo(this.map);

        marker.on('click', () => {
            this.showSoundPopup(sound);
        });

        // Tooltip on hover
        marker.bindTooltip(sound.title, {
            direction: 'top',
            offset: [0, -30],
        });

        return marker;
    }

    createClusterMarker(cluster) {
        const icon = L.divIcon({
            className: 'custom-cluster',
            html: `
                <div class="map-cluster" style="background-color: #9333ea;">
                    <span class="text-white font-bold">${cluster.count}</span>
                </div>
            `,
            iconSize: [48, 48],
            iconAnchor: [24, 24],
        });

        const marker = L.marker([cluster.centroid.lat, cluster.centroid.lon], { icon })
            .addTo(this.map);

        marker.on('click', () => {
            // Zoom into cluster
            this.map.fitBounds([
                [cluster.bounds.min_lat, cluster.bounds.min_lon],
                [cluster.bounds.max_lat, cluster.bounds.max_lon],
            ]);
        });

        marker.bindTooltip(`${cluster.count} sons`, {
            direction: 'top',
            offset: [0, -24],
        });

        return marker;
    }

    showSoundPopup(sound) {
        const popup = document.getElementById('sound-popup');
        const content = document.getElementById('popup-content');

        if (!popup || !content) return;

        content.innerHTML = `
            <div class="flex gap-4">
                <img src="${sound.thumbnail || '/wp-content/themes/arborisis/assets/placeholder.jpg'}"
                     alt="${sound.title}"
                     class="w-24 h-24 rounded-lg object-cover flex-shrink-0">
                <div class="flex-1">
                    <h3 class="text-xl font-bold mb-2">${sound.title}</h3>
                    <p class="text-sm text-dark-600 dark:text-dark-400 mb-3 line-clamp-2">${sound.description || ''}</p>
                    <div class="flex items-center gap-4 text-sm">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            ${window.formatDuration(sound.duration)}
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                            ${sound.likes_count || 0}
                        </span>
                    </div>
                    ${sound.tags && sound.tags.length ? `
                        <div class="flex flex-wrap gap-1 mt-3">
                            ${sound.tags.slice(0, 3).map(tag => `<span class="badge badge-primary">${tag}</span>`).join('')}
                        </div>
                    ` : ''}
                </div>
            </div>
            <div class="flex gap-2 mt-4">
                <button class="btn btn-primary flex-1" onclick="window.playSound(${sound.id})">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z"/>
                    </svg>
                    Écouter
                </button>
                <a href="/sound/${sound.id}" class="btn btn-outline flex-1">
                    Voir la page
                </a>
            </div>
        `;

        popup.classList.remove('hidden');
    }

    setFilter(filter) {
        this.currentFilter = filter;
        this.loadSounds();
    }

    setStyle(style) {
        // Remove current tile layer
        if (this.tileLayer) {
            this.map.removeLayer(this.tileLayer);
        }

        // Add new tile layer based on style
        let tileUrl;
        switch (style) {
            case 'satellite':
                tileUrl = 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}';
                break;
            case 'terrain':
                tileUrl = 'https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png';
                break;
            case 'streets':
            default:
                tileUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
        }

        this.tileLayer = L.tileLayer(tileUrl, {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19,
        }).addTo(this.map);
    }

    locateUser() {
        this.map.locate({ setView: true, maxZoom: 12 });

        this.map.on('locationfound', (e) => {
            L.circleMarker(e.latlng, {
                color: '#16a34a',
                fillColor: '#16a34a',
                fillOpacity: 0.3,
                radius: 10,
            }).addTo(this.map);
        });

        this.map.on('locationerror', (e) => {
            alert('Impossible de vous localiser. Veuillez activer la géolocalisation.');
        });
    }

    flyTo(latlng, zoom = 12) {
        this.map.flyTo(latlng, zoom);
    }
}

// Initialize map when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('map')) {
        const config = window.mapConfig || {};
        window.arbMap = new ArbMap(config);
    }
});

export default ArbMap;
