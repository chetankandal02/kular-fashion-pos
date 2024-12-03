<template>
    <div class="row">
        <!-- Left Column: Search and Order Items -->
        <div class="col-lg-8">
            <BarCodeBox @add-to-cart="addToCart" />
            <OrderItemsTable :order-items="orderItems" :return-items="returnItems" @remove-from-cart="removeFromCart" />
        </div>
        <!-- Right Column: Order Summary -->
        <div class="col-lg-4">
            <OrderSummary :order-items="orderItems" :return-items="returnItems" @return-item="returnItem" @cancel-sale="cancelSale" />
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
            returnItems: []
        };
    },
    methods: {
        returnItem(item){
            this.returnItems.push(item);
            localStorage.setItem('returnItems', JSON.stringify(this.returnItems));
        },
        addToCart(product) {
            this.orderItems.push(product);
            localStorage.setItem('orderItems', JSON.stringify(this.orderItems));
        },
        removeFromCart(itemIndex, storageKey = 'orderItems') {
            if(storageKey=='orderItems'){
                this.orderItems.splice(itemIndex, 1);
                localStorage.setItem('orderItems', JSON.stringify(this.orderItems));
            } else if(storageKey=='returnItems'){
                this.returnItems.splice(itemIndex, 1);
                localStorage.setItem('returnItems', JSON.stringify(this.returnItems));
            }
        },
        cancelSale(){
            this.returnItems = [];
            localStorage.removeItem('returnItems');

            this.orderItems = [];
            localStorage.removeItem('orderItems');
        }
    },
    mounted(){
        if(localStorage.getItem('orderItems')){
            this.orderItems = JSON.parse(localStorage.getItem('orderItems'));
        }
        
        if(localStorage.getItem('returnItems')){
            this.returnItems = JSON.parse(localStorage.getItem('returnItems'));
        }
    }
};
</script>

<style scoped></style>