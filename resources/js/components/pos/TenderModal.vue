<template>
    <div class="modal fade" id="tenderModal" tabindex="-1" aria-labelledby="tenderModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tenderModalLabel">Tender</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Dynamic buttons using v-for -->
                        <div v-for="(method, index) in tenderMethods" :key="index" class="col-6 col-md-4 mb-3">
                            <button type="button" class="btn btn-dark waves-effect waves-light w-100"
                                @click="selectMethod(method.label)">
                                <i :class="`mdi ${method.icon} d-block font-size-16`"></i> {{ method.label }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <TenderMethodModal :amountToBePaid="String(amountToBePaid)" :selectedMethod="selectedMethod"
        @on-payment-done="onPaymentDone" />
</template>

<script>
import TenderMethodModal from './TenderMethodModal.vue';

export default {
    components: {
        TenderMethodModal
    },
    props: {
        paymentInfo: {
            type: Array,
            required: true
        },
        amountToBePaid: {
            type: String,
            required: true
        }
    },
    emits: ['capturePaymentConfirmed'],
    data() {
        return {
            tenderMethods: [
                { icon: 'mdi-cash-multiple', label: 'Cash' },
                { icon: 'mdi-credit-card-outline', label: 'Card' },
                { icon: 'mdi-currency-eur', label: 'Euro' },
                { icon: 'mdi-gift-outline', label: 'Gift Voucher' },
                { icon: 'mdi-note-minus-outline', label: 'Credit Note' },
                { icon: 'mdi-note-text-outline', label: 'Deposit Note' },
            ],
            selectedMethod: ''
        };
    },
    methods: {
        selectMethod(method){
            this.selectedMethod = method;
            
            $('.modal-backdrop').remove();
            $('#tenderModal').hide();

            const tenderMethodModal = new bootstrap.Modal(document.getElementById('tenderMethodModal'));
            tenderMethodModal.show();
        },
        onPaymentDone(payment) {
            $('.modal-backdrop').remove();
            $('#tenderMethodModal').hide();
            $('#tenderModal').show();

            this.$emit('capturePaymentConfirmed', payment);
        }
    }
};
</script>