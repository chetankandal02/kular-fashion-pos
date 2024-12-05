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
                        <div class="position-relative" v-if="selectedMethod==='Cash' || selectedMethod==='Card' || selectedMethod==='Euro'">
                            <input type="number" v-model="amount" class="form-control" placeholder="Enter Amount" ref="amountInput" @keydown.enter="savePaymentInfo">
                            <i class="mdi mdi-currency-eur search-icon"></i>
                        </div>
                        <div class="position-relative" v-else>
                            <input type="number" v-model="barcode" class="form-control" placeholder="Scan barcode" @input="savePaymentInfo">
                            <i class="bx bx-barcode search-icon"></i>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" v-if="selectedMethod==='Cash' || selectedMethod==='Card' || selectedMethod==='Euro'">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" @click="savePaymentInfo">Save</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        selectedMethod: String
    },
    data(){
        return {
            amount: '',
            barcode: ''
        }
    },
    mounted() {
        const tenderMethodModal = document.getElementById('tenderMethodModal');
        tenderMethodModal.addEventListener('hidden.bs.modal', this.openTenderModal);
    },
    methods: {
        openTenderModal() {
            const tenderModal = new bootstrap.Modal(document.getElementById('tenderModal'));
            tenderModal.show();
        },
        savePaymentInfo(){
            if(this.selectedMethod==='Cash' || this.selectedMethod==='Card' || this.selectedMethod==='Euro'){
                console.log('amount', this.amount);
            } else if(String(this.barcode).length === 13){
                console.log('barcode', this.barcode);
                this.barcode = '';
            }
        }
    },
    beforeDestroy() {
        const tenderMethodModal = document.getElementById('tenderMethodModal');
        tenderMethodModal.removeEventListener('hidden.bs.modal', this.openTenderModal);
    }
};
</script>