<template>
    <div class="row">
        <!-- Left Column: Search and Order Items -->
        <div class="col-lg-8">
            <BarCodeBox @add-to-cart="addToCart" />
            <OrderItemsTable :order-items="orderItems" @remove-from-cart="removeFromCart" />
        </div>
        <!-- Right Column: Order Summary -->
        <div class="col-lg-4">
            <OrderSummary :order-items="orderItems" @cancel-sale="cancelSale" />
        </div>
    </div>
</template>

<script>
import BarCodeBox from '../components/pos/BarCodeBox.vue';
import OrderItemsTable from '../components/pos/OrderItemsTable.vue';
import OrderSummary from '../components/pos/OrderSummary.vue';

export default {
    components: {
        BarCodeBox,
        OrderItemsTable,
        OrderSummary,
    },
    data() {
        return {
            orderItems: [],
        };
    },
    methods: {
        addToCart(product) {
            this.orderItems.push(product);
            localStorage.setItem('orderItems', JSON.stringify(this.orderItems));
        },
        removeFromCart(itemIndex) {
            this.orderItems.splice(itemIndex, 1);
            localStorage.setItem('orderItems', JSON.stringify(this.orderItems));
            /* this.orderItems = this.orderItems.filter(item => item.product_id !== product.product_id || item.size !== product.size || item.color !== product.color);
            localStorage.setItem('orderItems', JSON.stringify(this.orderItems)); */
        },
        cancelSale(){
            this.orderItems = [];
            localStorage.removeItem('orderItems');
        }
    },
    mounted(){
        if(localStorage.getItem('orderItems')){
            this.orderItems = JSON.parse(localStorage.getItem('orderItems'));
        }
    }
};
</script>

<style scoped></style>