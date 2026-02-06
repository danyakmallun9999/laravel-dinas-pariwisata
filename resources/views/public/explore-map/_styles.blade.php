{{-- Inline Styles for Explore Map --}}
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
    /* MARKER STYLES (Minimalist) */
    /* ============================================ */
    .marker-container {
        position: relative;
        width: 44px;
        height: 52px;
        display: flex;
        flex-direction: column;
        align-items: center;
        cursor: pointer;
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
        transform: scale(1.1);
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
</style>
