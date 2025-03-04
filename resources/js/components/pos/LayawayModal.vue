<template>
  <div class="modal fade" id="layawayModal" tabindex="-1" aria-labelledby="layawayModalLabel">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <button class="btn" v-if="isCreatingCustomer" @click="isCreatingCustomer = false">
            <i class="fa fa-arrow-left me-2"></i>Back
          </button>
          <h5 class="modal-title" id="layawayModalLabel">{{ isCreatingCustomer ? `Create a new Customer` : `Layaway` }}
          </h5>
          <button type="button" class="btn-close" @click="closeModal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div v-if="!isCreatingCustomer">
            <button class="btn btn-primary mb-2" @click="isCreatingCustomer = true"><i
                class="fa fa-user-plus me-2"></i>Create a new customer</button>
            <table class="table table-striped table-bordered table-sm w-100" id="search-customers-modal">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Customer Code</th>
                  <th>Email Address</th>
                  <th>Phone Number</th>
                  <th>Address</th>
                  <th>Action</th>
                </tr>
              </thead>
            </table>
          </div>
          <div v-else>
            <div class="row">
              <div class="col-md-4 mb-2">
                <label for="customerName">Customer Name<span class="text-danger">*</span></label>
                <input type="text" id="customerName" class="form-control" v-model="customerForm.customerName"
                  ref="customerName" required>
                <div class="invalid-feedback">Customer name is required.</div>
              </div>
              <div class="col-md-4">
                <label for="customerEmail">Customer Email<span class="text-danger">*</span></label>
                <input type="email" id="customerEmail" class="form-control" v-model="customerForm.customerEmail"
                  ref="customerEmail" required>
                <div class="invalid-feedback">Valid email is required.</div>
              </div>
              <div class="col-md-4">
                <label for="customerPhoneNumber">Phone Number<span class="text-danger">*</span></label>
                <input type="number" id="customerPhoneNumber" class="form-control"
                  v-model="customerForm.customerPhoneNumber" ref="customerPhoneNumber" required>
                <div class="invalid-feedback">Phone number is required.</div>
              </div>
              <div class="col-md-4">
                <label for="customerCompanyName">Company Name</label>
                <input type="text" id="customerCompanyName" class="form-control"
                  v-model="customerForm.customerCompanyName" ref="customerCompanyName" maxlength="75">
              </div>
              <div class="col-md-6">
                <label for="customerAddress">Address<span class="text-danger">*</span></label>
                <textarea id="customerAddress" class="form-control" v-model="customerForm.customerAddress"
                  maxlength="255" ref="customerAddress" required></textarea>
                <div class="invalid-feedback">Address is required.</div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="closeModal">Close</button>
          <button type="button" class="btn btn-success" v-if="!isCreatingCustomer" :disabled="!customerId"
            @click="createLayaway">Proceed</button>
          <button type="button" class="btn btn-success" v-else @click="createCustomer">Save</button>
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
      customerId: null,
      isCreatingCustomer: false,
      customerForm: {
        customerName: '',
        customerEmail: '',
        customerPhoneNumber: '',
        customerCompanyName: '',
        customerAddress: ''
      },
      table: null
    };
  },
  async mounted() {
    this.initializeDataTable();
  },
  methods: {
    initializeDataTable() {
      const vm = this;

      this.table = $('#search-customers-modal').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: '/api/customers',
          data: (d) => {
            d.page = Math.floor(d.start / d.length) + 1;
          },
        },
        columns: [
          {
            title: '#',
            data: 'id',
          },
          {
            title: 'Customer Code',
            data: 'code',
          },
          {
            title: 'Customer Name',
            data: 'name',
          },
          {
            title: 'Customer Email',
            data: 'email',
          },
          {
            title: 'Phone Number',
            data: 'phone_number',
          },
          {
            title: 'Action',
            data: null,
            render: function (data, type, row) {
              return `
                <button class="btn btn-primary btn-sm py-0 px-1 pick-layaway-customer" data-customer-id='${row.id}'>
                  <i class="fas fa-piggy-bank"></i>
                </button>
              `;
            },
          },
        ],
        order: [[0, 'desc']],
        drawCallback: function () {
          $('.pick-layaway-customer').on('click', (event) => {
            const customerId = JSON.parse($(event.currentTarget).attr('data-customer-id'));
            vm.chooseLayawayCustomer(customerId);
          });
        },
      });
    },
    async chooseLayawayCustomer(customerId){
      this.customerId = customerId;
    },
    async createLayaway() {
      const formData = {
        customerId: this.customerId,
        down_payment: this.form.down_payment,
        grand_total: this.grandTotal,
      };

      try {
        const response = await axios.post('/api/layaway', formData);

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
    validateForm() {
      let isValid = true;

      // Validate Customer Name
      if (!this.customerForm.customerName.trim()) {
        isValid = false;
        this.$refs.customerName.classList.add('is-invalid');
      } else {
        this.$refs.customerName.classList.remove('is-invalid');
      }

      // Validate Email
      const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!this.customerForm.customerEmail || !emailPattern.test(this.customerForm.customerEmail)) {
        isValid = false;
        this.$refs.customerEmail.classList.add('is-invalid');
      } else {
        this.$refs.customerEmail.classList.remove('is-invalid');
      }

      // Validate Phone Number
      const phonePattern = /^\d{10,15}$/; // Adjust based on your requirements
      if (!this.customerForm.customerPhoneNumber || !phonePattern.test(this.customerForm.customerPhoneNumber)) {
        isValid = false;
        this.$refs.customerPhoneNumber.classList.add('is-invalid');
      } else {
        this.$refs.customerPhoneNumber.classList.remove('is-invalid');
      }

      // Validate Address
      if (!this.customerForm.customerAddress.trim()) {
        isValid = false;
        this.$refs.customerAddress.classList.add('is-invalid');
      } else {
        this.$refs.customerAddress.classList.remove('is-invalid');
      }

      return isValid;
    },
    async createCustomer() {
      if (!this.validateForm()) {
        return;
      }

      const formData = {
        name: this.customerForm.customerName,
        email: this.customerForm.customerEmail,
        phone_number: String(this.customerForm.customerPhoneNumber),
        address: this.customerForm.customerAddress,
        company_name: this.customerForm.customerCompanyName,
      };

      try {
        const response = await axios.post('/api/customers', formData);
        if (response.data.success) {
          Swal.fire({
            icon: 'success',
            title: 'Success',
            text: 'Customer created successfully!',
          }).then(() => {
            this.resetcustomerForm();
            this.isCreatingCustomer = false;
            this.reloadCustomersTable();
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Customer already exists with the same email or phone number.',
          });
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          const errors = error.response.data.errors;
          let errorMessages = '';

          for (const key in errors) {
            errorMessages += `${errors[key].join('<br>')}<br>`;
          }

          Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            html: errorMessages,
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'There was an issue processing your request. Please try again later.',
          });
        }
      }
    },
    resetcustomerForm() {
      this.customerForm = {
        customerName: '',
        customerEmail: '',
        customerPhoneNumber: '',
        customerAddress: '',
      };
    },
    reloadCustomersTable(){
      if (this.table) {
        this.table.ajax.reload();
      }
    },
    closeModal() {
      const modal = bootstrap.Modal.getInstance($('#layawayModal'));
      modal.hide();
    },
  }
};
</script>