import { createApp } from 'vue';
import PosPage from './pages/PosPage.vue';

const app = createApp({});
app.component('pos-page', PosPage);

app.mount('#layout-wrapper');
