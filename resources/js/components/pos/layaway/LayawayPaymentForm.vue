<template>
    <div>
        <div>
            <h4 class="fw-bold">Summary</h4>
            <h5>Sale Total: <strong>{{ formatPrice(grandTotal) }}</strong></h5>
            <h5>Paid Amount: <strong>{{ formatPrice(getTotalPaidAmount()) }}</strong></h5>
            <h5>Pending Balance: <strong class="text-warning">{{ formatPrice(pendingBalance()) }}</strong></h5>
        </div>

        <div class="row">
            <div class="col-md-6 mb-2">
                <label for="layaway_payment_method">Select Method</label>
                <select id="layaway_payment_method" class="form-control" v-model="layawayForm.paymentMethod">
                    <option value="Cash" selected>Cash</option>
                    <option value="Card">Card</option>
                    <option value="Euro">Euro</option>
                    <option value="Credit Note">Credit Note</option>
                    <option value="Gift Voucher">Gift Voucher</option>
                </select>
            </div>
            <div class="col-md-6 mb-2"
                v-if="layawayForm.paymentMethod === 'Cash' || layawayForm.paymentMethod === 'Card' || layawayForm.paymentMethod === 'Euro'">
                <label for="layaway_payment_method">Enter Amount</label>
                <input type="number" id="layaway_payment_method" class="form-control" v-model="layawayForm.amount"
                    :class="{ 'is-invalid': !isAmountValid }">
                <div class="invalid-feedback" v-if="!isAmountValid">
                    Please enter a valid amount.
                </div>
                <span class="font-size-12">minimum recommended amunt is {{ formatPrice((grandTotal * 20) / 100)
                }}</span>
            </div>
            <div class="col-md-6 mb-2" v-else>
                <label for="layaway_payment_barcode">Barcode</label>
                <input type="number" id="layaway_payment_barcode" class="form-control" v-model="layawayForm.barcode">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <label for="layaway_note">Note</label>
                <textarea id="layaway_note" class="form-control" v-model="layawayForm.note"></textarea>
            </div>
        </div>

        <div class="mt-3">
            <button type="button" class="btn btn-lg btn-secondary" @click="closeModal">Cancel</button>
            <button type="button" class="btn btn-lg btn-success ms-2" @click="processLayaway">Proceed</button>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
    data() {
        return {
            layawayForm: {
                paymentMethod: 'Cash',
                amount: '',
                barcode: '',
                note: ''
            },
            orderItems: localStorage.getItem('orderItems') ? JSON.parse(localStorage.getItem('orderItems')) : [],
            returnItems: localStorage.getItem('returnItems') ? JSON.parse(localStorage.getItem('returnItems')) : [],
            paymentInfo: localStorage.getItem('paymentInfo') ? JSON.parse(localStorage.getItem('paymentInfo')) : [],
        };
    },
    props: {
        customerId: Number
    },
    computed: {
        isAmountValid() {
            return (this.layawayForm.amount || 0) >= 0;
        },
        grandTotal() {
            let orderItemsTotal = this.orderItems.reduce((sum, item) => {
                return sum + (item.changedPrice?.amount || item.price);
            }, 0);

            let returnItemsTotal = this.returnItems.reduce((sum, item) => {
                return sum + (item.changedPrice?.amount || item.price);
            }, 0);

            return (orderItemsTotal - returnItemsTotal).toFixed(2);
        },
    },
    watch: {
        'layawayForm.barcode': {
            handler(barcode) {
                if (String(barcode).length === 13) {
                    if (this.layawayForm.paymentMethod === 'Gift Voucher') {
                        this.payViaBarcode(this.layawayForm.barcode);
                    } else if (this.layawayForm.paymentMethod === 'Credit Note') {
                        this.payViaBarcode(this.layawayForm.barcode);
                    }

                    this.layawayForm.barcode = '';
                }
            }
        },
    },
    methods: {
        formatPrice(price) {
            if (!price) return '£0.00';
            return `£${parseFloat(price).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}`;
        },
        capturePayment(payment) {
            const existingPayment = this.paymentInfo.find(item => item.method === payment.method);

            if (existingPayment) {
                existingPayment.amount += payment.amount;
            } else {
                this.paymentInfo.push({ method: payment.method, amount: payment.amount });
            }

            localStorage.setItem('paymentInfo', JSON.stringify(this.paymentInfo));
        },
        makePayment(amount) {
            let paymentInfo = {
                method: this.layawayForm.paymentMethod,
                amount: parseFloat(amount)
            }
            this.capturePayment(paymentInfo);
        },
        async payViaBarcode(barcode) {
            let payload = {
                endpoint: `/api/gift-voucher/apply`,
                param: 'gift'
            }

            if (this.layawayForm.paymentMethod === 'Credit Note') {
                payload = {
                    endpoint: `/api/credit-note/apply`,
                    param: 'credit'
                }
            }

            const response = await axios.post(payload.endpoint, {
                barcode: barcode
            });

            if (response.data.success) {
                this.makePayment(response.data[payload.param].amount || 0);
            } else {
                Swal.fire({
                    title: 'Oops!',
                    text: response.data.message,
                    icon: 'error',
                    confirmButtonText: 'Okay'
                });
            }
        },
        async processLayaway(param, forceProceedLayaway = false) {
            if (Number((this.grandTotal - this.pendingBalance()) + this.layawayForm.amount) < ((this.grandTotal * 20) / 100) && !forceProceedLayaway) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning...',
                    text: 'The amount is less than 20% of total amount.\n Do you still want to proceed with this amount?',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No',
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.processLayaway(null, true);
                    }
                });

                return false;
            }

            try {
                if (this.layawayForm.amount) {
                    this.makePayment(this.layawayForm.amount);
                }

                const orderApiResponse = await axios.post('/api/place-order', {
                    orderItems: this.orderItems,
                    returnItems: this.returnItems,
                    paymentInfo: [],
                    salesPersonId: window.config.userId
                });

                const layawayApiResponse = await axios.post('/api/layaways', {
                    orderId: orderApiResponse.data.order.id,
                    customerId: this.customerId,
                    paymentMethods: this.paymentInfo,
                    note: this.layawayForm.note,
                });

                console.log('layawayApiResponse', layawayApiResponse);

                if (layawayApiResponse.data.success) {
                    /* this.layawayForm = {
                      paymentMethod: 'Cash',
                      amount: '',
                      note: '',
                    }; */
                    //this.closeModal();
                } else {
                    /* Swal.fire({
                      icon: 'error',
                      title: 'Oops...',
                      text: 'Couldn\'t proceed layaway!',
                    }); */
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                });
            }
        },
        getTotalPaidAmount() {
            const euroToPound = window.config.euro_to_pound || 0;

            return this.paymentInfo.reduce((total, payment) => {
                if (payment.method === 'Euro') {
                    let amountInPound = parseFloat(payment.amount) * parseFloat(euroToPound) || 0;
                    return total + amountInPound;
                }

                return total + parseFloat(payment.amount);
            }, 0);
        },
        pendingBalance() {
            return this.grandTotal - this.getTotalPaidAmount();
        },
    }
};
</script>