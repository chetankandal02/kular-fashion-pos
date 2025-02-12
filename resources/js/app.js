import { createApp } from 'vue';
import PosPage from './pages/PosPage.vue';
import OnHoldSalesModal from './components/OnHoldSalesModal.vue';
import InventoryTransferModal from './components/InventoryTransferModal.vue';
import SearchArticleModal from './components/pos/SearchArticleModal.vue';

const app = createApp({});
app.component('pos-page', PosPage);
app.component('on-hold-sales-modal', OnHoldSalesModal);
app.component('search-article-modal', SearchArticleModal);
app.component('inventory-transfer-modal', InventoryTransferModal);

app.mount('#layout-wrapper');
