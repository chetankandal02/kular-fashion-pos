<template>
    <div class="modal fade" id="tenderModal" tabindex="-1" aria-labelledby="tenderModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tenderModalLabel">Tender</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6 col-md-4 mb-3">
                            <button type="button" class="btn btn-dark waves-effect waves-light w-100">
                                <i class="mdi mdi-cash-multiple d-block font-size-16"></i> Cash
                            </button>
                        </div>
                        <div class="col-6 col-md-4 mb-3">
                            <button type="button" class="btn btn-dark waves-effect waves-light w-100">
                                <i class="mdi mdi-credit-card-outline d-block font-size-16"></i> Card
                            </button>
                        </div>
                        <div class="col-6 col-md-4 mb-3">
                            <button type="button" class="btn btn-dark waves-effect waves-light w-100">
                                <i class="mdi mdi-currency-eur d-block font-size-16"></i> Euro
                            </button>
                        </div>
                        <div class="col-6 col-md-4 mb-3">
                            <button type="button" class="btn btn-dark waves-effect waves-light w-100">
                                <i class="mdi mdi-gift-outline d-block font-size-16"></i> Git Voucher
                            </button>
                        </div>
                        <div class="col-6 col-md-4 mb-3">
                            <button type="button" class="btn btn-dark waves-effect waves-light w-100">
                                <i class="mdi mdi-note-minus-outline d-block font-size-16"></i> Credit Note
                            </button>
                        </div>
                        <div class="col-6 col-md-4 mb-3">
                            <button type="button" class="btn btn-dark waves-effect waves-light w-100">
                                <i class="mdi mdi-note-text-outline d-block font-size-16"></i> Deposit Note
                            </button>
                        </div>
                        <div class="col-6 col-md-4">
                            <button type="button" class="btn btn-dark waves-effect waves-light w-100">
                                <i class="mdi mdi-wallet-giftcard d-block font-size-18"></i> Gift Card
                            </button>
                        </div>
                    </div>
                </div>
                <!-- <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div> -->
            </div>
        </div>
    </div>
</template>

<script>
export default {
    methods: {
        /* closeTenderModal() {
            const myModalEl = document.getElementById('tenderModal');
            const modal = bootstrap.Modal.getInstance(myModalEl);
            modal.hide();
        } */
    }
};
</script>