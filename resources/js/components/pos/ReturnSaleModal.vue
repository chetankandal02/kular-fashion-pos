<template>
    <div class="modal fade" id="returnSaleModal" tabindex="-1" aria-labelledby="returnSaleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="returnSaleModalLabel">Return Sale</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="search-box mb-2">
                        <div class="position-relative">
                            <input type="number" v-model="query" class="form-control" :class="{'is-invalid': barcodeInvalid}" placeholder="Enter barcode">
                            <i class="bx bx-barcode search-icon"></i>
                            <span v-if="barcodeInvalid" class="invalid-feedback">Barcode must be 13 characters long.</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" @click="confirmReturnSale">Confirm Return</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            query: '',
            barcodeInvalid: false
        }
    },
    methods: {
        returnSale(){
            this.barcodeInvalid = false;
            this.$emit('return-sale-confirmed', this.query);
            
            const myModalEl = $('#returnSaleModal');
            const modal = bootstrap.Modal.getInstance(myModalEl);
            modal.hide();
            this.query = '';
        },
        confirmReturnSale() {
            let returnSaleBarCode = String(this.query);
            if (returnSaleBarCode.length !== 13) {
                this.barcodeInvalid = true;
                return;
            }

            this.returnSale();
        }
    }
};
</script>