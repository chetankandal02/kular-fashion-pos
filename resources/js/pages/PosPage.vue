<template>
    <div class="row">
        <!-- Left Column: Search and Order Items -->
        <div class="col-lg-8">
            <BarCodeBox @add-to-cart="addToCart" />
            <OrderItemsTable :order-items="orderItems" @remove-from-cart="removeFromCart" />
        </div>
        <!-- Right Column: Order Summary -->
        <div class="col-lg-4">
            <OrderSummary :order-items="orderItems" />
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
            orderItems: [
                { code: '300001', description: 'Neal Matthews', color: 'Grey', size: 'S', brand: 'Puma', price: 45 },
                { code: '300005', description: 'Jamal Burnett', color: 'Black', size: 'L', brand: 'Nike', price: 470 },
            ],
            availableProducts: [
                { id: 1, name: 'TShirt', description: 'Comfortable cotton T-shirt', price: 15, image: 'https://via.placeholder.com/100' },
                { id: 2, name: 'Jeans', description: 'Denim jeans', price: 30, image: 'https://via.placeholder.com/100' },
                { id: 3, name: 'Shoes', description: 'Running shoes', price: 50, image: 'https://via.placeholder.com/100' },
            ],
        };
    },
    methods: {
        addToCart(product) {
            this.orderItems.push(product);
        },
        removeFromCart(product) {
            this.orderItems = this.orderItems.filter(item => item.id !== product.id);
        },
    },
};
</script>

<style scoped></style>