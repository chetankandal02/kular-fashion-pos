<template>
    <div class="modal fade" id="editPriceModal" tabindex="-1" aria-labelledby="editPriceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPriceModalLabel">Edit Item Price</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div v-if="selectedItem">
                        <div class="mb-3">
                            <label for="itemPrice" class="form-label">Price</label>
                            <input type="number" class="form-control" id="itemPrice" v-model="priceModel"
                                placeholder="Enter new price"  :class="{'is-invalid': selectedItem.price==priceModel}"/>
                            <span v-if="selectedItem.price==priceModel" class="invalid-feedback">Please enter a new price.</span>
                        </div>
                        <div class="mb-3">
                            <label for="priceReason" class="form-label">Reason</label>
                            <textarea class="form-control" id="priceReason" v-model="selectedItem.reason"
                                placeholder="Enter the reason for the price change"  :class="{'is-invalid': errorReason}"></textarea>
                            <span v-if="errorReason" class="invalid-feedback">Reason is required.</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" @click="savePriceChange">Save changes</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['selectedItem'],
    data() {
        return {
            errorReason: false,
        };
    },
    computed: {
        priceModel: {
            get() {
                return this.selectedItem.changedPrice ? this.selectedItem.changedPrice.amount : this.selectedItem.price;
            },
            set(value) {
                if (this.selectedItem.changedPrice) {
                    this.selectedItem.changedPrice.amount = value;
                } else {
                    this.selectedItem.price = value;
                }
            }
        }
    },
    methods: {
        savePriceChange() {
            if (!this.selectedItem.reason || this.selectedItem.reason.trim() === '') {
                this.errorReason = true;
            } else {
                this.errorReason = false;
            }

            if (!this.errorReason && !this.selectedItem.price!=this.priceModel) {
                let items = JSON.parse(localStorage.getItem('orderItems')) || [];
                const itemIndex = items.findIndex(item => item.product_id === this.selectedItem.product_id && item.size === this.selectedItem.size);
                if (itemIndex !== -1) {
                    const updatedItem = { 
                        ...items[itemIndex], 
                        changedPrice: {
                            amount: this.selectedItem.price, 
                            reason: this.selectedItem.reason 
                        }
                    };
                    items.splice(itemIndex, 1, updatedItem);
                    this.$emit('save-price-change', this.selectedItem);
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editPriceModal'));
                    modal.hide();
                    localStorage.setItem('orderItems', JSON.stringify(items));  
                } else {
                    console.log('Item not found');
                }
            }
        }
    },
};
</script>