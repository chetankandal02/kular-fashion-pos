<template>
    <div class="modal fade" id="tenderMethodModal" tabindex="-1" aria-labelledby="tenderMethodModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tenderMethodModalLabel">{{ selectedMethod }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="search-box mb-2">
                        <VirtualNumberKeyboard ref="virtualKeyboard"
                            v-if="selectedMethod === 'Cash' || selectedMethod === 'Card' || selectedMethod === 'Euro'"
                            :inputValue="initialAmount" @on-change="changeAmount" @on-submit="savePaymentInfo" />

                        <VirtualNumberKeyboard ref="virtualKeyboard" v-else variant="barcode" :inputValue="barcode"
                            @on-change="changeBarcode" @on-submit="savePaymentInfo" />
                    </div>
                </div>
                <div class="modal-footer"
                    v-if="selectedMethod === 'Cash' || selectedMethod === 'Card' || selectedMethod === 'Euro'">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" @click="savePaymentInfo">Save</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import VirtualNumberKeyboard from '../VirtualNumberKeyboard.vue';

export default {
    props: {
        selectedMethod: String,
        amountToBePaid: String
    },
    components: {
        VirtualNumberKeyboard
    },
    data() {
        return {
            amount: this.initialAmount,
            barcode: ''
        }
    },
    computed: {
        initialAmount() {
            return this.amountToBePaid || '';
        }
    },
    mounted() {
        const tenderMethodModal = document.getElementById('tenderMethodModal');
        tenderMethodModal.addEventListener('hidden.bs.modal', this.openTenderModal);
        tenderMethodModal.addEventListener('shown.bs.modal', this.focusInput);
    },
    beforeDestroy() {
        const tenderMethodModal = document.getElementById('tenderMethodModal');
        tenderMethodModal.removeEventListener('hidden.bs.modal', this.openTenderModal);
        tenderMethodModal.removeEventListener('shown.bs.modal', this.focusInput);
    },
    methods: {
        openTenderModal() {
            const tenderModal = new bootstrap.Modal(document.getElementById('tenderModal'));
            tenderModal.show();
        },
        changeAmount(value) {
            this.amount = value;
        },
        changeBarcode(value) {
            this.barcode = value;
            if (this.barcode.length === 13) {
                this.savePaymentInfo();
            }
        },
        makePayment(amount) {
            let paymentInfo = {
                method: this.selectedMethod,
                amount: parseFloat(amount)
            }
            this.$emit('onPaymentDone', paymentInfo);
            this.openTenderModal();
        },
        async payWithGiftCard(barcode) {
            const response = await axios.post('/api/gift-voucher/validate', {
                barcode: barcode
            });

            if (response.data.success) {
                this.makePayment(response.data.gift.amount);
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: response.data.message,
                    icon: 'error',
                    confirmButtonText: 'Okay'
                });
            }
        },
        payWithCreditNote(barcode) {

        },
        payWithDepositNote(barcode) {

        },
        savePaymentInfo() {
            if (this.selectedMethod === 'Cash' || this.selectedMethod === 'Card' || this.selectedMethod === 'Euro') {
                let paymentInfo = {
                    method: this.selectedMethod,
                    amount: parseFloat(this.amount || this.initialAmount)
                }
                this.$emit('onPaymentDone', paymentInfo);
                this.openTenderModal();
            } else if (String(this.barcode).length === 13) {
                if (this.selectedMethod === 'Gift Voucher') {
                    this.payWithGiftCard(this.barcode);
                } else if (this.selectedMethod === 'Credit Note') {
                    this.payWithCreditNote(this.barcode);
                } else {
                    this.payWithDepositNote(this.barcode);
                }
                this.barcode = '';
            }
        },
        focusInput() {
            if (this.$refs.virtualKeyboard && this.$refs.virtualKeyboard.$refs.input) {
                this.$refs.virtualKeyboard.$refs.input.focus();
            }
        }
    }
};
</script>