<template>
    <div class="modal fade" id="editPriceModal" tabindex="-1" aria-labelledby="editPriceModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPriceModalLabel">Edit Item Price</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div v-if="localSelectedItem">
                        <div class="mb-3">
                            <label for="itemPrice" class="form-label">Price</label>
                            <input
                                type="number"
                                class="form-control"
                                id="itemPrice"
                                v-model="priceModel"
                                @keyup="handlePriceChange"
                                placeholder="Enter new price"
                                :class="{ 'is-invalid': priceError }"
                            />
                            <span v-if="priceError" class="invalid-feedback">{{ priceError }}</span>
                        </div>
                        <div class="mb-3">
                            <label for="priceReason" class="form-label">Reason</label>
                            <textarea
                                class="form-control"
                                id="priceReason"
                                v-model="localSelectedItem.reason"
                                placeholder="Enter the reason for the price change"
                                :class="{ 'is-invalid': errorReason }"
                            ></textarea>
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
    data() {
        return {
            localSelectedItem: { ...this.selectedItem },
            errorReason: false,
            priceError: ''
        };
    },
    watch: {
        selectedItem: {
            immediate: true,
            handler(newValue) {
                this.localSelectedItem = { ...newValue };
            },
            deep: true
        }
    },
    props: {
        selectedItem: {
            type: Object
        }
    },
    computed: {
        priceModel: {
            get() {
                return this.localSelectedItem.changedPrice
                    ? this.localSelectedItem.changedPrice.amount
                    : this.localSelectedItem.price;
            },
            set(value) {
                this.localSelectedItem.changedPrice = { amount: value };
                this.handlePriceChange();
            }
        }
    },
    methods: {
        handlePriceChange() {
            if (this.priceModel === this.localSelectedItem.price) {
                this.priceError = 'Please enter a new price.';
            } else {
                this.priceError = '';
            }
        },
        savePriceChange() {
            if (!this.localSelectedItem.reason || this.localSelectedItem.reason.trim() === '') {
                this.errorReason = true;
            } else {
                this.errorReason = false;
            }

            this.handlePriceChange();

            if (!this.errorReason && !this.priceError) {
                const updatedItem = {
                    ...this.localSelectedItem,
                    changedPrice: {
                        amount: this.priceModel,
                        reason: this.localSelectedItem.reason
                    }
                };

                this.$emit('save-price-change', updatedItem);

                const modal = bootstrap.Modal.getInstance(document.getElementById('editPriceModal'));
                modal.hide();
            }
        }
    }
};
</script>
