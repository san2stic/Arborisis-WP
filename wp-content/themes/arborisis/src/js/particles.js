/**
 * Particle System for Hero Backgrounds
 * Creates an animated canvas particle effect
 */

class ParticleSystem {
    constructor(containerId, options = {}) {
        this.container = document.getElementById(containerId);
        if (!this.container) return;

        this.options = {
            particleCount: options.particleCount || 50,
            particleColor: options.particleColor || 'rgba(34, 197, 94, 0.5)',
            lineColor: options.lineColor || 'rgba(34, 197, 94, 0.2)',
            particleSpeed: options.particleSpeed || 0.5,
            lineDistance: options.lineDistance || 150,
            particleSize: options.particleSize || 3,
            ...options,
        };

        this.canvas = null;
        this.ctx = null;
        this.particles = [];
        this.animationId = null;

        this.init();
    }

    init() {
        // Create canvas
        this.canvas = document.createElement('canvas');
        this.canvas.className = 'absolute inset-0 w-full h-full';
        this.container.appendChild(this.canvas);

        this.ctx = this.canvas.getContext('2d');

        // Set canvas size
        this.resize();

        // Create particles
        this.createParticles();

        // Start animation
        this.animate();

        // Handle resize
        window.addEventListener('resize', () => this.resize());
    }

    resize() {
        this.canvas.width = this.container.offsetWidth;
        this.canvas.height = this.container.offsetHeight;
    }

    createParticles() {
        this.particles = [];
        for (let i = 0; i < this.options.particleCount; i++) {
            this.particles.push({
                x: Math.random() * this.canvas.width,
                y: Math.random() * this.canvas.height,
                vx: (Math.random() - 0.5) * this.options.particleSpeed,
                vy: (Math.random() - 0.5) * this.options.particleSpeed,
                radius: Math.random() * this.options.particleSize + 1,
            });
        }
    }

    drawParticles() {
        this.ctx.fillStyle = this.options.particleColor;

        this.particles.forEach(particle => {
            this.ctx.beginPath();
            this.ctx.arc(particle.x, particle.y, particle.radius, 0, Math.PI * 2);
            this.ctx.fill();
        });
    }

    drawLines() {
        this.ctx.strokeStyle = this.options.lineColor;
        this.ctx.lineWidth = 1;

        for (let i = 0; i < this.particles.length; i++) {
            for (let j = i + 1; j < this.particles.length; j++) {
                const dx = this.particles[i].x - this.particles[j].x;
                const dy = this.particles[i].y - this.particles[j].y;
                const distance = Math.sqrt(dx * dx + dy * dy);

                if (distance < this.options.lineDistance) {
                    const opacity = (1 - distance / this.options.lineDistance) * 0.5;
                    this.ctx.globalAlpha = opacity;
                    this.ctx.beginPath();
                    this.ctx.moveTo(this.particles[i].x, this.particles[i].y);
                    this.ctx.lineTo(this.particles[j].x, this.particles[j].y);
                    this.ctx.stroke();
                }
            }
        }

        this.ctx.globalAlpha = 1;
    }

    updateParticles() {
        this.particles.forEach(particle => {
            particle.x += particle.vx;
            particle.y += particle.vy;

            // Bounce off edges
            if (particle.x < 0 || particle.x > this.canvas.width) {
                particle.vx *= -1;
            }
            if (particle.y < 0 || particle.y > this.canvas.height) {
                particle.vy *= -1;
            }

            // Keep particles in bounds
            particle.x = Math.max(0, Math.min(this.canvas.width, particle.x));
            particle.y = Math.max(0, Math.min(this.canvas.height, particle.y));
        });
    }

    animate() {
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);

        this.drawLines();
        this.drawParticles();
        this.updateParticles();

        this.animationId = requestAnimationFrame(() => this.animate());
    }

    destroy() {
        if (this.animationId) {
            cancelAnimationFrame(this.animationId);
        }
        if (this.canvas && this.canvas.parentNode) {
            this.canvas.parentNode.removeChild(this.canvas);
        }
    }
}

// Initialize particles on page load
document.addEventListener('DOMContentLoaded', () => {
    const heroSection = document.querySelector('.hero');
    if (heroSection) {
        // Create a container for particles
        const particlesContainer = document.createElement('div');
        particlesContainer.id = 'particles-bg';
        particlesContainer.className = 'particles-container';
        heroSection.insertBefore(particlesContainer, heroSection.firstChild);

        // Initialize particle system
        window.particleSystem = new ParticleSystem('particles-bg', {
            particleCount: 60,
            particleColor: 'rgba(34, 197, 94, 0.4)',
            lineColor: 'rgba(34, 197, 94, 0.15)',
            particleSpeed: 0.3,
            lineDistance: 180,
            particleSize: 2.5,
        });
    }
});

export default ParticleSystem;
