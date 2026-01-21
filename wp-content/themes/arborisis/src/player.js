/**
 * Audio Player with WaveSurfer.js
 * Waveform visualization and playback
 */

import WaveSurfer from 'wavesurfer.js';

class ArbPlayer {
    constructor(containerId, audioUrl, options = {}) {
        this.containerId = containerId;
        this.audioUrl = audioUrl;
        this.options = {
            waveColor: '#94a3b8',
            progressColor: '#16a34a',
            cursorColor: '#9333ea',
            barWidth: 2,
            barRadius: 3,
            responsive: true,
            height: 96,
            normalize: true,
            backend: 'WebAudio',
            ...options,
        };

        this.wavesurfer = null;
        this.isPlaying = false;

        this.init();
    }

    init() {
        const container = document.getElementById(this.containerId);
        if (!container) {
            console.error(`Container #${this.containerId} not found`);
            return;
        }

        // Create WaveSurfer instance
        this.wavesurfer = WaveSurfer.create({
            container: `#${this.containerId}`,
            ...this.options,
        });

        // Load audio
        this.wavesurfer.load(this.audioUrl);

        // Event listeners
        this.wavesurfer.on('ready', () => this.onReady());
        this.wavesurfer.on('play', () => this.onPlay());
        this.wavesurfer.on('pause', () => this.onPause());
        this.wavesurfer.on('finish', () => this.onFinish());
        this.wavesurfer.on('audioprocess', () => this.onAudioProcess());
        this.wavesurfer.on('error', (error) => this.onError(error));

        this.setupControls();
    }

    setupControls() {
        // Play/Pause button
        const playBtn = document.getElementById('play-btn');
        if (playBtn) {
            playBtn.addEventListener('click', () => this.togglePlay());
        }

        // Progress bar
        const progressBar = document.getElementById('progress-bar');
        if (progressBar) {
            progressBar.addEventListener('input', (e) => {
                const progress = parseFloat(e.target.value) / 100;
                this.wavesurfer.seekTo(progress);
            });
        }

        // Volume button (toggle mute)
        const volumeBtn = document.getElementById('volume-btn');
        if (volumeBtn) {
            volumeBtn.addEventListener('click', () => this.toggleMute());
        }
    }

    togglePlay() {
        if (this.isPlaying) {
            this.pause();
        } else {
            this.play();
        }
    }

    play() {
        if (this.wavesurfer) {
            this.wavesurfer.play();
        }
    }

    pause() {
        if (this.wavesurfer) {
            this.wavesurfer.pause();
        }
    }

    stop() {
        if (this.wavesurfer) {
            this.wavesurfer.stop();
        }
    }

    toggleMute() {
        if (this.wavesurfer) {
            this.wavesurfer.toggleMute();
        }
    }

    seekTo(progress) {
        if (this.wavesurfer) {
            this.wavesurfer.seekTo(progress);
        }
    }

    setVolume(volume) {
        if (this.wavesurfer) {
            this.wavesurfer.setVolume(volume);
        }
    }

    onReady() {
        const duration = this.wavesurfer.getDuration();
        const totalTime = document.getElementById('total-time');
        if (totalTime) {
            totalTime.textContent = this.formatTime(duration);
        }
    }

    onPlay() {
        this.isPlaying = true;
        this.updatePlayButton(true);
    }

    onPause() {
        this.isPlaying = false;
        this.updatePlayButton(false);
    }

    onFinish() {
        this.isPlaying = false;
        this.updatePlayButton(false);
        this.wavesurfer.seekTo(0);
    }

    onAudioProcess() {
        const currentTime = this.wavesurfer.getCurrentTime();
        const duration = this.wavesurfer.getDuration();

        // Update current time
        const currentTimeEl = document.getElementById('current-time');
        if (currentTimeEl) {
            currentTimeEl.textContent = this.formatTime(currentTime);
        }

        // Update progress bar
        const progressBar = document.getElementById('progress-bar');
        if (progressBar) {
            const progress = (currentTime / duration) * 100;
            progressBar.value = progress;
        }
    }

    onError(error) {
        console.error('WaveSurfer error:', error);
    }

