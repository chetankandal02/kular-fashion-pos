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
                <h5>
                    <span v-if="payment.method === 'Euro'">€</span>
                    <span v-else>£</span>
                    {{ payment.amount.toFixed(2) }}
                </h5>
            </div>

            <div class="d-flex justify-content-between" v-if="paymentInfo.length > 0">
                <h5>Pending Balance</h5>
                <h5>{{ amountToBePaid(false) }}</h5>
            </div>

            <div class="row">
                <!-- Hold Sale Button -->
                <div class="col-6 mb-2 pe-1" v-if="this.orderItems.length || this.returnItems.length">
                    <button class="btn btn-warning w-100 fs-4 py-4" data-bs-toggle="modal" data-bs-target="#holdSaleModal">
                        <i class="mdi mdi-pause me-1"></i>
                        Hold Sale
                    </button>
                </div>

                <!-- Cancel Button -->
                <div class="col-6 mb-2 pe-1" v-if="this.orderItems.length || this.returnItems.length">
                    <button class="btn btn-danger w-100 fs-4 py-4" data-bs-toggle="modal" data-bs-target="#cancelSaleModal">
                        <i class="mdi mdi-close me-1"></i>
                        Cancel
                    </button>
                </div>

                <!-- Layaway Button -->
                <div class="col-6 mb-2 pe-1" v-if="this.orderItems.length || this.returnItems.length">
                    <button class="btn btn-secondary w-100 fs-4 py-4" data-bs-toggle="modal" data-bs-target="#layawayModal">
                        <i class="mdi mdi-account-cash me-1"></i>
                        Layaway
                    </button>
                </div>

                <!-- Return Sale Button -->
                <div class="col-6 mb-2 pe-1">
                    <button class="btn btn-danger w-100 fs-4 py-4" data-bs-toggle="modal" data-bs-target="#returnSaleModal">
                        <i class="mdi mdi-keyboard-return me-1"></i>
                        Return Sale
                    </button>
                </div>

                <!-- Gift Voucher Button -->
                <div class="col-6 mb-2 pe-1">
                    <button class="btn btn-dark w-100 fs-4 py-4" data-bs-toggle="modal" data-bs-target="#giftVoucherModal">
                        <i class="mdi mdi-gift me-1"></i>
                        Gift Voucher
                    </button>
                </div>

                <!-- EOD Button -->
                <div class="col-6 mb-2 pe-1">
                    <button class="btn btn-info w-100 fs-4 py-4" data-bs-toggle="modal" data-bs-target="#menuModal">
                        <i class="mdi mdi-menu me-1"></i>
                        Menu
                    </button>
                    <!-- <button class="btn btn-info w-100" data-bs-toggle="modal" data-bs-target="#eodModal">
                        <i class="mdi mdi-printer font-size-14 me-1"></i>
                        EOD
                    </button> -->
                </div>

                <!-- Tender Button -->
                <div class="col-12 mb-2 pe-1" v-if="amountToBePaid() > 0">
                    <button class="btn btn-success w-100 fs-4 py-4" data-bs-toggle="modal" data-bs-target="#tenderModal">
                        <i class="mdi mdi-cash-plus me-1"></i>
                        Tender
                    </button>
                </div>
                <div class="col-12 mb-2 pe-1" v-else-if="amountToBePaid() < 0">
                    <button class="btn btn-success w-100 fs-4 py-4" @click="handleActionClick('Credit Note')">
                        <i class="mdi mdi-notebook-edit-outline me-1"></i>
                        Credit Note
                    </button>
                </div>
                <div class="col-12 mb-2 pe-1" v-else-if="this.orderItems.length || this.returnItems.length">
                    <button class="btn btn-success w-100 fs-4 py-4" data-bs-toggle="modal" data-bs-target="#finishSaleModal">
                        <i class="mdi mdi-check-all me-1"></i>
                        Finish
                    </button>
                </div>
            </div>
        </div>
    </div>

    <ReturnSaleModal @returnItemConfirmed="returnItem" />
    <HoldSaleModal @holdSaleConfirmed="holdSale" />
    <CancelSaleModal @cancelSaleConfirmed="cancelSale" />
    <TenderModal :paymentInfo="paymentInfo" :shouldPlaceOrder="true" :amountToBePaid="String(amountToBePaid())"
        @capturePaymentConfirmed="capturePayment" />
    <GiftVoucherModal />
    <FinishSaleModal @finishSaleConfirmed="finishSale" />
    <MenuModal></MenuModal>
    <EODModal></EODModal>
    <LayawayModal :grandTotal="parseFloat(grandTotal(false))" :pendingBalance="parseFloat(amountToBePaid(false))" />
