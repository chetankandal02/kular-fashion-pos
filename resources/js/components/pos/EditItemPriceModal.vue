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
                            <input type="number" class="form-control" id="itemPrice" v-model="priceModel"
                                @keyup="handlePriceChange" placeholder="Enter new price"
                                :class="{ 'is-invalid': priceError }" />
                            <span v-if="priceError" class="invalid-feedback">{{ priceError }}</span>
                        </div>
                                                
                        <div class="mb-3">
                            <label for="priceReason" class="form-label">Reason</label>
                            <div class="row mb-2" v-if="changePriceReasons.length > 0">
                                <div class="col-6 col-sm-4" v-for="reason in changePriceReasons">
                                    <div class="form-check">
                                        <input v-model="selectedReasonId" :value="reason.id" class="form-check-input"
                                            name="reason" type="radio" id="reason-{{ reason.id }}"
                                            :class="{ 'is-invalid': errorSelectedReason }">

                                        <label class="form-check-label" for="reason-{{ reason.id }}">
                                            {{ reason.name }}
                                        </label>
                                    </div>
                                </div>

                                <div class="col-6 col-sm-4">
                                    <div class="form-check">
                                        <input v-model="selectedReasonId" :value="0" class="form-check-input"
                                            name="reason" type="radio" id="other-reason"
                                            :class="{ 'is-invalid': errorSelectedReason }">
                                        <label class="form-check-label" for="other-reason">Other</label>
                                    </div>
                                </div>
                            </div>

                            <textarea class="form-control" id="priceReason" v-if="!selectedReasonId || selectedReasonId===0" v-model="localSelectedItem.reason"
                                placeholder="Enter the reason for the price change"
                                :class="{ 'is-invalid': errorReason || errorSelectedReason }"></textarea>

                            <span v-if="errorReason || errorSelectedReason" class="invalid-feedback" :class="{ 'd-block': errorSelectedReason }">Reason is required.</span>
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
            priceError: '',
            selectedReasonId: '',
            errorSelectedReason: false
        };
    },
    watch: {
        selectedItem: {
            immediate: true,
            handler(newValue) {
                if(newValue?.changedPrice){
                    this.selectedReasonId = newValue.changedPrice?.reasonId;
                } else {
                    this.selectedReasonId = '';
                }

                this.localSelectedItem = { ...newValue };
            },
            deep: true
        }
    },
    props: {
        selectedItem: {
            type: Object
        },
        changePriceReasons: {
            type: Array
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
            if (!this.selectedReasonId && !this.localSelectedItem.reason) {
                this.errorSelectedReason = true;
            } else {
                this.errorSelectedReason = false;
            }

            if ((!this.localSelectedItem.reason || this.localSelectedItem.reason.trim() === '') && this.selectedReasonId === 0) {
                this.errorReason = true;
            } else {
                this.errorReason = false;
            }


            this.handlePriceChange();

            if (!this.errorReason && !this.errorSelectedReason && !this.priceError) {
                const updatedItem = {
                    ...this.localSelectedItem,
                    changedPrice: {
                        amount: this.priceModel,
                        reason: this.selectedReasonId === 0 || !this.selectedReasonId ? this.localSelectedItem.reason : '',
                        reasonId: this.selectedReasonId
                    }
                };

                this.$emit('save-price-change', updatedItem);

                bootstrap.Modal.getInstance($('#editPriceModal')).hide();
            }
        }
    }
};
</script>
