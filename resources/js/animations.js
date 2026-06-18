document.addEventListener('DOMContentLoaded', () => {
    // Scroll Reveal implementation using Intersection Observer
    const revealElements = document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale');

    if (revealElements.length > 0) {
        const revealObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active');
                    // Once animated, we don't need to observe it anymore
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.08, // Trigger when 8% of the element is visible
            rootMargin: '0px 0px -50px 0px' // offset slightly to trigger slightly before coming fully into view
        });

        revealElements.forEach(element => {
            revealObserver.observe(element);
        });
    }
});
