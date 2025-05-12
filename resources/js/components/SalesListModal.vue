<template>
    <div class="modal fade" id="salesListModal" tabindex="-1" aria-labelledby="salesListModalLabel">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header py-2 px-3">
                    <h5 class="modal-title" id="salesListModalLabel">Sales List</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body py-1">
                    <div class="row">
                        <div class="col-6 col-md-2 mb-1">
                            <label for="article_code" class="mb-0">Article Code:</label>
                            <input id="article_code" class="form-control h-50" />
                        </div>
                        <div class="col-6 col-md-2 mb-1">
                            <label for="sales_start_date" class="mb-0">From Date:</label>
                            <input id="sales_start_date" class="form-control h-50" />
                        </div>
                        <div class="col-6 col-md-2 mb-1">
                            <label for="sales_end_date" class="mb-0">To Date:</label>
                            <input id="sales_end_date" class="form-control h-50" />
                        </div>
                    </div>

                    <table class="table table-bordered table-sm w-100" id="sales-list">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Code</th>
                                <th>Customer Name</th>
                                <th>Sales Person</th>
                                <th>Items</th>
                                <th>Amount</th>
                                <th>Date & Time</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
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
            table: null,
            expandedRows: new Set()
        };
    },
    methods: {
        initializeDataTable() {
            const vm = this;
            if ($.fn.dataTable.isDataTable('#sales-list')) {
                return;
            }

            this.table = $('#sales-list').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 20,
                lengthChange: false,
                searching: false,
                ajax: {
                    url: "/get-orders",
                    data: function (d) {
                        d.article_code = $('#article_code').val();
                        d.sales_start_date = $('#sales_start_date').val();
                        d.sales_end_date = $('#sales_end_date').val();
                        d.page = Math.floor(d.start / d.length) + 1;
                    }
                },
                columns: [
                    { title: "#", data: 'id' },
                    { title: "Code", data: 'code' },
                    { title: "Customer Name", data: 'customer_name' },
                    { title: "Sales Person", data: 'sales_person_name' },
                    { title: "Items", data: 'total_items' },
                    {
                        title: "Amount",
                        data: 'total_payable_amount',
                        render: function (data) {
                            return '£' + parseFloat(data).toFixed(2);
                        }
                    },
                    { title: 'Date & Time', data: 'date_time' },
                ],
                order: [[0, 'desc']],
                rowCallback: function (row, data) {
                    $(row).off('click').on('click', function () {
                        vm.toggleRowDetails(row, data);
                    });
                },
                createdRow: function (row, data) {
                    if (data.is_price_changed === true) {
                        $(row).find('td').addClass('bg-warning');
                    }
                }
            });
        },

        async toggleRowDetails(row, data) {
            const table = $('#sales-list').DataTable();
            const rowIndex = table.row(row).index();

            if (this.expandedRows.has(rowIndex)) {
                table.row(rowIndex + 1).remove().draw(false);
                this.expandedRows.delete(rowIndex);
            } else {
                const response = await axios.get(`/sale-detail/${data.id}`);
                const saleDetails = response.data.sale;

                const detailsRowHtml = `
                    <tr class="order-details-row">
                        <td colspan="7">
                            <div class="p-2 bg-dark">
                                <div class="row">
                                    <div class="col-md-4"><strong>Sale ID:</strong> #${saleDetails.code}</div>
                                    <div class="col-md-4"><strong>Sale Date:</strong> ${this.formatDateTime(saleDetails.created_at)}</div>
                                    <div class="col-md-4"><strong>Sold By:</strong> ${saleDetails.sales_person.name}</div>
                                    <div class="col-md-4"><strong>Total Sale Items:</strong> ${saleDetails.total_items - saleDetails.total_return_items}</div>
                                    <div class="col-md-4"><strong>Total Return Items:</strong> ${saleDetails.total_return_items}</div>
                                    <div class="col-md-4"><strong>Total Amount:</strong> £${saleDetails.total_payable_amount}</div>
                                </div>

                                <hr class="m-1">

                                ${saleDetails.payment_methods.length > 0 ? `
                                    <h6>Payment Methods:</h6>
                                    <div class="row">
                                        ${saleDetails.payment_methods.map(pm => `
                                            <div class="col-md-4"><strong>${pm.method}:</strong> £${pm.amount}</div>
                                        `).join('')}
                                    </div>
                                    <hr class="m-1">
                                ` : ''}

                                <table class="table table-sm mt-2">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Article Code</th>
                                            <th>Description</th>
                                            <th>Color</th>
                                            <th>Size</th>
                                            <th>Brand</th>
                                            <th>Amount</th>
                                            <th>Changed Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${saleDetails.order_items.map((item, index) => `
                                            <tr>
                                                <td>${index + 1}</td>
                                                <td>${item.article_code}</td>
                                                <td>${item.description}</td>
                                                <td>${item.color_name}</td>
                                                <td>${item.size}</td>
                                                <td>${item.brand_name}</td>
                                                <td class="${item.flag !== 'SALE' ? 'text-danger' : ''}">
                                                    ${item.flag !== 'SALE' ? '-' : ''}£${item.original_price}
                                                </td>
                                                <td class="${item.flag !== 'SALE' ? 'text-danger' : ''}">
                                                    ${item.flag !== 'SALE' ? '-' : ''}£${item.changed_price}
                                                </td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                `;

                table.row(rowIndex).child(detailsRowHtml).show();
                this.expandedRows.add(rowIndex);
            }
        },

        reloadDataTable() {
            if (this.table) {
                this.table.ajax.reload();
            }
        },

        formatDateTime(timestamp) {
            const date = new Date(timestamp);
            return date.toLocaleString();
        }
    },
    mounted() {
        this.initializeDataTable();

        

        $('#article_code').on('keyup', this.reloadDataTable);
        $('#sales_start_date, #sales_end_date').on('change', this.reloadDataTable);

        $('[data-bs-target="#salesListModal"]').on('click', function(){
            
        });

        $(document).on('click', '#sales-list tr', function () {
            $('#sales-list tr').children('td').removeClass('bg-dark');
            $(this).children('td').addClass('bg-dark');
        });

        $('#salesListModal').on('hidden.bs.modal', function () {
            if ($.fn.dataTable.isDataTable('#sales-list')) {
                $('#sales-list').DataTable().clear().destroy();
            }
            $('.modal-backdrop').remove();
        });
    },
    beforeUnmount() {

        $(document).off('click', '#sales-list tbody tr');
    }
};
</script>
