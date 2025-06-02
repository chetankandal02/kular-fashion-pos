<template>
  <div class="modal fade" id="recallLayawayModal" tabindex="-1" aria-labelledby="recallLayawayModalLabel">
    <div class="modal-dialog modal-fullscreen">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="recallLayawayModalLabel">Recall Layaway</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="d-flex justify-content-between mb-3">
            <div class="d-flex align-items-center">
              <div class="spinner-border spinner-border-sm me-2" role="status" v-if="loading">
                <span class="visually-hidden">Loading...</span>
              </div>
              <button class="btn btn-sm btn-outline-primary" @click="reloadTable">
                <i class="fas fa-sync-alt me-1"></i> Refresh
              </button>
            </div>
            <div class="d-flex">
              <input type="text" class="form-control form-control-sm" placeholder="Search..." v-model="searchQuery" 
                     @input="handleSearch" style="width: 200px;">
            </div>
          </div>
          
          <table class="table table-striped table-bordered w-100 table-sm" id="layaways-table">
            <thead>
              <tr>
                <th>#</th>
                <th>Layaway Code</th>
                <th>Sale ID</th>
                <th>Customer Code</th>
                <th>Customer Name</th>
                <th>Balance</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody v-if="!useDataTables">
              <tr v-for="(layaway, index) in layaways" :key="layaway.id">
                <td>{{ index + 1 }}</td>
                <td>{{ layaway.code }}</td>
                <td>{{ layaway.order?.code }}</td>
                <td>{{ layaway.customer?.code }}</td>
                <td>{{ layaway.customer?.name }}</td>
                <td>{{ formatPrice(layaway.balance) }}</td>
                <td>
                  <button class="btn btn-primary btn-sm py-0 px-1" @click="pickLayaway(layaway)">
                    <i class="fas fa-hand-holding-usd"></i>
                  </button>
                </td>
              </tr>
              <tr v-if="layaways.length === 0 && !loading">
                <td colspan="7" class="text-center">No layaways found</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <EditLayawayModal :layaway="selectedLayaway" @reloadTable="reloadTable" />
</template>

<script>
import axios from 'axios';
import EditLayawayModal from './layaway/EditLayawayModal.vue';

export default {
  data() {
    return {
      table: null,
      selectedLayaway: null,
      loading: false,
      layaways: [],
      searchQuery: '',
      useDataTables: true, // Set to false if you want to use Vue rendering instead
      modalInstance: null
    };
  },
  components: {
    EditLayawayModal
  },
  mounted() {
    this.initializeModal();
    if (this.useDataTables) {
      this.initializeDataTable();
    }
  },
  beforeUnmount() {
    // Clean up modal event listeners
    const modal = document.getElementById('recallLayawayModal');
    if (modal) {
      modal.removeEventListener('show.bs.modal', this.handleModalShow);
      modal.removeEventListener('hidden.bs.modal', this.handleModalHide);
    }
    
    // Destroy DataTable if it exists
    if (this.table) {
      this.table.destroy();
    }
  },
  methods: {
    initializeModal() {
      const modal = document.getElementById('recallLayawayModal');
      if (modal) {
        modal.addEventListener('show.bs.modal', this.handleModalShow);
        modal.addEventListener('hidden.bs.modal', this.handleModalHide);
        this.modalInstance = new bootstrap.Modal(modal);
      }
    },
    handleModalShow() {
      // Refresh data when modal opens
      this.reloadTable();
    },
    handleModalHide() {
      // Clean up when modal closes
      this.searchQuery = '';
    },
    async initializeDataTable() {
      const vm = this;
      
      this.table = $('#layaways-table').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 20,
        lengthChange: false,
        ajax: {
          url: '/api/layaways',
          method: 'GET',
          data: (d) => {
            d.page = Math.floor(d.start / d.length) + 1;
            if (vm.searchQuery) {
              d.search = vm.searchQuery;
            }
          },
          beforeSend: () => {
            vm.loading = true;
          },
          complete: () => {
            vm.loading = false;
          }
        },
        columns: [
          {
            title: '#',
            data: 'id',
          },
          {
            data: 'code',
          },
          {
            data: 'order.code',
          },
          {
            data: 'customer.code',
          },
          {
            data: 'customer.name',
          },
          {
            data: null,
            render: function (data, type, row) {
              return vm.formatPrice(row.balance);
            },
          },
          {
            data: null,
            render: function (data, type, row) {
              return `
                <button class="btn btn-primary btn-sm py-0 px-1 pick-layaway" data-layaway='${JSON.stringify(row)}'>
                  <i class="fas fa-hand-holding-usd"></i>
                </button>
              `;
            },
          },
        ],
        order: [[0, 'desc']],
        drawCallback: function () {
          $('.pick-layaway').on('click', (event) => {
            const layaway = JSON.parse($(event.currentTarget).attr('data-layaway'));
            vm.pickLayaway(layaway);
          });
        },
      });
    },
    async fetchLayaways() {
      try {
        this.loading = true;
        const response = await axios.get('/api/layaways', {
          params: {
            search: this.searchQuery
          }
        });
        this.layaways = response.data.data || response.data;
      } catch (error) {
        console.error('Error fetching layaways:', error);
      } finally {
        this.loading = false;
      }
    },
    handleSearch() {
      if (this.useDataTables) {
        this.table.ajax.reload();
      } else {
        this.fetchLayaways();
      }
    },
    reloadTable() {
      if (this.useDataTables) {
        if (this.table) {
          this.table.ajax.reload();
        } else {
          this.initializeDataTable();
        }
      } else {
        this.fetchLayaways();
      }
    },
    pickLayaway(layaway) {
      this.selectedLayaway = layaway;
      $('#editLayawayModal').modal('show');
    },
    formatPrice(price) {
      if (!price) return '£0.00';
      return `£${parseFloat(price).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}`;
    },
    formatDate(date) {
      if (!date) return '';
      const d = new Date(date);
      return `${d.getDate().toString().padStart(2, '0')}-${(d.getMonth() + 1).toString().padStart(2, '0')}-${d.getFullYear()} ${d.getHours().toString().padStart(2, '0')}:${d.getMinutes().toString().padStart(2, '0')}:${d.getSeconds().toString().padStart(2, '0')}`;
    },
    nl2br(text) {
      return text?.replace(/\n/g, '<br>');
    },
  },
};
</script>