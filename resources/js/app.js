import L from 'leaflet';
import maplibregl from 'maplibre-gl';
window.L = L;
import 'leaflet-draw';
import 'leaflet-routing-machine';
import 'leaflet-routing-machine/dist/leaflet-routing-machine.css'; // Import CSS
window.maplibregl = maplibregl;

import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';

Alpine.plugin(intersect);

window.Alpine = Alpine;

Alpine.start();
