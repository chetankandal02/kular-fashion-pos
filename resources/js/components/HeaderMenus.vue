<template>
    <BarCodeBox @add-to-cart="addToCart" />

    <div class="dropdown dropdown-mega d-none d-lg-block ms-2">
        <button type="button" class="btn header-item waves-effect btn-success" data-bs-toggle="modal"
            data-bs-target="#searchArticalModal">
            <span class="fs-5">Search Article</span>
        </button>

        <button type="button" class="btn header-item waves-effect btn-dark" data-bs-toggle="modal"
            data-bs-target="#salesListModal">
            <span class="fs-5">Sales List</span>
        </button>

        <button type="button" class="btn header-item waves-effect btn-warning" data-bs-toggle="modal"
            data-bs-target="#onHoldSalesModal">
            <span class="fs-5">On Hold Sales</span>
        </button>

        <button type="button" class="btn header-item waves-effect btn-secondary" data-bs-toggle="modal"
            data-bs-target="#transferInventoryModal">
            <span class="fs-5">Transfer Inventory</span>
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