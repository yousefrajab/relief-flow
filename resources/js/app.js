import './bootstrap';
import Alpine from 'alpinejs';
import L from 'leaflet';
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';
import { Chart, ArcElement, DoughnutController, Tooltip, Legend } from 'chart.js';

delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconRetinaUrl: markerIcon2x,
    iconUrl: markerIcon,
    shadowUrl: markerShadow,
});

Chart.register(ArcElement, DoughnutController, Tooltip, Legend);

window.L = L;
window.Chart = Chart;
window.Alpine = Alpine;
Alpine.start();
