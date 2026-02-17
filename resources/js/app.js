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
// Admin Main Dashboard - ApexCharts
import './charts/admin-dashboard';

// TinyMCE Global Handler
import './plugins/tinymce';

import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import intersect from '@alpinejs/intersect';
import morph from '@alpinejs/morph';
import CultureSection from './components/culture-section';
import MapDrawer from './components/map-drawer';

Alpine.plugin(intersect);
Alpine.plugin(morph);
Alpine.data('cultureSection', CultureSection);
Alpine.data('mapDrawer', MapDrawer);

// Register QR Scanner
import registerQrScanner from './pages/admin/scan';
registerQrScanner(Alpine);

// Expose to window
window.Alpine = Alpine;
window.Livewire = Livewire;

// Start Livewire (which starts Alpine)
Livewire.start();
