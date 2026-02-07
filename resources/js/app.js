import L from 'leaflet';
import maplibregl from 'maplibre-gl';
window.L = L;
import 'leaflet-draw';
import 'leaflet-routing-machine';
import 'leaflet-routing-machine/dist/leaflet-routing-machine.css';
import 'leaflet.markercluster';
import 'leaflet.markercluster/dist/MarkerCluster.css';

import { gsap } from 'gsap';
window.gsap = gsap;
window.maplibregl = maplibregl;

import Chart from 'chart.js/auto';
window.Chart = Chart;

import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';
import CultureSection from './components/culture-section';

Alpine.plugin(intersect);
Alpine.data('cultureSection', CultureSection);

window.Alpine = Alpine;

Alpine.start();
