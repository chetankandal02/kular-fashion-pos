<template>
  <div class="modal fade" id="layawayModal" tabindex="-1" aria-labelledby="layawayModalLabel">
    <div class="modal-dialog modal-fullscreen">
      <div class="modal-content">
        <div class="modal-header">
          <button class="btn" v-if="isCreatingCustomer || customerId"
            @click="isCreatingCustomer = false, customerId = null">
            <i class="fa fa-arrow-left me-2"></i>Back
          </button>
          <h5 class="modal-title" id="layawayModalLabel">
            <span v-if="isCreatingCustomer">Create a new Customer</span>
            <span v-else-if="customerId">Layaway</span>
            <span v-else>Choose Customer</span>
          </h5>
          <button type="button" class="btn-close" @click="closeModal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div :class="{ 'd-none': isCreatingCustomer || customerId }">
            <button class="btn btn-primary mb-2" @click="isCreatingCustomer = true">
              <i class="fa fa-user-plus me-2"></i>Create a new customer
            </button>
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

          <div :class="{ 'd-none': !customerId }" v-if="selectedCustomer">
            <div class="row">
              <div class="col-md-6">
                <LayawayPaymentForm :customerId="customerId" :orderItems="orderItems" :returnItems="returnItems" :paymentInfo="paymentInfo" @capturePayment="capturePayment" @clearSaleItems="clearSaleItems" />
              </div>
              <div class="col-md-6">
                <div>
                  <h4 class="fw-bold">Customer Information</h4>
                  <div class="row">
                    <div class="col-md-6">
                      <h5>Code: <strong>#{{ selectedCustomer.code }}</strong></h5>
                    </div>
                    <div class="col-md-6">
                      <h5>Name: <strong>{{ selectedCustomer.name }}</strong></h5>
                    </div>
                    <div class="col-md-6">
                      <h5>Phone Number: <strong>{{ selectedCustomer.phone_number }}</strong></h5>
                    </div>
                    <div class="col-md-6">
                      <h5>Email: <strong>{{ selectedCustomer.email }}</strong></h5>
                    </div>
                  </div>
                  <h5>Address: <strong>{{ selectedCustomer.address }}</strong></h5>
                </div>

                <div class="mt-4">
                  <h4 class="fw-bold">Items Details</h4>

                  <div v-if="returnItems.length > 0">
                    <h4 class="card-title mb-2">Return Items</h4>
                    <div class="table-responsive mb-4">
                      <table class="table align-middle table-nowrap mb-0">
                        <thead class="table-light">
                          <tr>
                            <th class="align-middle p-1">Article Code</th>
                            <th class="align-middle p-1">Description</th>
                            <th class="align-middle p-1">Color</th>
                            <th class="align-middle p-1">Size</th>
                            <th class="align-middle p-1">Brand</th>
                            <th class="align-middle p-1">Price</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="(item, index) in returnItems" :key="item.code + item.size">
                            <td class="p-1"><a href="javascript: void(0);" class="text-body fw-bold">{{ item.code }}</a>
                            </td>
                            <td class="p-1">{{ item.description }}</td>
                            <td class="p-1">{{ item.color }}</td>
                            <td class="p-1">{{ item.size }}</td>
                            <td class="p-1">{{ item.brand }}</td>
                            <td class="p-1 text-danger">-{{ formatPrice(item.changedPrice ? item.changedPrice.amount :
                              item.price) }}</td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>

                  <h4 class="card-title mb-2">Sale Items</h4>
                  <div class="table-responsive">
                    <table class="table align-middle table-striped table-nowrap mb-0">
                      <thead class="table-light">
                        <tr>
                          <th class="align-middle p-1">Article Code</th>
                          <th class="align-middle p-1">Description</th>
                          <th class="align-middle p-1">Color</th>
                          <th class="align-middle p-1">Size</th>
                          <th class="align-middle p-1">Brand</th>
                          <th class="align-middle p-1">Price</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-if="!orderItems.length">
                          <td colspan="7" class="text-center">No items added for sale</td>
                        </tr>
                        <tr v-for="(item, index) in orderItems" :key="index">
                          <td class="p-1"><a href="javascript: void(0);" class="text-body fw-bold">{{ item.code }}</a>
                          </td>
                          <td class="p-1">{{ item.description }}</td>
                          <td class="p-1">{{ item.color }}</td>
                          <td class="p-1">{{ item.size }}</td>
                          <td class="p-1">{{ item.brand }}</td>
                          <td class="p-1">{{ formatPrice(item.changedPrice ? item.changedPrice.amount : item.price) }}
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>

          </div>
          <div :class="{ 'd-none': !isCreatingCustomer }">
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
        <div class="modal-footer" v-if="isCreatingCustomer">
          <button type="button" class="btn btn-secondary" @click="closeModal">Close</button>
          <button type="button" class="btn btn-success" @click="createCustomer">Save</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';
import LayawayPaymentForm from './layaway/LayawayPaymentForm.vue';

export default {
  components: {
    LayawayPaymentForm
  },
  props: {
    orderItems: Array,
    returnItems: Array,
    paymentInfo: Array
  },
  data() {
    return {
      customerId: null,
      selectedCustomer: null,
      isCreatingCustomer: false,
      customerForm: {
        customerName: '',
        customerEmail: '',
        customerPhoneNumber: '',
        customerCompanyName: '',
        customerAddress: ''
      },
      layawayForm: {
        paymentMethod: 'Cash',
        amount: '',
        barcode: '',
        note: ''
      },
      table: null,
    };
  },
  mounted() {
    this.initializeDataTable();
  },
  computed: {
    isAmountValid() {
      return (this.layawayForm.amount || 0) >= 0;
    }
  },
  watch: {
    isCreatingCustomer() {
      this.reloadCustomersTable();
    },
  },
  methods: {
    formatPrice(price) {
      if (!price) return '£0.00';
      return `£${parseFloat(price).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}`;
    },
    capturePayment(payment) {
      this.$emit('captureLayawayPayment', payment, true)
    },
    clearSaleItems(){
      this.$emit('clearSaleItems');
    },
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
                <button class="btn btn-primary btn-sm pick-layaway-customer" data-customer='${JSON.stringify(row)}'>
                  <i class="fas fa-piggy-bank"></i>
                </button>
              `;
            },
          },
        ],
        order: [[0, 'desc']],
        drawCallback: function () {
          $('.pick-layaway-customer').on('click', (event) => {
            const customer = JSON.parse($(event.currentTarget).attr('data-customer'));
            vm.chooseLayawayCustomer(customer);
          });
        },
      });
    },
    async chooseLayawayCustomer(customer) {
      this.selectedCustomer = customer;
      this.customerId = customer.id;
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
    reloadCustomersTable() {
      if (this.table) {
        this.table.ajax.reload();
      } else {
        console.error('DataTable instance is not available.');
      }
    },
    closeModal() {
      const modal = bootstrap.Modal.getInstance($('#layawayModal'));
      modal.hide();
    },
  }
};
</script>