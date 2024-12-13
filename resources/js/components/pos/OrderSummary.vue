<template>
    <div class="card">
        <div class="card-body">
            <h4>Order Summary</h4>

            <div class="d-flex justify-content-between">
                <h5>Sale Items</h5>
                <h5>{{ this.orderItems.length }}</h5>
            </div>

            <div class="d-flex justify-content-between" v-if="this.returnItems.length">
                <h5>Return Items</h5>
                <h5>{{ this.returnItems.length }}</h5>
            </div>

            <div class="d-flex justify-content-between">
                <h5>Grand Total</h5>
                <h5>{{ grandTotal() }}</h5>
            </div>


            <div class="d-flex justify-content-between" v-for="payment in paymentInfo">
                <h5>Paid by {{ payment.method }}</h5>
                <h5>£{{ payment.amount.toFixed(2) }}</h5>
            </div>

            <div class="d-flex justify-content-between">
                <h5>Pending Balance</h5>
                <h5>{{ amountToBePaid(false) }}</h5>
            </div>

            <div class="row">
                <!-- Hold Sale Button -->
                <div class="col-6 mb-2 pe-1" v-if="this.orderItems.length || this.returnItems.length">
                    <button class="btn btn-warning w-100" data-bs-toggle="modal" data-bs-target="#holdSaleModal">
                        <i class="mdi mdi-pause font-size-14 me-1"></i>
                        Hold Sale
                    </button>
                </div>

                <!-- Cancel Button -->
                <div class="col-6 mb-2 pe-1" v-if="this.orderItems.length || this.returnItems.length">
                    <button class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#cancelSaleModal">
                        <i class="mdi mdi-close font-size-14 me-1"></i>
                        Cancel
                    </button>
                </div>

                <!-- Layway Button -->
                <div class="col-6 mb-2 pe-1" v-if="this.orderItems.length || this.returnItems.length">
                    <button class="btn btn-secondary w-100" @click="handleActionClick('Layway')">
                        <i class="mdi mdi-account-cash font-size-14 me-1"></i>
                        Layway
                    </button>
                </div>

                <!-- Return Sale Button -->
                <div class="col-6 mb-2 pe-1">
                    <button class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#returnSaleModal">
                        <i class="mdi mdi-keyboard-return font-size-14 me-1"></i>
                        Return Sale
                    </button>
                </div>

                <!-- Gift Voucher Button -->
                <div class="col-6 mb-2 pe-1">
                    <button class="btn btn-dark w-100" data-bs-toggle="modal" data-bs-target="#giftVoucherModal">
                        <i class="mdi mdi-gift font-size-14 me-1"></i>
                        Gift Voucher
                    </button>
                </div>

                <!-- EOD Button -->
                <div class="col-6 mb-2 pe-1">
                    <button class="btn btn-info w-100" @click="handleActionClick('EOD')">
                        <i class="mdi mdi-printer font-size-14 me-1"></i>
                        EOD
                    </button>
                </div>

                <!-- Tender Button -->
                <div class="col-12 mb-2 pe-1" v-if="amountToBePaid() > 0">
                    <button class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#tenderModal">
                        <i class="mdi mdi-cash-plus font-size-14 me-1"></i>
                        Tender
                    </button>
                </div>
                <div class="col-12 mb-2 pe-1" v-else-if="amountToBePaid() < 0">
                    <button class="btn btn-success w-100" @click="handleActionClick('Credit Notes')">
                        <i class="mdi mdi-notebook-edit-outline font-size-14 me-1"></i>
                        Credit Notes
                    </button>
                </div>
                <div class="col-12 mb-2 pe-1" v-else>
                    <button class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#finishSaleModal">
                        <i class="mdi mdi-check-all font-size-14 me-1"></i>
                        Finish
                    </button>
                </div>
            </div>
        </div>
    </div>

    <ReturnSaleModal @returnItemConfirmed="returnItem" />
    <HoldSaleModal @holdSaleConfirmed="holdSale" />
    <CancelSaleModal @cancelSaleConfirmed="cancelSale" />
    <TenderModal :paymentInfo="paymentInfo" :amountToBePaid="String(amountToBePaid())"
        @capturePaymentConfirmed="capturePayment" />
    <GiftVoucherModal />
    <FinishSaleModal @finishSaleConfirmed="finishSale" />
</template>

<script>
import ReturnSaleModal from './ReturnSaleModal.vue';
import HoldSaleModal from './HoldSaleModal.vue';
import CancelSaleModal from './CancelSaleModal.vue';
import TenderModal from './TenderModal.vue';
import GiftVoucherModal from './GiftVoucherModal.vue';
import FinishSaleModal from './FinishSaleModal.vue';

export default {
    components: {
        ReturnSaleModal,
        HoldSaleModal,
        CancelSaleModal,
        TenderModal,
        GiftVoucherModal,
        FinishSaleModal
    },
    props: {
        orderItems: Array,
        returnItems: Array,
    },
    emits: ['cancelSale', 'returnItem', 'finishSale', 'placeOrder', 'holdSale', 'capturePaymentConfirmed', 'returnItem'],
    data() {
        return {
            paymentInfo: localStorage.getItem('paymentInfo') ? JSON.parse(localStorage.getItem('paymentInfo')) : []
        };
    },
    methods: {
        returnItem(item) {
            this.$emit('returnItem', item);
        },
        amountToBePaid(isMoneyFormat = true) {
            if (!isMoneyFormat) {
                return `£${this.grandTotal(false) - parseFloat(this.getTotalPaidAmount()).toFixed(2)}`
            }

            return this.grandTotal(false) - parseFloat(this.getTotalPaidAmount());
        },
        cancelSale() {
            this.$emit('cancelSale');
        },
        holdSale() {
            this.$emit('holdSale');
        },
        finishSale() {
            this.$emit('finishSale');
        },
        capturePayment(payment) {
            const existingPayment = this.paymentInfo.find(item => item.method === payment.method);

            if (existingPayment) {
                existingPayment.amount += payment.amount;
            } else {
                this.paymentInfo.push({ method: payment.method, amount: payment.amount });
            }

            localStorage.setItem('paymentInfo', JSON.stringify(this.paymentInfo));

            if(this.amountToBePaid()==0){
                this.$emit('placeOrder');
            }
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
        },
        getTotalPaidAmount() {
            return this.paymentInfo.reduce((total, payment) => total + payment.amount, 0);
        },
        grandTotal(isMoneyFormat = true) {
            let orderItemsTotal = this.orderItems.reduce((sum, item) => {
                return sum + (item.changedPrice?.amount || item.price);
            }, 0);

            let returnItemsTotal = this.returnItems.reduce((sum, item) => {
                return sum + (item.changedPrice?.amount || item.price);
            }, 0);

            if (!isMoneyFormat) {
                return `${(orderItemsTotal - returnItemsTotal).toFixed(2)}`;
            }

            return `£${(orderItemsTotal - returnItemsTotal).toFixed(2)}`;
        },
    }
};
</script>