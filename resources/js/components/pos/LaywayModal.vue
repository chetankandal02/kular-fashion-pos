<template>
  <!-- Layaway Modal -->
  <div class="modal fade" id="laywayModal" tabindex="-1" aria-labelledby="laywayModalLabel">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="laywayModalLabel">Layaway</h5>
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
export default {
  data() {
    return {
      form: {
        customer_name: '',
        customer_email: '',
        contact_number: ''
      },
    };
  },
  methods: {
    async submitForm() {
        const response = await axios.post('/api/layaway', this.form);
        const customerId = response.data.customer_id;
        console.log('Customer ID:', customerId);

        this.form = {
          customer_name: '',
          customer_email: '',
          contact_number: ''
        };
        this.closeModal();
    },
    closeModal() {
      const modalElement = document.getElementById('laywayModal');
      const modal = bootstrap.Modal.getInstance(modalElement);
      modal.hide();
    },
  }
};
</script>