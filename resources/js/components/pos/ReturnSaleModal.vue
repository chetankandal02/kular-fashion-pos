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
                    <input class="form-control mb-2" type="text" v-model="manualProduct.code" placeholder="Enter Article Code">
                    <input type="button" class="btn btn-primary" value="Add" @click="addReturnItem">
                </div>
            </div>
        </div>
    </div>

    <!-- Size Selection Modal -->
    <div class="modal fade" id="returnSizeSelectionModal" tabindex="-1" aria-labelledby="returnSizeSelectionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="returnSizeSelectionModalLabel">Select Size</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div v-if="selectedProduct && selectedProduct.sizes">
                        <div class="row">
                            <div v-for="size in selectedProduct.sizes" :key="size.id" class="col-md-2 text-center">
                                <div class="size-box mb-3" 
                                    :class="{ selected: size.id === selectedSize }"
                                    @click="selectSize(size.id)">
                                    {{ size.size_detail.size }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-primary" @click="confirmSizeSelection" :disabled="!selectedSize">
                        Confirm Selection
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Color Selection Modal -->
    <div class="modal fade" id="returnColorSelectionModal" tabindex="-1" aria-labelledby="returnColorSelectionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="returnColorSelectionModalLabel">Select Color</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div v-if="selectedProduct && selectedProduct.colors">
                        <div class="row">
                            <div v-for="color in selectedProduct.colors" :key="color.id" class="col-md-3 text-center">
                                <div class="color-box d-block m-auto" 
                                    :class="{ selected: color.id === selectedColor }"
                                    @click="selectColor(color.id)"
                                    :style="{ backgroundColor: color.color_detail.ui_color_code }"></div>
                                <label class="form-check-label" :for="color">{{ color.color_detail.name }}</label>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-primary mt-3" @click="confirmColorSelection" :disabled="!selectedColor">
                        Confirm Selection
                    </button>
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
                code: ''
            },
            selectedProduct: null,
            selectedSize: null,
            selectedColor: null
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
        async addReturnItem() {
            const { code } = this.manualProduct;
            if (!code) {
                Swal.fire({
                    title: 'Missing Fields',
                    text: 'Please enter both Article Code and Price.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }

            try {
                // Fetch product details by article code
                const response = await axios.get(`/get-product-by-code/${code}`);
                
                if (response.data.success && response.data.date) {

                    const productData = response.data.date;
                    const apiPrice = parseInt(productData.price);
                    this.selectedProduct = {
                        id: productData.id,
                        article_code: productData.article_code,
                        short_description: productData.short_description,
                        brand: productData.brand,
                        price: apiPrice, // Use the manually entered price
                        colors: productData.colors,
                        sizes: productData.sizes,
                        quantities: productData.quantities
                    };
                    //console.log('selectedProduct',this.selectedProduct);
                    // Reset selections
                    this.selectedSize = null;
                    this.selectedColor = null;
                    
                    // Hide return modal and show color selection first
                    $('#returnSaleModal').modal('hide');
                    
                    // If only one color, auto-select it
                    if (this.selectedProduct.colors.length === 1) {
                        this.selectedColor = this.selectedProduct.colors[0].id;
                        $('#returnSizeSelectionModal').modal('show');
                    } else {
                        $('#returnColorSelectionModal').modal('show');
                    }
                } else {
                    Swal.fire({
                        title: 'Not Found',
                        text: 'Product not found with the provided article code.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            } catch (error) {
                Swal.fire({
                    title: 'Error',
                    text: 'Failed to fetch product details.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                console.error('Error fetching product:', error);
            }
        },
        selectSize(sizeId) {
            this.selectedSize = sizeId;
        },
        selectColor(colorId) {
            this.selectedColor = colorId;
            this.selectedSize = null; // Reset size selection when color changes
        },
        confirmColorSelection() {
            $('#returnColorSelectionModal').modal('hide');
            $('#returnSizeSelectionModal').modal('show');
        },
        confirmSizeSelection() {
            $('#returnSizeSelectionModal').modal('hide');
            this.finalizeReturn();
        },
        async finalizeReturn() {
            try {
                // Find the selected size and color details
                const selectedSize = this.selectedProduct.sizes.find(s => s.id === this.selectedSize);
                const selectedColor = this.selectedProduct.colors.find(c => c.id === this.selectedColor);
                
                if (!selectedSize || !selectedColor) {
                    throw new Error('Invalid size or color selection');
                }

                // Find the matching quantity record
                const quantityRecord = this.selectedProduct.quantities.find(q => 
                    q.product_size_id === this.selectedSize && 
                    q.product_color_id === this.selectedColor
                );

                // Use size_id from size_detail instead of product_size_id
                const correctSizeId = selectedSize.size_detail.id;

                // Create return product object with all required fields
                const returnProduct = {
                    id: this.selectedProduct.id,
                    product_id: this.selectedProduct.id,
                    code: this.selectedProduct.article_code,
                    description: this.selectedProduct.short_description,
                    product_quantity_id: quantityRecord ? quantityRecord.id : null,
                    brand: this.selectedProduct.brand.short_name,
                    brand_id: this.selectedProduct.brand.id,
                    color: selectedColor.color_detail.name,
                    color_id: selectedColor.id,
                    size: selectedSize.size_detail.size,
                    size_id: correctSizeId, // Use the correct size_id from size_detail
                    price: this.selectedProduct.price,
                    available_quantity: 0,
                    manufacture_barcode: quantityRecord ? quantityRecord.manufacture_barcode : null,
                    barcode: this.generateBarcode(this.selectedProduct.article_code, selectedColor.color_detail.code, selectedSize.size_detail.new_code)
                };

                this.$emit('return-item-confirmed', returnProduct);
                
                // Reset form
                this.manualProduct.code = '';
                this.manualProduct.price = null;
                this.selectedProduct = null;
                this.selectedSize = null;
                this.selectedColor = null;
            } catch (error) {
                console.error('Error finalizing return:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'Failed to process return. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        },
        getManufactureBarcode(sizeId, colorId) {
            if (!this.selectedProduct.quantities) return null;
            const item = this.selectedProduct.quantities.find(q => 
                q.product_size_id === sizeId && 
                q.product_color_id === colorId
            );
            return item ? item.manufacture_barcode : null;
        },
        generateBarcode(articleCode, colorCode, sizeCode) {
            // Simple barcode generation logic - adjust as needed
            return `${articleCode}${colorCode}${sizeCode}${Math.floor(Math.random() * 10)}`;
        }
    }
};
</script>

<style scoped>
.size-box {
    border: 1px solid #ddd;
    padding: 10px;
    cursor: pointer;
    border-radius: 4px;
    transition: all 0.2s;
}

.size-box:hover {
    background-color: #f8f9fa;
}

.size-box.selected {
    background-color: #0d6efd;
    color: white;
    border-color: #0d6efd;
}

.color-box {
    width: 30px;
    height: 30px;
    border: 1px solid #ddd;
    border-radius: 50%;
    cursor: pointer;
    margin-bottom: 5px;
    transition: all 0.2s;
}

.color-box:hover {
    transform: scale(1.1);
}

.color-box.selected {
    border: 2px solid #0d6efd;
    box-shadow: 0 0 5px rgba(13, 110, 253, 0.5);
}
</style>