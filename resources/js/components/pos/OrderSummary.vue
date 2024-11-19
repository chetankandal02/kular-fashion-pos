<template>
    <div class="card">
        <div class="card-body">
            <h4>Order Summary</h4>

            <div class="d-flex justify-content-between">
                <h5>Sub Total</h5>
                <h5>{{ subtotal }}</h5>
            </div>

            <div class="d-flex justify-content-between">
                <h5>Total Items</h5>
                <h5>{{ totalItems }}</h5>
            </div>

            <div class="d-flex justify-content-between">
                <h5>Grand Total</h5>
                <h5>{{ grandTotal }}</h5>
            </div>

            <div class="row">
                <!-- Actions (Hold, Cancel, etc.) -->
                <div v-for="action in actions" :key="action.name" class="col-6 mb-2 pe-1">
                    <button :class="`btn ${action.class} w-100`" @click="handleActionClick(action.name)">
                        <i :class="action.icon" class="font-size-14 me-1"></i>
                        {{ action.name }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            orderItems: [
                { code: '#SK2540', description: 'Neal Matthews', price: 45 },
                { code: '#SK2541', description: 'Jamal Burnett', price: 470 },
            ],
            actions: [
                { name: 'Hold Sale', class: 'btn-warning', icon: 'mdi mdi-pause' },
                { name: 'Cancel', class: 'btn-danger', icon: 'mdi mdi-close' },
                { name: 'Layway', class: 'btn-secondary', icon: 'mdi mdi-account-cash' },
                { name: 'Credit Notes', class: 'btn-dark', icon: 'mdi mdi-notebook-edit-outline' },
                { name: 'Tender', class: 'btn-success', icon: 'mdi mdi-cash-plus' },
                { name: 'Return Sale', class: 'btn-danger', icon: 'mdi mdi-keyboard-return' },
                { name: 'EOD', class: 'btn-info', icon: 'mdi mdi-printer' },
                { name: 'Gift Voucher', class: 'btn-dark', icon: 'mdi mdi-gift' },
            ],
        };
    },
    methods: {
        handleActionClick(actionName) {
            switch (actionName) {
                case 'Hold Sale':
                    this.holdOrder();
                    break;
                case 'Cancel':
                    this.cancelOrder();
                    break;
                case 'Layway':
                    this.laywayOrder();
                    break;
                case 'Credit Notes':
                    this.creditNotes();
                    break;
                case 'Tender':
                    this.tenderOrder();
                    break;
                case 'Return Sale':
                    this.returnSale();
                    break;
                case 'EOD':
                    this.endOfDay();
                    break;
                case 'Gift Voucher':
                    this.giftVoucher();
                    break;
                default:
                    console.log(`Unknown action: ${actionName}`);
                    break;
            }
        }
    },
    computed: {
        subtotal() {
            return this.orderItems.reduce((sum, item) => sum + item.price, 0);
        },
        totalItems() {
            return this.orderItems.length;
        },
        grandTotal() {
            return this.subtotal;
        },
    },
};
</script>