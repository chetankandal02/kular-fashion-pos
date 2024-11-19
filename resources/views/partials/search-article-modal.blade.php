<div class="modal fade" id="searchArticalModal" tabindex="-1" aria-labelledby="searchArticalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="searchArticalModalLabel">Search Article</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <x-form-input name="search_by_keyword" label="Search"></x-form-input>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="search_by_brand">Brand</label>
                        <select name="search_by_brand" id="search_by_brand" class="form-control">
                            <option value="">Select brand</option>
                            <option value="1">Adidas</option>
                            <option value="2">Puma</option>
                            <option value="3">Nike</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="search_by_product_type">Product Type</label>
                        <select name="search_by_product_type" id="search_by_product_type" class="form-control">
                            <option value="">Select Product Type</option>
                            <option value="1">T-Shirt</option>
                            <option value="2">Shirt</option>
                        </select>
                    </div>
                </div>
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Article Code</th>
                            <th>Manufacture Code</th>
                            <th>Department</th>
                            <th>Brand</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>300001</td>
                            <td>123456</td>
                            <td>Men</td>
                            <td>Adidas</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>300002</td>
                            <td>asdasd</td>
                            <td>Women</td>
                            <td>Puma</td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>300003</td>
                            <td>Chetan</td>
                            <td>Unisex</td>
                            <td>Nike</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


@push('scripts')
<script>
    $(function(){
        $('#search_by_brand').chosen({
            width: "100%"
        });

        $('#search_by_product_type').chosen({
            width: "100%"
        });
    })
</script>
@endpush