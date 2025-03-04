<template>
  <div class="modal fade" id="layawayModal" tabindex="-1" aria-labelledby="layawayModalLabel">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="layawayModalLabel">Layaway</h5>
          <button type="button" class="btn-close" @click="closeModal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-sm-4">
              <label for="customer_name">Customer Name</label>
              <input type="text" class="form-control" v-model="form.customer_name" />
            </div>
            <div class="col-sm-4">
              <label for="customer_email">Customer Email</label>
              <input type="text" class="form-control" v-model="form.customer_email" />
            </div>
            <div class="col-sm-4">
              <label for="contact_number">Contact Number</label>
              <input type="text" class="form-control" v-model="form.contact_number" />
            </div>
            <div class="col-sm-4">
              <label for="down_payment">Down Payment</label>
              <input type="number" class="form-control" v-model="form.down_payment" />
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="closeModal">Close</button>
          <button type="button" class="btn btn-success" @click="submitForm">Proceed</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
  props: {
    grandTotal: Number,
    pendingBalance: Number,
  },
  data() {
    return {
      form: {
        customer_name: '',
        customer_email: '',
        contact_number: '',
        down_payment: 0
      },
    };
  },
  methods: {
    async submitForm() {
      const formData = {
        customer_name: this.form.customer_name,
        customer_email: this.form.customer_email,
        contact_number: this.form.contact_number,
        down_payment: this.form.down_payment,
        grand_total: this.grandTotal,
      };

      try {
        const response = await axios.post('/api/customer', formData);

        if (response.data.success) {
          const customerId = response.data.customer_id;

          this.form = {
            customer_name: '',
            customer_email: '',
            contact_number: '',
            down_payment: 0,
          };
          this.closeModal();
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Customer already exists with the same email or phone number.',
          }).then(() => {
            this.form = {
              customer_name: '',
              customer_email: '',
              contact_number: '',
              down_payment: 0,
            };
            this.closeModal();
          });
        }
      } catch (error) {
        console.error('Error during API call:', error);
        this.errorMessage = 'There was an issue processing your request. Please try again later.';
      }
    },
    closeModal() {
      const modal = bootstrap.Modal.getInstance($('LayawayModal'));
      modal.hide();
    },
  }
};
</script>