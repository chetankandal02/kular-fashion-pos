<template>
    <div class="modal fade" id="returnSaleModal" tabindex="-1" aria-labelledby="returnSaleModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="returnSaleModalLabel">Return Sale</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="search-box mb-2">
                        <div class="position-relative">
                            <input type="number" v-model="query" class="form-control" ref="barcodeInput"
                                :class="{ 'is-invalid': barcodeInvalid }" placeholder="Enter barcode" @input="returnItem">
                            <i class="bx bx-barcode search-icon"></i>
                            <span v-if="barcodeInvalid" class="invalid-feedback">Barcode must be 13 characters
                                long.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            query: '',
            barcodeInvalid: false
        }
    },
    mounted() {
        const modalElement = document.getElementById('returnSaleModal');
        modalElement.addEventListener('shown.bs.modal', () => {
            this.$refs.barcodeInput.focus(); 
        });
    },
    beforeDestroy() {
        const modalElement = document.getElementById('returnSaleModal');
        modalElement.removeEventListener('shown.bs.modal', this.focusInput);
    },
    methods: {
        async returnItem() {
            if (this.query.toString().length === 13) {
                const barcode = this.query;
                this.query = '';
                const response = await axios.get(`/validate-item/${barcode}`);
                const { product } = response.data;
                if (product) {
                    this.barcodeInvalid = false;
                    this.$emit('return-item-confirmed', product);

                    const myModalEl = $('#returnSaleModal');
                    const modal = bootstrap.Modal.getInstance(myModalEl);
                    modal.hide();
                    this.query = '';
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Product Barcode is invalid.',
                        icon: 'error',
                        confirmButtonText: 'Okay'
                    });
                }
            }
        }
    }
};
</script>