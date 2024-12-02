<template>
    <div class="modal fade" id="giftVoucherModal" tabindex="-1" aria-labelledby="giftVoucherModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="giftVoucherModalLabel">Gift Voucher</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="search-box mb-2">
                        <div class="position-relative">
                            <input type="number" v-model="amount" class="form-control" placeholder="Enter Amount">
                            <i class="mdi mdi-currency-eur search-icon"></i>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" @click="generateGiftVoucher">Generate Gift Voucher</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data(){
        return {
            amount: ''
        }
    },
    methods: {
        generateGiftVoucher() {
            const myModalEl = document.getElementById('giftVoucherModal');
            const modal = bootstrap.Modal.getInstance(myModalEl);
            modal.hide();
        }
    }
};
</script>