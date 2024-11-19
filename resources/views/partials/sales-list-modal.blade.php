<div class="modal fade" id="salesListModal" tabindex="-1" aria-labelledby="salesListModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="salesListModalLabel">Sales List</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-6 col-md-4 mb-3">
                        <x-form-input name="sales_start_date" label="Start Date:"></x-form-input>
                    </div>
                    <div class="col-6 col-md-4 mb-3">
                        <x-form-input name="sales_end_date" label="End Date:"></x-form-input>
                    </div>
                </div>
                
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Customer Name</th>
                            <th>Sales Person</th>
                            <th>Items</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>John Doe</td>
                            <td>Chetan</td>
                            <td>2</td>
                            <td>£249</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Jane Smith</td>
                            <td>Pummy</td>
                            <td>5</td>
                            <td>£499</td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Mark Johnson</td>
                            <td>Chetan</td>
                            <td>6</td>
                            <td>£549</td>
                        </tr>
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
    });
</script>
@endpush