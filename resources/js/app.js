import L from 'leaflet';
import maplibregl from 'maplibre-gl';
window.L = L;
import 'leaflet-draw';
import 'leaflet-routing-machine';
import 'leaflet-routing-machine/dist/leaflet-routing-machine.css';
import 'leaflet.markercluster';
import 'leaflet.markercluster/dist/MarkerCluster.css';

import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);
window.gsap = gsap;
window.ScrollTrigger = ScrollTrigger;
window.maplibregl = maplibregl;

import Chart from 'chart.js/auto';
window.Chart = Chart;

// Financial Report — ApexCharts
import './charts/financial';

// Ticket Dashboard — ApexCharts
import './charts/dashboard';

import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';
import CultureSection from './components/culture-section';

Alpine.plugin(intersect);
Alpine.data('cultureSection', CultureSection);

// Register QR Scanner
import registerQrScanner from './pages/admin/scan';
registerQrScanner(Alpine);

window.Alpine = Alpine;

Alpine.start();
