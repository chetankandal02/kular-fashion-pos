<template>
  <div class="modal fade" id="editLayawayModal" tabindex="-1" aria-labelledby="editLayawayModalLabel">
    <div class="modal-dialog modal-fullscreen">
      <div class="modal-content" v-if="layaway">
        <div class="modal-header">
          <h5 class="modal-title" id="editLayawayModalLabel">Edit Layaway #{{ layaway.code }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div>
                <h4 class="fw-bold">Summary</h4>
                <h5>Sale Total: <strong>{{ formatPrice(layaway.total_amount) }}</strong></h5>
                <h5>Paid Amount: <strong>{{ formatPrice(layaway.paid_amount) }}</strong></h5>
                <h5>Pending Balance: <strong class="text-warning">{{ formatPrice(layaway.balance) }}</strong></h5>
              </div>
              <div class="row">
                <div class="col-md-6 mb-2">
                  <label for="layaway_payment_method">Select Method</label>
                  <select id="layaway_payment_method" class="form-control" v-model="paymentMethod">
                    <option value="Cash" selected>Cash</option>
                    <option value="Card">Card</option>
                    <option value="Euro">Euro</option>
                    <option value="Credit Note">Credit Note</option>
                    <option value="Gift Voucher">Gift Voucher</option>
                  </select>
                </div>
                <div class="col-md-6 mb-2"
                  v-if="paymentMethod === 'Cash' || paymentMethod === 'Card' || paymentMethod === 'Euro'">
                  <label for="edit_layaway_payment_amount">Enter Amount</label>
                  <input type="number" id="edit_layaway_payment_amount" class="form-control" v-model="amount">
                </div>
                <div class="col-md-6 mb-2" v-else>
                  <label for="edit_layaway_payment_barcode">Barcode</label>
                  <input type="number" id="edit_layaway_payment_barcode" class="form-control" v-model="barcode">
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <label for="layaway_note">Note</label>
                  <textarea id="layaway_note" class="form-control" v-model="note"></textarea>
                </div>
              </div>

              <div class="mt-3">
                <button type="button" class="btn btn-lg btn-secondary" @click="closeModal">Cancel</button>
                <button type="button" class="btn btn-lg btn-success ms-2" @click="addPayment(amount)"
                  v-if="paymentMethod !== 'Credit Note' && paymentMethod !== 'Gift Voucher'">Add Payment</button>
              </div>
            </div>
            <div class="col-md-6">
              <div>
                <h4 class="fw-bold">Customer Information</h4>
                <div class="row">
                  <div class="col-md-6">
                    <h5>Code: <strong>#{{ layaway.customer.code }}</strong></h5>
                  </div>
                  <div class="col-md-6">
                    <h5>Name: <strong>{{ layaway.customer.name }}</strong></h5>
                  </div>
                  <div class="col-md-6">
                    <h5>Phone Number: <strong>{{ layaway.customer.phone_number }}</strong></h5>
                  </div>
                  <div class="col-md-6">
                    <h5>Email: <strong>{{ layaway.customer.email }}</strong></h5>
                  </div>
                </div>
                <h5>Address: <strong>{{ layaway.customer.address }}</strong></h5>
              </div>

              <div class="mt-4" v-if="layaway.payments.length > 0">
                <h4 class="fw-bold">Transaction History</h4>
                <div>
                  <div class="table-responsive mb-4">
                    <table class="table align-middle table-nowrap table-sm mb-0">
                      <thead class="table-light">
                        <tr>
                          <th class="align-middle">#</th>
                          <th class="align-middle">Payment Method</th>
                          <th class="align-middle">Paid Amount</th>
                          <th class="align-middle">Date</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="(payment, index) in layaway.payments" :key="`payment-${index}`">
                          <td>{{ index + 1 }}</td>
                          <td>{{ payment.method }}</td>
                          <td>
                            {{ formatPrice(payment.amount) }}
                            <span v-if="payment.method === 'Euro'">(€{{ payment.original_amount }})</span>
                          </td>
                          <td>{{ payment.payment_date || formatDateTime(payment.created_at) }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <div class="mt-4" v-if="layaway.order.order_items.length > 0">
                <h4 class="fw-bold">Items Details</h4>
                <div>
                  <div class="table-responsive mb-4">
                    <table class="table align-middle table-nowrap table-sm mb-0">
                      <thead class="table-light">
                        <tr>
                          <th class="align-middle">Article Code</th>
                          <th class="align-middle">Description</th>
                          <th class="align-middle">Color</th>
                          <th class="align-middle">Size</th>
                          <th class="align-middle">Brand</th>
                          <th class="align-middle">Price</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="(item, index) in layaway.order.order_items" :key="item.code + item.size">
                          <td>{{ item.article_code }}</td>
                          <td>{{ item.description }}</td>
                          <td>{{ item.color_name }}</td>
                          <td>{{ item.size }}</td>
                          <td>{{ item.brand_name }}</td>
                          <td>
                            <span v-if="item.flag === 'SALE'">
                              {{ formatPrice(item.changed_price) }}
                            </span>
                            <span v-else class="text-danger">
                              -{{ formatPrice(item.changed_price) }}
                            </span>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import LayawayPaymentForm from './LayawayPaymentForm.vue';

export default {
  props: {
    layaway: Array | Object
  },
  components: {
    LayawayPaymentForm
  },
  data() {
    return {
      table: null,
      paymentMethod: 'Cash',
      amount: '',
      barcode: '',
      note: this.layaway?.note || ''
    };
  },
  watch: {
    layaway: {
      handler(newLayaway) {
        this.note = newLayaway?.note || '';
      },
      immediate: true,
    },
    barcode: {
      handler(barcode) {
        if (String(barcode).length === 13) {
          if (this.paymentMethod === 'Gift Voucher') {
            this.payViaBarcode(this.barcode);
          } else if (this.paymentMethod === 'Credit Note') {
            this.payViaBarcode(this.barcode);
          }

          this.barcode = '';
        }
      }
    }
  },
  methods: {
    closeModal() {
      $('#editLayawayModal').modal('hide');
    },
    formatPrice(price) {
      if (!price) return '£0.00';
      return `£${parseFloat(price).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}`;
    },
    async addPayment(amount) {
      const layawayResponse = await axios.put(`/api/layaways/${this.layaway.id}`, {
        paymentMethod: this.paymentMethod,
        amount: amount,
        note: this.note,
        salesPersonId: window.config.userId
      });

      if (layawayResponse.data.success) {
        this.amount = '';
        this.amount = '';
        this.amount = '';

        Swal.fire({
          title: 'Success!',
          text: `Payment added successfully!`,
          icon: 'success',
          timer: 2000,
          showConfirmButton: false
        });

        this.$emit('reloadTable');
        $('#editLayawayModal').modal('hide');
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: response.data.message || 'Something went wrong!',
        });
      }
    },
    async payViaBarcode(barcode) {
      let payload = {
        endpoint: `/api/gift-voucher/apply`,
        param: 'gift'
      }

      if (this.paymentMethod === 'Credit Note') {
        payload = {
          endpoint: `/api/credit-note/apply`,
          param: 'credit'
        }
      }

      const response = await axios.post(payload.endpoint, {
        barcode: barcode
      });

      if (response.data.success) {
        let capturedAmount = response.data[payload.param].amount;
        Swal.fire({
          title: 'Success!',
          text: `${this.formatPrice(capturedAmount)} captured successfully!`,
          icon: 'success',
          timer: 2000,
          showConfirmButton: false
        });

        this.addPayment(capturedAmount || 0);
      } else {
        Swal.fire({
          title: 'Oops!',
          text: response.data.message,
          icon: 'error',
          confirmButtonText: 'Okay'
        });
      }
    },
    formatDateTime(timestamp) {
      let now = new Date(timestamp);
      let day = now.getDate();
      let month = now.getMonth() + 1;
      let year = now.toLocaleDateString('en', { year: '2-digit' });
      let hours = now.getHours();
      let minutes = now.getMinutes();
      let seconds = now.getSeconds();

      let period = hours >= 12 ? 'PM' : 'AM';

      hours = hours % 12;
      hours = hours ? hours : 12;

      minutes = minutes < 10 ? '0' + minutes : minutes;
      seconds = seconds < 10 ? '0' + seconds : seconds;

      let formattedDate = (day < 10 ? '0' + day : day) + '-' + month + '-' + year;

      let formattedTime = hours + ':' + minutes + ':' + seconds + ' ' + period;

      return formattedDate + ' ' + formattedTime;
    },
    nl2br(text) {
      return text?.replace(/\n/g, '<br>');
    },
  },
};
</script>