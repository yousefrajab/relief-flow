import './bootstrap';
import { submitFormOrQueue, syncQueue } from './offline-queue';
import Alpine from 'alpinejs';
import L from 'leaflet';
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';
import {
    Chart,
    ArcElement,
    DoughnutController,
    LineController,
    LineElement,
    PointElement,
    CategoryScale,
    LinearScale,
    Filler,
    Tooltip,
    Legend,
} from 'chart.js';

delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconRetinaUrl: markerIcon2x,
    iconUrl: markerIcon,
    shadowUrl: markerShadow,
});

Chart.register(
    ArcElement,
    DoughnutController,
    LineController,
    LineElement,
    PointElement,
    CategoryScale,
    LinearScale,
    Filler,
    Tooltip,
    Legend
);

window.L = L;
window.Chart = Chart;
window.Alpine = Alpine;
window.ReliefFlowOffline = { submitFormOrQueue, syncQueue };
Alpine.start();

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(() => {
            // Offline support is a progressive enhancement — the app works fine without it.
        });
    });
}
