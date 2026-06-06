import gsap from 'gsap';
import '../css/blobCursor.css';

class BlobCursor {
    constructor(options = {}) {
        this.options = {
            blobType: options.blobType || 'circle',
            fillColor: options.fillColor || '#2F6CF9',
            trailCount: options.trailCount || 3,
            sizes: options.sizes || [20, 30, 28],
            innerSizes: options.innerSizes || [12, 22, 16],
            innerColor: options.innerColor || 'rgba(255,255,255,0.8)',
            opacities: options.opacities || [1.0, 1.0, 1.0], // Keep blobs opaque inside filter to prevent vanishing
            containerOpacity: options.containerOpacity !== undefined ? options.containerOpacity : 1.0, // Apply transparency to container
            shadowColor: options.shadowColor || 'rgba(0,0,0,0.15)',
            shadowBlur: options.shadowBlur || 5,
            shadowOffsetX: options.shadowOffsetX || 0,
            shadowOffsetY: options.shadowOffsetY || 0,
            filterId: options.filterId || 'blob',
            useFilter: options.useFilter !== undefined ? options.useFilter : true,
            fastDuration: options.fastDuration || 0.08,
            slowDuration: options.slowDuration || 0.25, // Lower duration to keep blobs close when moving fast
            fastEase: options.fastEase || 'power3.out',
            slowEase: options.slowEase || 'power1.out',
            zIndex: options.zIndex || 9999
        };

        this.container = null;
        this.blobs = [];
        this.init();
    }

    init() {
        // Add class to body to enable cursor hiding via CSS
        document.body.classList.add('has-blob-cursor');

        // Create container element
        this.container = document.createElement('div');
        this.container.className = 'blob-cursor-container';
        this.container.style.zIndex = this.options.zIndex;

        // Add SVG filter if requested
        if (this.options.useFilter) {
            const svgNS = "http://www.w3.org/2000/svg";
            const svg = document.createElementNS(svgNS, 'svg');
            svg.setAttribute('style', 'position: absolute; width: 0; height: 0;');

            const filter = document.createElementNS(svgNS, 'filter');
            filter.setAttribute('id', this.options.filterId);

            const blur = document.createElementNS(svgNS, 'feGaussianBlur');
            blur.setAttribute('in', 'SourceGraphic');
            blur.setAttribute('result', 'blur');
            blur.setAttribute('stdDeviation', '18'); // Reduced blur to maintain alpha at speed

            const matrix = document.createElementNS(svgNS, 'feColorMatrix');
            matrix.setAttribute('in', 'blur');
            matrix.setAttribute('values', '1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 35 -10');

            filter.appendChild(blur);
            filter.appendChild(matrix);
            svg.appendChild(filter);
            this.container.appendChild(svg);
        }

        // Create blob-main wrapper
        const blobMain = document.createElement('div');
        blobMain.className = 'blob-cursor-main';
        blobMain.style.opacity = this.options.containerOpacity;
        if (this.options.useFilter) {
            blobMain.style.filter = `url(#${this.options.filterId})`;
        }

        // Create blobs
        for (let i = 0; i < this.options.trailCount; i++) {
            const blob = document.createElement('div');
            blob.className = 'blob-cursor-item';
            
            // Set styles
            blob.style.width = `${this.options.sizes[i]}px`;
            blob.style.height = `${this.options.sizes[i]}px`;
            blob.style.borderRadius = this.options.blobType === 'circle' ? '50%' : '0%';
            blob.style.backgroundColor = this.options.fillColor;
            blob.style.opacity = this.options.opacities[i];
            blob.style.boxShadow = `${this.options.shadowOffsetX}px ${this.options.shadowOffsetY}px ${this.options.shadowBlur}px 0 ${this.options.shadowColor}`;
            blob.style.top = '0';
            blob.style.left = '0';

            // Create inner dot
            const innerDot = document.createElement('div');
            innerDot.className = 'blob-cursor-inner';
            innerDot.style.width = `${this.options.innerSizes[i]}px`;
            innerDot.style.height = `${this.options.innerSizes[i]}px`;
            innerDot.style.top = `${(this.options.sizes[i] - this.options.innerSizes[i]) / 2}px`;
            innerDot.style.left = `${(this.options.sizes[i] - this.options.innerSizes[i]) / 2}px`;
            innerDot.style.backgroundColor = this.options.innerColor;
            innerDot.style.borderRadius = this.options.blobType === 'circle' ? '50%' : '0%';

            blob.appendChild(innerDot);
            blobMain.appendChild(blob);
            this.blobs.push(blob);
        }

        this.container.appendChild(blobMain);
        document.body.appendChild(this.container);

        // Bind events
        window.addEventListener('mousemove', this.handleMove.bind(this));
        window.addEventListener('touchmove', this.handleMove.bind(this));
    }

    handleMove(e) {
        const x = 'clientX' in e ? e.clientX : e.touches[0].clientX;
        const y = 'clientY' in e ? e.clientY : e.touches[0].clientY;

        this.blobs.forEach((el, i) => {
            const isLead = i === 0;
            gsap.to(el, {
                x: x,
                y: y,
                xPercent: -50,
                yPercent: -50,
                duration: isLead ? this.options.fastDuration : this.options.slowDuration,
                ease: isLead ? this.options.fastEase : this.options.slowEase
            });
        });
    }
}

// Automatically instantiate on DOM load if we are on the landing page
document.addEventListener('DOMContentLoaded', () => {
    // Check if the current page has a hero section or is the landing page
    if (document.getElementById('features') && window.matchMedia('(min-width: 1024px)').matches) {
        new BlobCursor({
            blobType: 'circle',
            fillColor: '#2F6CF9', // style guide main-blue
            trailCount: 3,
            sizes: [20, 35, 25],
            innerSizes: [8, 16, 10],
            innerColor: 'rgba(255, 255, 255, 0.75)',
            opacities: [1.0, 1.0, 1.0], // Keep blobs opaque inside gooey filter
            containerOpacity: 1.0, // Global solid aesthetic
            shadowColor: 'rgba(47, 108, 249, 0.04)',
            shadowBlur: 10,
            shadowOffsetX: 0,
            shadowOffsetY: 0,
            zIndex: 9999 // Floats on top of all text & cards so it remains visible
        });
    }
});

