{{-- Inline Styles for Explore Map - Enhanced with GSAP Animations --}}
<style>
    /* ============================================ */
    /* CUSTOM SCROLLBAR */
    /* ============================================ */
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #475569; }
    
    /* Hide scrollbar for category pills */
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    
    /* ============================================ */
    /* MATERIAL SYMBOLS */
    /* ============================================ */
    .material-symbols-outlined { 
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; 
        font-size: 24px;
    }
    .filled-icon { font-variation-settings: 'FILL' 1; }
    
    /* ============================================ */
    /* LEAFLET MAP */
    /* ============================================ */
    #leaflet-map { 
        height: 100%; 
        width: 100%; 
        z-index: 0; 
    }
    
    [x-cloak] { display: none !important; }
    
    /* ============================================ */
    /* MARKER STYLES (Minimalist + Animation) */
    /* ============================================ */
    .marker-container {
        position: relative;
        width: 44px;
        height: 52px;
        display: flex;
        flex-direction: column;
        align-items: center;
        cursor: pointer;
        animation: markerDrop 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }
    
    @keyframes markerDrop {
        0% { 
            opacity: 0;
            transform: translateY(-30px) scale(0.5);
        }
        60% { 
            transform: translateY(5px) scale(1.1);
        }
        100% { 
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
    
    .marker-pulse {
        position: absolute;
        top: 2px;
        left: 50%;
        transform: translateX(-50%);
        width: 36px;
        height: 36px;
        border-radius: 50%;
        opacity: 0.25;
        animation: marker-pulse 2.5s ease-out infinite;
    }
    
    .marker-icon {
        position: relative;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: 2.5px solid white;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 14px;
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        z-index: 2;
    }
    
    .marker-container:hover .marker-icon {
        transform: scale(1.15);
    }
    
    .marker-container:hover .marker-pulse {
        animation: marker-pulse-fast 1s ease-out infinite;
    }
    
    .marker-pointer {
        width: 0;
        height: 0;
        border-left: 6px solid transparent;
        border-right: 6px solid transparent;
        border-top: 10px solid;
        border-top-color: inherit;
        margin-top: -1px;
        z-index: 1;
    }
    
    @keyframes marker-pulse {
        0% { transform: translateX(-50%) scale(1); opacity: 0.3; }
        100% { transform: translateX(-50%) scale(1.8); opacity: 0; }
    }
    
    @keyframes marker-pulse-fast {
        0% { transform: translateX(-50%) scale(1); opacity: 0.5; }
        100% { transform: translateX(-50%) scale(2); opacity: 0; }
    }

    /* ============================================ */
    /* SAFE AREA (iPhone notch) */
    /* ============================================ */
    @supports (padding-bottom: env(safe-area-inset-bottom)) {
        .safe-bottom { 
            padding-bottom: calc(1.25rem + env(safe-area-inset-bottom)); 
        }
    }

    /* ============================================ */
    /* SNAP SCROLL */
    /* ============================================ */
    .snap-x { scroll-snap-type: x mandatory; }
    .snap-start { scroll-snap-align: start; }

    /* ============================================ */
    /* LINE CLAMP */
    /* ============================================ */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* ============================================ */
    /* CARD HOVER ANIMATIONS */
    /* ============================================ */
    .place-card {
        animation: fadeSlideUp 0.4s ease-out forwards;
        opacity: 0;
    }
    
    @keyframes fadeSlideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* ============================================ */
    /* BUTTON MICRO-INTERACTIONS */
    /* ============================================ */
    .btn-bounce:active {
        animation: btnBounce 0.3s ease;
    }
    
    @keyframes btnBounce {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(0.95); }
    }

    /* ============================================ */
    /* LOADING SPINNER */
    /* ============================================ */
    .loading-shimmer {
        background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
    }
    
    @keyframes shimmer {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    /* ============================================ */
    /* ROUTING CONTAINER */
    /* ============================================ */
    .leaflet-routing-container {
        background-color: rgba(255, 255, 255, 0.98);
        padding: 1rem;
        border-radius: 1rem;
        max-height: 40vh;
        overflow-y: auto;
        font-family: 'Plus Jakarta Sans', sans-serif;
        border: 1px solid #e2e8f0;
        position: absolute !important;
        z-index: 9999 !important;
        display: none;
        animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }
    
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Desktop: Right of sidebar */
    @media (min-width: 1024px) {
        .leaflet-routing-container {
            top: 80px !important;
            left: 440px !important;
            right: auto !important;
            width: 320px !important;
        }
    }
    
    /* Mobile: Bottom sheet style */
    @media (max-width: 1023px) {
        .leaflet-routing-container {
            bottom: 180px !important;
            top: auto !important;
            left: 16px !important;
            right: 16px !important;
            width: auto !important;
            max-width: 100% !important;
            border-radius: 1rem;
        }
    }
    
    .dark .leaflet-routing-container {
        background-color: rgba(30, 41, 59, 0.98);
        color: #f1f5f9;
        border-color: #334155;
    }
    
    .leaflet-routing-alt tr:hover {
        background-color: rgba(14, 165, 233, 0.05);
    }
    
    /* ============================================ */
    /* LEAFLET CONTROLS HIDE */
    /* ============================================ */
    .leaflet-control-attribution,
    .leaflet-control-zoom {
        display: none !important;
    }

    /* ============================================ */
    /* BOTTOM SHEET ANIMATION OVERRIDE */
    /* ============================================ */
    @media (max-width: 1023px) {
        .bottom-sheet-animate {
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
    }

    /* ============================================ */
    /* GLOW EFFECT */
    /* ============================================ */
    .glow-sky {
        box-shadow: 0 0 20px rgba(14, 165, 233, 0.3), 0 0 40px rgba(14, 165, 233, 0.1);
    }
</style>
