
import L from 'leaflet';
import 'leaflet-draw';

export default (config) => ({
    map: null,
    drawControl: null,
    drawnItems: null,
    drawingMode: null,
    geometryJson: (function () {
        if (!config.initialGeometry) return '';
        if (typeof config.initialGeometry === 'string') {
            try {
                JSON.parse(config.initialGeometry);
                return config.initialGeometry;
            } catch (e) {
                return '';
            }
        }
        return JSON.stringify(config.initialGeometry);
    })(),
    coordinates: { lat: config.center[0], lng: config.center[1] },
    hasGeometry: false,
    currentDrawHandler: null,

    init() {
        this.$nextTick(() => {
            this.initMap();
            if (config.initialGeometry) {
                let geom = config.initialGeometry;
                if (typeof geom === 'string') {
                    try {
                        geom = JSON.parse(geom);
                    } catch (e) {
                        console.error('Failed to parse initialGeometry string:', e);
                        geom = null;
                    }
                }
                if (geom) {
                    this.loadExistingGeometry(geom);
                }
            }
        });
    },

    initMap() {
        if (!this.$refs.mapContainer) return;

        // Ensure clean slate if map already exists (SPA navigation safety)
        if (this.map) {
            this.map.remove();
            this.map = null;
        }

        this.map = L.map(this.$refs.mapContainer).setView(config.center, config.zoom);

        const googleStreets = L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}&s=Galileo&apistyle=s.t%3Apoi%7Cp.v%3Aoff%2Cs.t%3Atransit%7Cp.v%3Aoff', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            attribution: '&copy; Google Maps'
        }).addTo(this.map);

        const googleHybrid = L.tileLayer('https://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}&s=Galileo&apistyle=s.t%3Apoi%7Cp.v%3Aoff%2Cs.t%3Atransit%7Cp.v%3Aoff', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            attribution: '&copy; Google Maps'
        });

        const googleSatellite = L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}&s=Galileo&apistyle=s.t%3Apoi%7Cp.v%3Aoff%2Cs.t%3Atransit%7Cp.v%3Aoff', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            attribution: '&copy; Google Maps'
        });

        const googleTerrain = L.tileLayer('https://{s}.google.com/vt/lyrs=p&x={x}&y={y}&z={z}&s=Galileo&apistyle=s.t%3Apoi%7Cp.v%3Aoff%2Cs.t%3Atransit%7Cp.v%3Aoff', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            attribution: '&copy; Google Maps'
        });

        const baseLayers = {
            "Google Streets": googleStreets,
            "Google Hybrid": googleHybrid,
            "Google Satellite": googleSatellite,
            "Google Terrain": googleTerrain
        };

        this.drawnItems = new L.FeatureGroup();
        this.map.addLayer(this.drawnItems);

        const overlays = {
            "Gambar": this.drawnItems
        };

        L.control.layers(baseLayers, overlays).addTo(this.map);

        this.initDrawControl();

        if (config.drawType === 'point') {
            this.startDrawing('point');
        } else if (config.drawType === 'line') {
            this.startDrawing('line');
        } else if (config.drawType === 'polygon') {
            this.startDrawing('polygon');
        }

        // Force invalidate size after a small delay to handle flexbox resizing
        setTimeout(() => {
            this.map.invalidateSize();
        }, 200);
    },

    initDrawControl() {
        // Safe check for L.Control.Draw availability
        if (!L.Control.Draw) {
            console.warn('Leaflet Draw plugin not loaded');
            return;
        }

        const drawOptions = {
            position: 'topright',
            draw: {
                polygon: {
                    allowIntersection: true,
                    showArea: true,
                    drawError: {
                        color: '#e1e100',
                        message: '<strong>Oh snap!<strong> you can\'t draw that!'
                    },
                },
                polyline: {
                    metric: true
                },
                circle: false,
                rectangle: false,
                marker: true,
                circlemarker: false
            },
            edit: {
                featureGroup: this.drawnItems,
                remove: true
            }
        };

        this.drawControl = new L.Control.Draw(drawOptions);
        this.map.addControl(this.drawControl);

        this.map.on(L.Draw.Event.CREATED, (e) => {
            this.handleDrawCreated(e);
        });

        this.map.on(L.Draw.Event.EDITED, (e) => {
            this.handleDrawEdited(e);
        });

        this.map.on(L.Draw.Event.DELETED, (e) => {
            this.handleDrawDeleted(e);
        });
    },

    startDrawing(type) {
        this.map.off('click', this.handleMapClick);

        if (this.currentDrawHandler) {
            this.currentDrawHandler.disable();
            this.currentDrawHandler = null;
        }

        this.drawingMode = type;

        if (type === 'point') {
            this.map.on('click', this.handleMapClick.bind(this));
        } else {
            // Check if L.Draw is available
            if (!L.Draw) return;

            if (type === 'line') {
                this.currentDrawHandler = new L.Draw.Polyline(this.map, this.drawControl.options.draw.polyline);
            } else if (type === 'polygon') {
                this.currentDrawHandler = new L.Draw.Polygon(this.map, this.drawControl.options.draw.polygon);
            }

            if (this.currentDrawHandler) {
                this.currentDrawHandler.enable();
            }
        }
    },

    handleMapClick(e) {
        const { lat, lng } = e.latlng;
        this.coordinates = {
            lat: Number(lat).toFixed(6),
            lng: Number(lng).toFixed(6)
        };

        this.drawnItems.clearLayers();

        const marker = L.marker([lat, lng]);
        marker.addTo(this.drawnItems);

        this.geometryJson = JSON.stringify({
            type: 'Point',
            coordinates: [parseFloat(lng), parseFloat(lat)]
        });
        this.hasGeometry = true;
    },

    handleDrawCreated(e) {
        const layer = e.layer;
        this.drawnItems.addLayer(layer);
        this.updateGeometryFromLayer(layer);
    },

    handleDrawEdited(e) {
        const layers = e.layers;
        layers.eachLayer((layer) => {
            this.updateGeometryFromLayer(layer);
        });
    },

    handleDrawDeleted(e) {
        this.geometryJson = '';
        this.hasGeometry = false;
        if (config.drawType === 'point') {
            this.coordinates = { lat: config.center[0], lng: config.center[1] };
        }
    },

    updateGeometryFromLayer(layer) {
        const geojson = layer.toGeoJSON();
        const geometry = geojson.geometry;

        if (geometry) {
            this.geometryJson = JSON.stringify(geometry);
            this.hasGeometry = true;
            this.coordinates = {
                lat: Number(geojson.properties?.lat || config.center[0]).toFixed(6),
                lng: Number(geojson.properties?.lng || config.center[1]).toFixed(6)
            };

            if (geometry.type === 'Point') {
                this.coordinates = {
                    lat: Number(geometry.coordinates[1]).toFixed(6),
                    lng: Number(geometry.coordinates[0]).toFixed(6)
                };
            }

            if (geometry.type === 'Polygon' || geometry.type === 'MultiPolygon') {
                this.calculateArea(layer);
            }
        }
    },

    calculateArea(layer) {
        let areaSqMeters = 0;

        if (layer instanceof L.Polygon) {
            const latlngs = layer.getLatLngs();
            const ringArea = (ring) => L.GeometryUtil.geodesicArea(ring);

            const processLatLngs = (coords) => {
                if (coords.length === 0) return;
                if (coords[0] instanceof L.LatLng) {
                    areaSqMeters += ringArea(coords);
                } else if (Array.isArray(coords[0])) {
                    coords.forEach(child => processLatLngs(child));
                }
            };

            processLatLngs(latlngs);
        }

        const areaHectares = (areaSqMeters / 10000).toFixed(4);
        this.$dispatch('area-calculated', areaHectares);
    },

    clearDrawing() {
        this.drawnItems.clearLayers();
        this.geometryJson = '';
        this.hasGeometry = false;
        if (config.drawType === 'point') {
            this.coordinates = { lat: config.center[0], lng: config.center[1] };
        }
    },

    editDrawing() {
        if (!this.hasGeometry) return;

        const editControl = new L.EditToolbar.Edit(this.map, {
            featureGroup: this.drawnItems
        });
        editControl.enable();
    },

    loadExistingGeometry(geometry) {
        const tempLayer = L.geoJSON(geometry, {
            style: {
                color: '#3388ff',
                fillOpacity: 0.2,
                weight: 3
            },
            pointToLayer: (feature, latlng) => {
                return L.marker(latlng);
            }
        });

        const layers = tempLayer.getLayers();
        if (layers.length > 0) {
            const layer = layers[0];

            if (layer instanceof L.LayerGroup) {
                layer.eachLayer(l => {
                    l.addTo(this.drawnItems);
                });
            } else {
                layer.addTo(this.drawnItems);
            }

            try {
                this.map.fitBounds(tempLayer.getBounds());
            } catch (e) {
                console.warn('Map fitBounds failed:', e);
            }

            this.hasGeometry = true;
            this.geometryJson = JSON.stringify(geometry);

            if (geometry.type === 'Point') {
                this.coordinates = {
                    lat: Number(geometry.coordinates[1]).toFixed(6),
                    lng: Number(geometry.coordinates[0]).toFixed(6)
                };
            }

            if (geometry.type === 'Polygon' || geometry.type === 'MultiPolygon') {
                if (layer instanceof L.LayerGroup) {
                    // TODO: Handle area for group
                } else {
                    this.calculateArea(layer);
                }
            }
        }
    }
});
