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
                        <VirtualNumberKeyboard 
                            ref="virtualKeyboard"
                            :inputValue="amount" 
                            @on-change="changeAmount" />
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
import VirtualNumberKeyboard from '../VirtualNumberKeyboard.vue';

export default {
    data() {
        return {
            amount: ''
        };
    },
    components: {
        VirtualNumberKeyboard
    },
    mounted() {
        const modalElement = document.getElementById('giftVoucherModal');
        modalElement.addEventListener('shown.bs.modal', this.focusInput);
    },
    beforeDestroy() {
        const modalElement = document.getElementById('giftVoucherModal');
        modalElement.removeEventListener('shown.bs.modal', this.focusInput);
    },
    methods: {
        generateGiftVoucher() {
            const myModalEl = document.getElementById('giftVoucherModal');
            const modal = bootstrap.Modal.getInstance(myModalEl);
            modal.hide();
        },
        changeAmount(value) {
            this.amount = value;
        },
        focusInput() {
            if (this.$refs.virtualKeyboard && this.$refs.virtualKeyboard.$refs.input) {
                this.$refs.virtualKeyboard.$refs.input.focus();
            }
        }
    }
};
</script>
