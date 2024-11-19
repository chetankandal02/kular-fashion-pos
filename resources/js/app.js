import { createApp } from 'vue';
import PosPage from './pages/PosPage.vue';
import OnHoldSalesModal from './components/OnHoldSalesModal.vue';

const app = createApp({});
app.component('pos-page', PosPage);
app.component('on-hold-sales-modal', OnHoldSalesModal);

app.mount('#layout-wrapper');
