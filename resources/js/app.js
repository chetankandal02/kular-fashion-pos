import { createApp } from 'vue';
import SalesListButton from './components/buttons/SalesListButton.vue';
import PosPage from './pages/PosPage.vue';

const app = createApp({});
app.component('sales-list-button', SalesListButton);
app.component('pos-page', PosPage);

app.mount('#layout-wrapper');
