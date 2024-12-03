<template>
    <div class="card">
        <div class="card-body">
            <h4>Order Summary</h4>

            <div class="d-flex justify-content-between">
                <h5>Sub Total</h5>
                <h5>{{ subtotal }}</h5>
            </div>

            <div class="d-flex justify-content-between">
                <h5>Total Items</h5>
                <h5>{{ totalItems }}</h5>
            </div>

            <div class="d-flex justify-content-between">
                <h5>Grand Total</h5>
                <h5>{{ grandTotal }}</h5>
            </div>

            <div class="row">
                <!-- Hold Sale Button -->
                <div class="col-6 mb-2 pe-1" v-if="totalItems > 0">
                    <button class="btn btn-warning w-100" data-bs-toggle="modal" data-bs-target="#holdSaleModal">
                        <i class="mdi mdi-pause font-size-14 me-1"></i>
                        Hold Sale
                    </button>
                </div>

                <!-- Cancel Button -->
                <div class="col-6 mb-2 pe-1" v-if="totalItems > 0">
                    <button class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#cancelSaleModal">
                        <i class="mdi mdi-close font-size-14 me-1"></i>
                        Cancel
                    </button>
                </div>

                <!-- Layway Button -->
                <div class="col-6 mb-2 pe-1" v-if="totalItems > 0">
                    <button class="btn btn-secondary w-100" @click="handleActionClick('Layway')">
                        <i class="mdi mdi-account-cash font-size-14 me-1"></i>
                        Layway
                    </button>
                </div>

                <!-- Credit Notes Button -->
                <div class="col-6 mb-2 pe-1" v-if="totalItems > 0">
                    <button class="btn btn-dark w-100" @click="handleActionClick('Credit Notes')">
                        <i class="mdi mdi-notebook-edit-outline font-size-14 me-1"></i>
                        Credit Notes
                    </button>
                </div>

                <!-- Tender Button -->
                <div class="col-6 mb-2 pe-1" v-if="totalItems > 0">
                    <button class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#tenderModal">
                        <i class="mdi mdi-cash-plus font-size-14 me-1"></i>
                        Tender
                    </button>
                </div>

                <!-- Return Sale Button -->
                <div class="col-6 mb-2 pe-1">
                    <button class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#returnSaleModal">
                        <i class="mdi mdi-keyboard-return font-size-14 me-1"></i>
                        Return Sale
                    </button>
                </div>

                <!-- EOD Button -->
                <div class="col-6 mb-2 pe-1">
                    <button class="btn btn-info w-100" @click="handleActionClick('EOD')">
                        <i class="mdi mdi-printer font-size-14 me-1"></i>
                        EOD
                    </button>
                </div>

                <!-- Gift Voucher Button -->
                <div class="col-6 mb-2 pe-1">
                    <button class="btn btn-dark w-100" data-bs-toggle="modal" data-bs-target="#giftVoucherModal">
                        <i class="mdi mdi-gift font-size-14 me-1"></i>
                        Gift Voucher
                    </button>
                </div>
            </div>
        </div>
    </div>

    <ReturnSaleModal @returnItemConfirmed="returnItem" />
    <HoldSaleModal />
    <CancelSaleModal @cancelSaleConfirmed="cancelSale" />
    <TenderModal />
    <GiftVoucherModal />
</template>

<script>
import ReturnSaleModal from './ReturnSaleModal.vue';
import HoldSaleModal from './HoldSaleModal.vue';
import CancelSaleModal from './CancelSaleModal.vue';
import TenderModal from './TenderModal.vue';
import GiftVoucherModal from './GiftVoucherModal.vue';

export default {
    components: {
        ReturnSaleModal,
        HoldSaleModal,
        CancelSaleModal,
        TenderModal,
        GiftVoucherModal
    },
    props: {
        orderItems: Array,
        returnItems: Array,
    },
    emits: ['cancelSale', 'returnItem'],
    data() {
        return {
            //
        };
    },
    methods: {
        returnItem(item){
            this.$emit('returnItem', item);
        },
        cancelSale(){
            this.$emit('cancelSale');
        },
        handleActionClick(actionName) {
            switch (actionName) {
                case 'Hold Sale':
                    this.holdOrder();
                    break;
                case 'Layway':
                    this.laywayOrder();
                    break;
                case 'Credit Notes':
                    this.creditNotes();
                    break;
                case 'EOD':
                    this.endOfDay();
                    break;
                default:
                    console.log(`Unknown action: ${actionName}`);
                    break;
            }
        }
    },
    computed: {
        subtotal() {
            return this.orderItems.reduce((sum, item) => sum + item.price, 0);
        },
        totalItems() {
            return this.orderItems.length;
        },
        grandTotal() {
            return this.subtotal;
        },
    },
};
</script>