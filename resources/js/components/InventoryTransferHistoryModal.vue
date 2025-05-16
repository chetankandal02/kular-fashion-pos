<template>
    <div class="modal fade" id="transferInventoryHistoryModal" tabindex="-1"
        aria-labelledby="transferInventoryHistoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Transfer Inventory History</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table table-sm table-bordered" id="transfer-history-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Sent By</th>
                                <th>No.of Items</th>
                                <th>Date</th>

                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(transfer, index) in inventoryHistory" :key="transfer.id"
                                @click="toggleTransferDetails($event.currentTarget, [transfer.id])">
                                <td>{{ index + 1 }}</td>
                                <td>{{ transfer.sent_from?.name }}</td>
                                <td>{{ transfer.sent_to?.name }}</td>
                                <td>{{ transfer.sent_by?.name }}</td>
                                <td>{{ transfer.inventory_items?.length || 0 }}</td>
                                <td>{{ formatDate(transfer.created_at) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
    name: 'TransferInventoryHistoryModal',
    data() {
        return {
            inventoryHistory: [],
            table: null
        };
    },
    mounted() {
        const modal = document.getElementById('transferInventoryHistoryModal');
        if (modal) {
            modal.addEventListener('shown.bs.modal', this.onHistoryModalShow);
            modal.addEventListener('hidden.bs.modal', () => {
                if ($.fn.DataTable.isDataTable('#transfer-history-table')) {
                    this.table.destroy();
                }
            });
        }
    },
    methods: {
        async onHistoryModalShow() {
            try {
                const res = await axios.get('/inventory-transfer-history');
                console.log("Transfer history data:", res.data);
                this.inventoryHistory = res.data;

                this.$nextTick(() => {
                    this.table = $('#transfer-history-table').DataTable({
                        responsive: true,
                        destroy: true,
                    });
                });
            } catch (err) {
                Swal.fire('Error', 'Could not load transfer history', 'error');
            }
        },

        async toggleTransferDetails(row, transfer) {
            const table = $('#transfer-history-table').DataTable();
            const dataTableRow = table.row(row);

        
            $('#transfer-history-table tbody tr').removeClass('bg-dark text-white table-active');

           
            table.rows().every(function () {
                if (this.child.isShown()) {
                    this.child.hide();
                }
            });

           
            if (dataTableRow.child.isShown()) {
                return;
            }

            try {
                const response = await axios.get(`/inventory-transfer-details/${transfer[0]}`);
                const details = response.data;

                const detailsHtml = `
          <div class="p-2 bg-dark text-white">
            <div class="row mb-2">
              <div class="col-md-3"><strong>Sent From:</strong> ${details.sent_from?.name || '-'}</div>
              <div class="col-md-3"><strong>Sent To:</strong> ${details.sent_to?.name || '-'}</div>
              <div class="col-md-3"><strong>Sent By:</strong> ${details.sent_by?.name || '-'}</div>
              <div class="col-md-3"><strong>Date:</strong> ${this.formatDate(details.created_at)}</div>
            </div>
            <table class="table table-sm table-bordered bg-white text-dark">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Article Code</th>
                  <th>Brand</th>
                  <th>Type</th>
                  <th>Dept</th>
                  <th>Description</th>
                  <th>Color</th>
                  <th>Size</th>
                  <th>Manufacture Code</th>
                  <th>Price</th>
                </tr>
              </thead>
              <tbody>
                ${details.inventory_items.map((item, i) => `
                  <tr>
                    <td>${i + 1}</td>
                    <td>${item.product?.article_code || '-'}</td>
                    <td>${item.brand?.name || '-'}</td>
                    <td>${item.product?.product_type?.name || '-'}</td>
                    <td>${item.product?.department_id || '-'}</td>
                    <td>${item.product?.name || '-'}</td>
                    <td>${item.product_color?.color_detail?.name || '-'}</td>
                    <td>${item.product_size?.size_detail?.size || '-'}</td>
                    <td>${item.product?.manufacture_code || '-'}</td>
                    <td>${item.product?.price || '0.00'}</td>
                  </tr>
                `).join('')}
              </tbody>
            </table>
          </div>
        `;

                dataTableRow.child(detailsHtml).show();
             
                $(row).addClass('bg-dark text-white');
                $(row).next('tr').addClass('bg-dark text-white');

            } catch (error) {
                Swal.fire('Failed to load details', 'Please try again later.', 'error');
            }
        }
        ,


        formatDate(dateStr) {
            const d = new Date(dateStr);
            const day = String(d.getDate()).padStart(2, '0');
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const year = d.getFullYear();
            const hours = d.getHours() % 12 || 12;
            const minutes = String(d.getMinutes()).padStart(2, '0');
            const ampm = d.getHours() >= 12 ? '' : '';
            return `${day}-${month}-${String(year).slice(-2)} ${hours}:${minutes} ${ampm}`;
        }
    }
};
$(document).on('click', '#transfer-history-table tr', function () {
    $('#transfer-history-table tr').children('td').removeClass('bg-dark text-white');
    $(this).children('td').addClass('bg-dark text-white');
});
</script>
