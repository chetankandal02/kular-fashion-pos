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
                        <div class=" col-6 col-md-2 mb-2 d-flex align-items-end">
                            <button class="btn btn-primary btn-md " data-bs-toggle="modal"
                                data-bs-target="#giftVoucherSaleListModal">
                                <i class="mdi mdi-gift-outline font-size-2 me-1"></i>
                                Gift Voucher Sale List
                            </button>
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
    <!-- Gift Voucher Sale List Modal -->
    <div class="modal fade" id="giftVoucherSaleListModal" tabindex="-1" aria-labelledby="giftVoucherSaleListLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">

                <div class="modal-header py-2 px-3">
                    <h5 class="modal-title" id="giftVoucherSaleListLabel">Gift Voucher Sale List</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body py-1">
                    <div class="row">
                        <div class="col-6 col-md-2 mb-1">
                            <label for="voucher_code" class="mb-0">Voucher Code:</label>
                            <input id="voucher_code" class="form-control h-50" />
                        </div>
                        <div class="col-6 col-md-2 mb-1">
                            <label for="voucher_start_date" class="mb-0">From Date:</label>
                            <input id="voucher_start_date" class="form-control h-50" />
                        </div>
                        <div class="col-6 col-md-2 mb-1">
                            <label for="voucher_end_date" class="mb-0">To Date:</label>
                            <input id="voucher_end_date" class="form-control h-50" />
                        </div>
                        <div class="col-6 col-md-2 mb-2 d-flex align-items-end" id="backToSalesListBtn">
                            <button class="btn btn-primary w-100">
                                <i class="mdi mdi-arrow-left-bold me-1"></i>
                                Back to Sales List
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm w-100" id="gift-voucher-sales-list">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>BarCode</th>
                                    <th>Customer Name</th>
                                    <th>Sales Person</th>
                                    <th>Items</th>
                                    <th>Amount</th>
                                    <!-- <th>Date & Time</th> -->
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
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
            giftVoucherTable: null,
            expandedRows: new Set()
        };
    },
    methods: {
        initializeGiftVoucherTable() {
            const vm = this;
            if ($.fn.dataTable.isDataTable('#gift-voucher-sales-list')) {
                return;
            }

            this.giftVoucherTable = $('#gift-voucher-sales-list').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 20,
                ajax: {
                    url: '/get-gift-voucher-orders',
                    data: function (d) {
                        d.voucher_code = $('#voucher_code').val();
                        d.voucher_start_date = $('#voucher_start_date').val();
                        d.voucher_end_date = $('#voucher_end_date').val();
                    },
                    error: function (xhr) {
                        console.log("ERROR RESPONSE:", xhr.responseText); // Debug in browser
                    }
                },
                columns: [
                    { data: 'id', title: '#' },
                    { data: 'barcode', title: 'Barcode' },
                    { data: 'payment_through', title: 'Payment Method' },
                    {
                        data: 'amount',
                        title: 'Amount',
                        render: function (data) {
                            return '£' + parseFloat(data).toFixed(2);
                        }
                    },
                    { data: 'generated_by', title: 'Generated By' },
                    { data: 'created_at', title: 'Created At' }
                ]
            });
        },

        reloadGiftVoucherTable() {
            if (this.giftVoucherTable) {
                this.giftVoucherTable.ajax.reload(null, false);
            } else {
                this.initializeGiftVoucherTable();
            }
        },
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
                    $(row).attr('id', 'sales-list-row');
                    if (data.is_price_changed === true) {
                        $(row).find('td').addClass('bg-warning');
                    }
                }
            });
        },

        async toggleRowDetails(row, data) {
            const table = $('#sales-list').DataTable();
            const rowIndex = table.row(row).index();

            for (let expandedIndex of this.expandedRows) {
                if (expandedIndex !== rowIndex) {
                    table.row(expandedIndex).child.hide();
                    this.expandedRows.delete(expandedIndex);
                }
            }

            if (this.expandedRows.has(rowIndex)) {
                table.row(rowIndex).child.hide();
                this.expandedRows.delete(rowIndex);
            } else {
                const response = await axios.get(`/sale-detail/${data.id}`);
                const saleDetails = response.data.sale;

                const detailsRowHtml = `
                    <div class="p-2 bg-dark" id="s-details">

                        <div class="row">
                            <div class="col-md-2"><strong>Total Sale Items:</strong> ${saleDetails.total_items - saleDetails.total_return_items}</div>
                            <div class="col-md-2"><strong>Total Return Items:</strong> ${saleDetails.total_return_items}</div>
                            <div class="col-md-2"><strong>Total Amount:</strong> £${saleDetails.total_payable_amount}</div>

                            ${saleDetails.payment_methods.length > 0 ? saleDetails.payment_methods.map(pm => `
                                <div class="col-md-2"><strong>${pm.method}:</strong> £${pm.amount}</div>
                            `).join('') : ''}
                        </div>

                        <hr class="m-1">

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
                `;

                table.row(rowIndex).child(detailsRowHtml).show();
                this.expandedRows.add(rowIndex);
            }
        },

        reloadDataTable() {
            if (this.table) {
                this.table.ajax.reload(null, false); // false keeps the current pagination
            } else {
                this.initializeDataTable(); // If it was destroyed earlier
            }
        },

        formatDateTime(timestamp) {
            const date = new Date(timestamp);
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are 0-based
            const year = date.getFullYear();
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');

            return `${day}-${month}-${year} ${hours}:${minutes}`;
        }
    },
    mounted() {
        const vm = this;

        $('#salesListModal').on('shown.bs.modal', function () {
            if (!$.fn.dataTable.isDataTable('#sales-list')) {
                vm.initializeDataTable();
            } else {
                vm.reloadDataTable();
            }
        });

        $('#article_code').on('keyup', this.reloadDataTable);
        $('#sales_start_date, #sales_end_date').on('change', this.reloadDataTable);

        $('#voucher_code').on('keyup', this.reloadGiftVoucherTable);
        $('#voucher_start_date, #voucher_end_date').on('change', this.reloadGiftVoucherTable);

        $('#voucher_start_date').flatpickr({
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'F j, Y',
            onChange: function (selectedDates, dateStr) {
                $('#voucher_end_date').flatpickr({
                    minDate: dateStr,
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'F j, Y'
                });
            }
        });

        $('#giftVoucherSaleListModal').on('shown.bs.modal', function () {
            if (!$.fn.dataTable.isDataTable('#gift-voucher-sales-list')) {
                vm.initializeGiftVoucherTable();
            } else {
                vm.reloadGiftVoucherTable();
            }
        });

        $('#backToSalesListBtn').on('click', function () {
            $('#giftVoucherSaleListModal').modal('hide');
            setTimeout(function () {
                $('#salesListModal').modal('show');
            }, 100);
        });

        $(document).on('click', 'tr#sales-list-row', function () {
            const isActive = $(this).children('td').hasClass('bg-dark');
            $('tr#sales-list-row').children('td').removeClass('bg-dark');
            if (!isActive) {
                $(this).children('td').addClass('bg-dark');
            }
        });

        $('#sales_start_date').flatpickr({
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'F j, Y',
            onChange: function (selectedDates, dateStr) {
                $('#sales_end_date').flatpickr({
                    minDate: dateStr,
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'F j, Y'
                });
            }
        });

        $('#salesListModal').on('hidden.bs.modal', function () {
            if ($.fn.dataTable.isDataTable('#sales-list')) {
                $('#sales-list').DataTable().clear().destroy();
                vm.table = null;
            }
            $('.modal-backdrop').remove();
        });

        $('#giftVoucherSaleListModal').on('hidden.bs.modal', function () {
            if ($.fn.dataTable.isDataTable('#gift-voucher-sales-list')) {
                $('#gift-voucher-sales-list').DataTable().clear().destroy();
                vm.giftVoucherTable = null;
            }
        });
    },
    beforeUnmount() {
        $(document).off('click', '#sales-list tbody tr');
    }
};
</script>
