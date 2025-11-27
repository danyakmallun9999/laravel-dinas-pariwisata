<script>
    // Load geometry from sessionStorage if available
    document.addEventListener('DOMContentLoaded', function() {
        const savedGeometry = sessionStorage.getItem('newFeatureGeometry');
        if (savedGeometry) {
            try {
                const geometry = JSON.parse(savedGeometry);
                const geometryInput = document.querySelector('input[name="geometry"]');
                if (geometryInput) {
                    geometryInput.value = savedGeometry;
                    // Dispatch input event to trigger Alpine reactivity
                    geometryInput.dispatchEvent(new Event('input', { bubbles: true }));
                    
                    // Wait for Alpine to initialize, then load geometry
                    setTimeout(() => {
                        if (window.Alpine) {
                            const drawer = document.querySelector('[x-data*="mapDrawer"]');
                            if (drawer) {
                                Alpine.nextTick(() => {
                                    const data = Alpine.$data(drawer);
                                    if (data && data.loadExistingGeometry) {
                                        data.loadExistingGeometry(geometry);
                                    }
                                });
                            }
                        }
                    }, 100);
                }
                // For point type (places), also set latitude/longitude
                const latInput = document.querySelector('input[name="latitude"]');
                const lngInput = document.querySelector('input[name="longitude"]');
                if (geometry.type === 'Point' && latInput && lngInput) {
                    const [lng, lat] = geometry.coordinates;
                    latInput.value = lat.toFixed(6);
                    lngInput.value = lng.toFixed(6);
                    latInput.dispatchEvent(new Event('input', { bubbles: true }));
                    lngInput.dispatchEvent(new Event('input', { bubbles: true }));
                }
                sessionStorage.removeItem('newFeatureGeometry');
            } catch (e) {
                console.error('Error loading geometry:', e);
            }
        }
    });
</script>