    updatePlayButton(isPlaying) {
        const playIcon = document.getElementById('play-icon');
        const pauseIcon = document.getElementById('pause-icon');

        if (playIcon && pauseIcon) {
            if (isPlaying) {
                playIcon.classList.add('hidden');
                pauseIcon.classList.remove('hidden');
            } else {
                playIcon.classList.remove('hidden');
                pauseIcon.classList.add('hidden');
            }
        }
    }

    formatTime(seconds) {
        if (!seconds || isNaN(seconds)) return '0:00';
        const mins = Math.floor(seconds / 60);
        const secs = Math.floor(seconds % 60);
        return `${mins}:${secs.toString().padStart(2, '0')}`;
    }

    destroy() {
        if (this.wavesurfer) {
            this.wavesurfer.destroy();
        }
    }
}

// Global audio player (for persistent playback across pages)
class GlobalPlayer {
    constructor() {
        this.currentPlayer = null;
        this.currentSoundId = null;
    }

    play(soundId, audioUrl) {
        // Stop current playback if different sound
        if (this.currentSoundId !== soundId && this.currentPlayer) {
            this.currentPlayer.destroy();
        }

        // Create new player if needed
        if (!this.currentPlayer || this.currentSoundId !== soundId) {
            this.currentPlayer = new ArbPlayer('waveform', audioUrl);
            this.currentSoundId = soundId;

            // Track play via API
            if (window.ArbAPI) {
                window.ArbAPI.trackPlay(soundId);
            }
        } else {
            // Resume current player
            this.currentPlayer.play();
        }
    }

    pause() {
        if (this.currentPlayer) {
            this.currentPlayer.pause();
        }
    }

    stop() {
        if (this.currentPlayer) {
            this.currentPlayer.stop();
        }
    }
}

// Initialize global player
window.globalPlayer = new GlobalPlayer();

// Global function to play sound (used from map, search, etc.)
window.playSound = async function(soundId) {
    try {
        const sound = await window.ArbAPI.getSound(soundId);
        window.globalPlayer.play(soundId, sound.audio_url);
    } catch (error) {
        console.error('Failed to play sound:', error);
    }
};

// Initialize single-sound page player
document.addEventListener('DOMContentLoaded', () => {
    const waveformContainer = document.getElementById('waveform');

    if (waveformContainer && typeof soundData !== 'undefined') {
        const player = new ArbPlayer('waveform', soundData.audioUrl);
        window.currentPagePlayer = player;

        // Like button functionality
        const likeBtn = document.getElementById('like-btn');
        if (likeBtn && window.ArbAPI) {
            likeBtn.addEventListener('click', async () => {
                try {
                    const result = await window.ArbAPI.toggleLike(soundData.id);
                    const likesCount = document.getElementById('likes-count');

                    if (result.liked) {
                        likeBtn.classList.add('text-red-500');
                        soundData.isLiked = true;
                    } else {
                        likeBtn.classList.remove('text-red-500');
                        soundData.isLiked = false;
                    }

                    if (likesCount) {
                        likesCount.textContent = result.likes_count;
                    }
                } catch (error) {
                    console.error('Failed to toggle like:', error);
                }
            });

            // Set initial like state
            if (soundData.isLiked) {
                likeBtn.classList.add('text-red-500');
            }
        }

        // Load similar sounds via graph API
        if (window.ArbAPI) {
            window.ArbAPI.exploreGraph(soundData.id, 1, 5).then(data => {
                const container = document.getElementById('similar-sounds');
                if (!container || !data.nodes) return;

                const similarSounds = data.nodes.filter(n => n.id !== soundData.id).slice(0, 5);

                container.innerHTML = similarSounds.map(sound => `
                    <a href="/sound/${sound.id}" class="flex items-center gap-3 p-2 hover:bg-dark-100 dark:hover:bg-dark-700 rounded-lg transition-colors">
                        <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-600 rounded-lg flex items-center justify-center text-white font-bold flex-shrink-0">
                            ${sound.title.charAt(0).toUpperCase()}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-medium text-sm truncate">${sound.title}</div>
                            <div class="text-xs text-dark-500">${sound.plays || 0} plays</div>
                        </div>
                    </a>
                `).join('');
            });
        }
    }
});

export { ArbPlayer, GlobalPlayer };
