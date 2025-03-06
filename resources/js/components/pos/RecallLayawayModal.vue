<template>
  <div class="modal fade" id="recallLayawayModal" tabindex="-1" aria-labelledby="recallLayawayModalLabel">
    <div class="modal-dialog modal-fullscreen">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="recallLayawayModalLabel">Recall Layaway</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
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
          </table>
        </div>
      </div>
    </div>
  </div>

  <EditLayawayModal :layaway="selectedLayaway" />
</template>

<script>
import EditLayawayModal from './layaway/EditLayawayModal.vue';

export default {
  data() {
    return {
      table: null,
      selectedLayaway: null
    };
  },
  components: {
    EditLayawayModal
  },
  mounted() {
    this.initializeDataTable();
  },
  methods: {
    initializeDataTable() {
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
          },
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
    pickLayaway(layaway){
      this.selectedLayaway = layaway;
      $('#editLayawayModal').modal('show');
    },
    formatPrice(price) {
      if (!price) return '£0.00';
      return `£${parseFloat(price).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}`;
    },
    formatDate(date) {
      return `${date.getDate().toString().padStart(2, '0')}-${(date.getMonth() + 1).toString().padStart(2, '0')}-${date.getFullYear()} ${date.getHours().toString().padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}:${date.getSeconds().toString().padStart(2, '0')}`;
    },
    nl2br(text) {
      return text?.replace(/\n/g, '<br>');
    },
  },
};
</script>