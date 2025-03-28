<template>
    <div class="modal fade" id="menuModal" tabindex="-1" aria-labelledby="menuModalLabel">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="menuModalLabel">Menu</h5>
                    <button type="button" class="btn-close" @click="closeModal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <button class="btn btn-dark btn-lg w-100" @click="printLastOrderReceipt">
                                <i class="mdi mdi-printer font-size-20 me-1"></i>
                                Last Receipt
                            </button>
                        </div>
                        <div class="col-md-6 mb-2">
                            <button class="btn btn-secondary btn-lg w-100" @click="printLastGiftReceipt">
                                <i class="mdi mdi-printer font-size-20 me-1"></i>
                                Gift Receipt
                            </button>
                        </div>
                        <div class="col-md-6 mb-2">
                            <button class="btn btn-info btn-lg w-100" data-bs-toggle="modal" data-bs-target="#eodModal">
                                <i class="mdi mdi-cash-multiple font-size-20 me-1"></i>
                                Running Total
                            </button>
                        </div>
                        <div class="col-md-6 mb-2">
                            <button class="btn btn-success btn-lg w-100" data-bs-toggle="modal" data-bs-target="#eodModal">
                                <i class="mdi mdi-printer font-size-20 me-1"></i>
                                EOD
                            </button>
                        </div>
                        <div class="col-md-6 mb-2">
                            <button class="btn btn-primary btn-lg w-100" data-bs-toggle="modal" data-bs-target="#recallLayawayModal">
                                <i class="mdi mdi-account-cash font-size-20 me-1"></i>
                                Recall Layaway
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="closeModal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <EODModal />
    <RecallLayawayModal />
</template>

<script>
import axios from 'axios';
import EODModal from './EODModal.vue';
import RecallLayawayModal from './RecallLayawayModal.vue';

export default {
    components: {
        EODModal,
        RecallLayawayModal
    },
    methods: {
        async printLastOrderReceipt(){
            const response = await axios.post('/api/print-last-sale-receipt');

            if (response.data.success) {
                Swal.fire({
                    title: 'Success!',
                    text: 'Last receipt printed successfully.',
                    icon: 'success',
                    confirmButtonText: 'Great!'
                });
            } else {
                alert(response.data.message || 'Something went wrong!')
            }
        },
        async printLastGiftReceipt(){
            const response = await axios.post('/api/print-gift-receipt');

            if (response.data.success) {
                Swal.fire({
                    title: 'Success!',
                    text: 'Gift voucher receipt printed successfully.',
                    icon: 'success',
                    confirmButtonText: 'Great!'
                });
            } else {
                alert(response.data.message || 'Something went wrong!')
            }
        },
        closeModal() {
            const modal = bootstrap.Modal.getInstance($('#menuModal'));
            modal.hide();
        },
    }
};
</script>