</template>

<script>
import ReturnSaleModal from './ReturnSaleModal.vue';
import HoldSaleModal from './HoldSaleModal.vue';
import CancelSaleModal from './CancelSaleModal.vue';
import TenderModal from './TenderModal.vue';
import GiftVoucherModal from './GiftVoucherModal.vue';
import FinishSaleModal from './FinishSaleModal.vue';
import LayawayModal from './LayawayModal.vue';
import EODModal from './EODModal.vue';
import MenuModal from './menuModal.vue';
import axios from 'axios';

export default {
    components: {
        ReturnSaleModal,
        HoldSaleModal,
        CancelSaleModal,
        TenderModal,
        GiftVoucherModal,
        FinishSaleModal,
        LayawayModal,
        EODModal,
        MenuModal
    },
    props: {
        orderItems: Array,
        returnItems: Array,
        paymentInfo: Array
    },
    emits: ['cancelSale', 'returnItem', 'finishSale', 'placeOrder', 'creditNote', 'holdSale', 'capturePaymentConfirmed', 'returnItem'],
    methods: {
        returnItem(item) {
            this.$emit('returnItem', item);
        },
        amountToBePaid(isMoneyFormat = true) {
            const grandTotal = parseFloat(this.grandTotal(false));
            const totalPaidAmount = parseFloat(this.getTotalPaidAmount());
            const amountDue = grandTotal - totalPaidAmount;

            if (!isMoneyFormat) {
                return `£${amountDue.toFixed(2)}`;
            }

            return `${amountDue.toFixed(2)}`;
        },
        cancelSale() {
            this.$emit('cancelSale');
        },
        holdSale(customerName) {
            this.$emit('holdSale', customerName);
        },
        finishSale() {
            this.$emit('finishSale');
        },
        async creditNote() {
            const response = await axios.post('/api/credit-note', {
                amount: this.amountToBePaid(),
                auth_user_id: window.config.userId
            });

            if (response.data.success) {
                this.capturePayment({
                    method: 'Credit Note',
                    amount: parseFloat(this.amountToBePaid())
                });
            }
        },
        capturePayment(payment) {
            const existingPayment = this.paymentInfo.find(item => item.method === payment.method);

            if (existingPayment) {
                existingPayment.amount += payment.amount;
            } else {
                this.paymentInfo.push({ method: payment.method, amount: payment.amount });
            }

            localStorage.setItem('paymentInfo', JSON.stringify(this.paymentInfo));

            if (this.amountToBePaid() <= 0) {
                $('.modal-backdrop').remove();
                
                setTimeout(() => {
                    if (bootstrap.Modal.getInstance($('#tenderModal'))) {
                        bootstrap.Modal.getInstance($('#tenderModal')).hide();
                    }
                }, 750);

                if (bootstrap.Modal.getInstance($('#tenderMethodModal'))) {
                    bootstrap.Modal.getInstance($('#tenderMethodModal')).hide();
                }

                if (parseFloat(this.amountToBePaid()) < 0) {
                    this.creditNote();
                }

                if (this.amountToBePaid() == 0) {
                    this.$emit('placeOrder');
                }
            } else {
                $('.modal-backdrop').remove();
                bootstrap.Modal.getInstance($('#tenderMethodModal')).hide();
                bootstrap.Modal.getInstance($('#tenderModal')).show();
            }
        },
        handleActionClick(actionName) {
            switch (actionName) {
                case 'Credit Note':
                    this.creditNote();
                    break;
                default:
                    console.log(`Unknown action: ${actionName}`);
                    break;
            }
        },
        getTotalPaidAmount() {
            const euroToPound = window.config.euro_to_pound || 0;

            return this.paymentInfo.reduce((total, payment) => {
                if (payment.method === 'Euro') {
                    let amountInPound = parseFloat(payment.amount) * parseFloat(euroToPound) || 0;
                    return total + amountInPound;
                }

                return total + parseFloat(payment.amount);
            }, 0);
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