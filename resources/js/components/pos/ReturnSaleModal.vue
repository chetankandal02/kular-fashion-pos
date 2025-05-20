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
                        <VirtualNumberKeyboard 
                            ref="virtualKeyboard"
                            variant="barcode"
                            :inputValue="query" 
                            @on-change="returnItem" />
                    </div>
                    <label>If Barcode not available</label>
                    <input class="form-control mb-2" type="text" v-model="manualProduct.code" placeholder="Enter Artical Code">
                    <input class="form-control mb-2" type="number" v-model.number="manualProduct.price" placeholder="Enter Price">
                    <input type="button" class="btn btn-primary" value="Add" @click="addReturnItem">
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
            query: '',
            barcodeInvalid: false,
            manualProduct: {
                code: '',
                price: null
            }
        }
    },
    components: {
        VirtualNumberKeyboard
    },
    mounted() {
        const modalElement = document.getElementById('returnSaleModal');
        modalElement.addEventListener('shown.bs.modal', this.focusInput);
    },
    beforeDestroy() {
        const modalElement = document.getElementById('returnSaleModal');
        modalElement.removeEventListener('shown.bs.modal', this.focusInput);
    },
    methods: {
        async returnItem(barcode) {
            if (barcode.toString().length === 13) {
                const response = await axios.get(`/validate-item/${barcode}`);
                const { product } = response.data;
                if (product) {
                    this.barcodeInvalid = false;
                    this.$emit('return-item-confirmed', product);

                    const myModalEl = $('#returnSaleModal');
                    const modal = bootstrap.Modal.getInstance(myModalEl);
                    modal.hide();
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Product Barcode is invalid.',
                        icon: 'error',
                        confirmButtonText: 'Okay'
                    });
                }
            }
        },
        focusInput() {
            if (this.$refs.virtualKeyboard && this.$refs.virtualKeyboard.$refs.input) {
                this.$refs.virtualKeyboard.$refs.input.focus();
            }
        },
        addReturnItem() {
            const { code, price } = this.manualProduct;
            if (!code || !price) {
                Swal.fire({
                    title: 'Missing Fields',
                    text: 'Please enter both Article Code and Price.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }

            const product = {
                code,
                price
            };

            this.$emit('return-item-confirmed', product);

            this.manualProduct.code = '';
            this.manualProduct.price = null;

            const myModalEl = $('#returnSaleModal');
            const modal = bootstrap.Modal.getInstance(myModalEl);
            modal.hide();
        }
    }
};
</script>