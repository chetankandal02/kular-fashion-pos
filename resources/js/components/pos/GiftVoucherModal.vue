<template>
    <div class="modal fade" id="giftVoucherModal" tabindex="-1" aria-labelledby="giftVoucherModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="giftVoucherModalLabel">Gift Voucher</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="" class="mb-0">Payment Method</label>
                    <div class="row mb-2">
                        <div class="col-sm-4">
                            <button type="button" class="btn waves-effect waves-light w-100"
                                :class="selectedPayment === 'Cash' ? 'btn-dark' : 'btn-secondary'"
                                @click="selectPayment('Cash')">
                                <i class="mdi mdi-cash-multiple d-block font-size-16"></i> Cash
                            </button>
                        </div>
                        <div class="col-sm-4">
                            <button type="button" class="btn waves-effect waves-light w-100"
                                :class="selectedPayment === 'Card' ? 'btn-dark' : 'btn-secondary'"
                                @click="selectPayment('Card')">
                                <i class="mdi mdi-credit-card-outline d-block font-size-16"></i> Card
                            </button>
                        </div>
                        <div class="col-sm-4">
                            <button type="button" class="btn waves-effect waves-light w-100"
                                :class="selectedPayment === 'Euro' ? 'btn-dark' : 'btn-secondary'"
                                @click="selectPayment('Euro')">
                                <i class="mdi mdi-currency-eur d-block font-size-16"></i> Euro
                            </button>
                        </div>
                    </div>

                    <div class="search-box mb-2">
                        <VirtualNumberKeyboard ref="virtualKeyboard" :invalidMessage="invalidMessage"
                            @on-change="changeAmount" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" @click="generateGiftVoucher">Generate Gift
                        Voucher</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import VirtualNumberKeyboard from '../VirtualNumberKeyboard.vue';

export default {
    data() {
        return {
            amount: '',
            selectedPayment: 'Card',
            invalidMessage: ''
        };
    },
    components: {
        VirtualNumberKeyboard
    },
    mounted() {
        const modalElement = document.getElementById('giftVoucherModal');
        modalElement.addEventListener('shown.bs.modal', this.focusInput);
    },
    beforeDestroy() {
        const modalElement = document.getElementById('giftVoucherModal');
        modalElement.removeEventListener('shown.bs.modal', this.focusInput);
    },
    methods: {
        async generateGiftVoucher() {
            if (!this.amount) {
                this.invalidMessage = 'Please enter amount';
            } else {
                this.invalidMessage = '';
            }

            if (!this.invalidMessage) {
                const response = await axios.post('/api/gift-voucher', {
                    amount: this.amount,
                    payment_method: this.selectedPayment,
                    auth_user_id: window.config.userId
                });

                console.log('response', response)

                /* const myModalEl = document.getElementById('giftVoucherModal');
                bootstrap.Modal.getInstance(myModalEl).hide(); */
            }
        },
        changeAmount(value) {
            this.amount = value;
        },
        selectPayment(paymentMethod) {
            this.selectedPayment = paymentMethod;
        },
        focusInput() {
            if (this.$refs.virtualKeyboard && this.$refs.virtualKeyboard.$refs.input) {
                this.$refs.virtualKeyboard.$refs.input.focus();
            }
        }
    }
};
</script>
