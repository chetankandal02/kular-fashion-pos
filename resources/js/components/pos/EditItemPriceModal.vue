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
                            <input type="number" class="form-control" id="itemPrice" v-model="selectedItem.price"
                                placeholder="Enter new price" />
                        </div>
                        <div class="mb-3">
                            <label for="priceReason" class="form-label">Reason</label>
                            <textarea class="form-control" id="priceReason" v-model="selectedItem.reason"
                                placeholder="Enter the reason for the price change"></textarea>
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
    methods: {
        savePriceChange() {
            this.$emit('save-price-change', this.selectedItem);
            const modal = bootstrap.Modal.getInstance(document.getElementById('editPriceModal'));
            modal.hide();
        }
    },
};
</script>