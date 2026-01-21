/**
 * Scroll Animations & Effects
 * Handles intersection observer animations, parallax, and smooth scrolling
 */

class ScrollAnimations {
    constructor() {
        this.observers = [];
        this.init();
    }

    init() {
        this.initScrollAnimations();
        this.initParallax();
        this.initCounters();
    }

    /**
     * Intersection Observer for scroll-triggered animations
     */
    initScrollAnimations() {
        const elementsToAnimate = document.querySelectorAll('[data-animate]');

        if (elementsToAnimate.length === 0) return;

        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        const element = entry.target;
                        const animation = element.dataset.animate || 'fade-in';
                        const delay = element.dataset.delay || '0';

                        element.style.animationDelay = `${delay}ms`;
                        element.classList.add(`animate-${animation}`);

                        // Unobserve after animation
                        if (element.dataset.once !== 'false') {
                            observer.unobserve(element);
                        }
                    }
                });
            },
            {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px',
            }
        );

        elementsToAnimate.forEach((el) => observer.observe(el));
        this.observers.push(observer);
    }

    /**
     * Parallax effect for elements
     */
    initParallax() {
        const parallaxElements = document.querySelectorAll('[data-parallax]');

        if (parallaxElements.length === 0) return;

        const handleScroll = () => {
            const scrolled = window.pageYOffset;

            parallaxElements.forEach((el) => {
                const speed = parseFloat(el.dataset.parallax) || 0.5;
                const yPos = -(scrolled * speed);
                el.style.transform = `translate3d(0, ${yPos}px, 0)`;
            });
        };

        // Throttle scroll handler
        let ticking = false;
        window.addEventListener('scroll', () => {
            if (!ticking) {
                window.requestAnimationFrame(() => {
                    handleScroll();
                    ticking = false;
                });
                ticking = true;
            }
        });
    }

    /**
     * Animated counters for stats
     */
    initCounters() {
        const counters = document.querySelectorAll('[data-counter]');

        if (counters.length === 0) return;

        const animateCounter = (element, target, duration = 2000) => {
            const start = 0;
            const increment = target / (duration / 16); // 60fps
            let current = start;

            const updateCounter = () => {
                current += increment;
                if (current < target) {
                    element.textContent = Math.floor(current).toLocaleString();
                    requestAnimationFrame(updateCounter);
                } else {
                    element.textContent = target.toLocaleString();
                }
            };

            updateCounter();
        };

        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        const element = entry.target;
                        const target = parseInt(element.dataset.counter);
                        const duration = parseInt(element.dataset.duration) || 2000;

                        animateCounter(element, target, duration);
                        observer.unobserve(element);
                    }
                });
            },
            { threshold: 0.5 }
        );

        counters.forEach((counter) => observer.observe(counter));
        this.observers.push(observer);
    }

    /**
     * Smooth scroll to element
     */
    static scrollTo(elementOrSelector, offset = 0) {
        const element = typeof elementOrSelector === 'string'
            ? document.querySelector(elementOrSelector)
            : elementOrSelector;

        if (!element) return;

        const targetPosition = element.getBoundingClientRect().top + window.pageYOffset - offset;

        window.scrollTo({
            top: targetPosition,
            behavior: 'smooth',
        });
    }

    /**
     * Clean up observers
     */
    destroy() {
        this.observers.forEach((observer) => observer.disconnect());
    }
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    window.scrollAnimations = new ScrollAnimations();

    // Add smooth scroll to all anchor links
    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href === '#') return;

            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                ScrollAnimations.scrollTo(target, 100);
            }
        });
    });
});

export default ScrollAnimations;
