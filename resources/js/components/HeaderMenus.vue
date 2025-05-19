<template>
    <BarCodeBox @add-to-cart="addToCart" />

    <div class="dropdown dropdown-mega d-none d-lg-block ms-2">
        <button type="button" class="btn header-item waves-effect btn-success" data-bs-toggle="modal"
            data-bs-target="#searchArticalModal">
            <span>Search Article</span>
        </button>

        <button type="button" 
                style="background-color:  #e4606e; color: white;" 
                class="btn header-item waves-effect btn-dark" 
                data-bs-toggle="modal"
                data-bs-target="#salesListModal"
                @click="handleModalOpen">
        <span>Sales List</span>
        </button>

        <button type="button" class="btn header-item waves-effect btn-warning" data-bs-toggle="modal"
            data-bs-target="#onHoldSalesModal">
            <span>On Hold Sales</span>
        </button>
    </div>
</template>

<script>
import BarCodeBox from '../components/pos/BarCodeBox.vue';
import { EventBus } from '@/eventBus';

export default {
    components: {
        BarCodeBox,
    },
    methods: {
        addToCart(item) {
            EventBus.scanBarcode = {
                item: item,
                timestamp: Date.now(),
            };
        }
    }
};
</script>