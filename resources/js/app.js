import './animations';
import './blobCursor';
import './aurora';

// Process all static image assets with Vite
import.meta.glob([
    '../images/**',
], { eager: true });
