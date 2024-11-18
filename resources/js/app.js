import { createApp } from 'vue';
import SalesListButton from './components/buttons/SalesListButton.vue';

const app = createApp({});
app.component('sales-list-button', SalesListButton);

app.mount('#layout-wrapper');
