<div class="modal fade" id="salesListModal" tabindex="-1" aria-labelledby="salesListModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="salesListModalLabel">Sales List</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-6 col-md-4 mb-3">
                        <x-form-input name="article_code" label="Article Code:"></x-form-input>
                    </div>
                    <div class="col-6 col-md-4 mb-3">
                        <x-form-input name="sales_start_date" label="Start Date:"></x-form-input>
                    </div>
                    <div class="col-6 col-md-4 mb-3">
                        <x-form-input name="sales_end_date" label="End Date:"></x-form-input>
                    </div>
                </div>
                
                <table class="table table-striped table-bordered" id="sales-list" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Code</th>
                            <th>Customer Name</th>
                            <th>Sales Person</th>
                            <th>Items</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#sales_start_date').flatpickr({
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'F j, Y',
            onChange: function(selectedDates, dateStr, instance) {
                $('#sales_end_date').flatpickr({
                    minDate: dateStr,
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'F j, Y'
                });
            }
        });

        var table = $('#sales-list').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('get.orders') }}',
                data: function (d) {
                    d.article_code = $('#article_code').val();
                    d.sales_start_date = $('#sales_start_date').val();
                    d.sales_end_date = $('#sales_end_date').val();
                    d.page = Math.floor(d.start / d.length) + 1;
                }
            },
            columns: [
                { title: "#", data: 'id'},
                { title: "Code", data: 'code'},
                { title: "Customer Name", data: 'customer_name' },
                { title: "Sales Person", data: 'sales_person_name' },
                { title: "Items", data: 'total_items' },
                { 
                    title: "Amount", 
                    data: 'total_amount',
                    render: function(data, type, row) {
                        return '€' + parseFloat(data).toFixed(2);
                    }
                },
            ],
            order: [[0, 'desc']],
            drawCallback: function(settings) {
                $('#sales-list th, #sales-list td').addClass('p-1');
            }
        });

        $('#article_code').on('keyup', function() {
            table.ajax.reload();
        });

        $('#sales_start_date, #sales_end_date').on('change', function() {
            table.ajax.reload();
        });
    });
</script>
@endpush