<template>
    <div class="modal fade" id="salesListModal" tabindex="-1" aria-labelledby="salesListModalLabel">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header py-2 px-3">
                    <h5 class="modal-title" id="salesListModalLabel">Sales List</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body py-1">
                    <div class="row">
                        <div class="col-6 col-md-4 mb-1">
                            <label for="article_code" class="mb-0">Article Code:</label>
                            <input id="article_code" class="form-control h-50" />
                        </div>
                        <div class="col-6 col-md-4 mb-1">
                            <label for="article_code" class="mb-0">From Date:</label>
                            <input id="sales_start_date" class="form-control h-50" />
                        </div>
                        <div class="col-6 col-md-4 mb-1">
                            <label for="article_code" class="mb-0">To Date:</label>
                            <input id="sales_end_date" class="form-control h-50" />
                        </div>
                    </div>

                    <table class="table table-bordered table-sm" id="sales-list" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Code</th>
                                <th>Customer Name</th>
                                <th>Sales Person</th>
                                <th>Items</th>
                                <th>Amount</th>
                                <th>Date & Time</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="saleDetailModal" tabindex="-1" aria-labelledby="salesDetailModalLabel">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="salesDetailModalLabel">Sales Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body" v-if="saleDetails">
                    <div class="row">
                        <div class="col-md-4">
                            <h5 class="fs-6">Sale ID: <strong>#{{ saleDetails.code }}</strong></h5>
                        </div>
                        <div class="col-md-4">
                            <h5 class="fs-6">Sale Date: <strong>{{ formatDateTime(saleDetails.created_at) }}</strong>
                            </h5>
                        </div>
                        <div class="col-md-4">
                            <h5 class="fs-6">Sold By: <strong>{{ saleDetails.sales_person.name }}</strong></h5>
                        </div>
                        <div class="col-md-4">
                            <h5 class="fs-6">Total Sale Items: <strong>{{ saleDetails.total_items -
                                saleDetails.total_return_items }}</strong></h5>
                        </div>
                        <div class="col-md-4">
                            <h5 class="fs-6">Total Return Items: <strong>{{ saleDetails.total_return_items }}</strong>
                            </h5>
                        </div>
                        <div class="col-md-4">
                            <h4 class="fs-6">Total Amount: <strong>£{{ saleDetails.total_amount }}</strong></h4>
                        </div>
                    </div>

                    <hr class="m-1">
                    <div v-if="saleDetails.payment_methods.length > 0">
                        <h5>Payment Methods:</h5>
                        <div class="row">
                            <div class="col-md-4" v-for="(paymentMethod, index) in saleDetails.payment_methods"
                                :key="index">
                                <h5 class="fs-6 mb-0">{{ paymentMethod.method }}:
                                    <strong>£{{ paymentMethod.original_amount }}</strong>
                                </h5>
                            </div>
                        </div>
                        <hr class="m-1">
                    </div>

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
                            <tr v-for="(item, index) in saleDetails.order_items" :key="index">
                                <td>{{ index + 1 }}</td>
                                <td>{{ item.article_code }}</td>
                                <td>{{ item.description }}</td>
                                <td>{{ item.color_name }}</td>
                                <td>{{ item.size }}</td>
                                <td>{{ item.brand_name }}</td>
                                <td :class="{'text-danger': item.flag !== 'SALE'}"><span v-if="item.flag !== 'SALE'">-</span>£{{ item.original_price }}</td>
                                <td :class="{'text-danger': item.flag !== 'SALE'}"><span v-if="item.flag !== 'SALE'">-</span>£{{ item.changed_price }}</td>
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

export default {
    data() {
        return {
            table: null,
            saleDetails: null
        };
    },
    mounted() {
        if (localStorage.getItem('orderItems')) {
            this.orderItems = JSON.parse(localStorage.getItem('orderItems'));
        }

        if (localStorage.getItem('returnItems')) {
            this.returnItems = JSON.parse(localStorage.getItem('returnItems'));
        }
    },
    methods: {
        initializeDataTable() {
            const vm = this;

            this.table = $('#sales-list').DataTable({
                processing: true,
                serverSide: true,
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
                        data: 'total_amount',
                        render: function (data, type, row) {
                            return '£' + parseFloat(data).toFixed(2);
                        }
                    },
                    { title: 'Date & Time', data: 'date_time' },
                    {
                        title: "Action",
                        data: null,
                        render: function (data, type, row) {
                            return `<button class="btn btn-primary btn-sm me-2 py-0 px-1 view-sale-details" data-sale-id='${row.id}'>
                            <i class="fas fa-eye"></i>
                        </button>`
                        }
                    },
                ],
                order: [[0, 'desc']],
                drawCallback: function (settings) {
                    $('.view-sale-details').on('click', (event) => {
                        const saleId = $(event.currentTarget).attr('data-sale-id');
                        vm.viewSaleDetail(saleId);
                    });
                },
                createdRow: function (row, data, dataIndex) {
                    if (data.is_price_changed === true) {
                        $(row).find('td').addClass('bg-warning');
                    }
                }
            });
        },
        reloadDataTable() {
            if (this.table) {
                this.table.ajax.reload();
            }
        },
        formatDateTime(timestamp) {
            let now = new Date(timestamp);
            let day = now.getDate();
            let month = now.getMonth() + 1;
            let year = now.toLocaleDateString('en', {year: '2-digit'});
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
        async viewSaleDetail(saleId) {
            //$('#salesListModal').modal('hide');
            $('#saleDetailModal').modal('show');

            const response = await axios.get(`/sale-detail/${saleId}`);
            this.saleDetails = response.data.sale;
        }
    },
    mounted() {
        this.initializeDataTable();

        $('#article_code').on('keyup', () => {
            this.reloadDataTable();
        });

        $('#sales_start_date, #sales_end_date').on('change', () => {
            this.reloadDataTable();
        });

        $('[data-bs-target="#salesListModal"]').on('click', () => {
            this.reloadDataTable();
        });

        $('#sales_start_date').flatpickr({
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'F j, Y',
            onChange: function (selectedDates, dateStr, instance) {
                $('#sales_end_date').flatpickr({
                    minDate: dateStr,
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'F j, Y'
                });
            }
        });
    }
};
</script>

<style scoped></style>