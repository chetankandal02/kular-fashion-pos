<div class="modal fade" id="searchArticalModal" tabindex="-1" aria-labelledby="searchArticalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="searchArticalModalLabel">Search Article</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row">
                    {{-- <div class="col-md-4 mb-3">
                        <x-form-input name="search_by_keyword" label="Search"></x-form-input>
                    </div> --}}
                    <div class="col-md-4 mb-3">
                        <label for="search_by_brand">Brand</label>
                        <select name="search_by_brand" id="search_by_brand" class="form-control">
                            <option value="">Select brand</option>
                            @foreach($brands as $brand)
                            <option value="{{ $brand->id }}">{{$brand->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="search_by_product_type">Product Type</label>
                        <select name="search_by_product_type" id="search_by_product_type" class="form-control">
                            <option value="">Select Product Type</option>
                            @foreach($productTypes as $productType)
                            <option value="{{ $productType->id}}">{{$productType->product_type_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <table class="table table-striped table-bordered" id="search-article-modal" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Article Code</th>
                            <th>Manufacture Code</th>
                            <th>Department</th>
                            <th>Brand</th>
                        </tr>
                    </thead>

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

        $(document).ready(function() {
            var table = $('#search-article-modal').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('get.products') }}',
                    data: function (d) {
                        d.brand_id = $('#search_by_brand').val();
                        d.product_type_id = $('#search_by_product_type').val();
                        d.page = Math.floor(d.start / d.length) + 1;
                    }
                },
                columns: [
                    { title: "#", data: 'id'},
                    { title: "Article Code", data: 'article_code' },
                    { title: "Manufacture Code", data: 'manufacture_code' },
                    { title: "Department", data: 'department.name' },
                    { title: "Brand", data: 'brand.name' },
                ],
                order: [[0, 'desc']]
            });
            $('#search_by_brand').on('change', function() {
                table.ajax.reload();
            });

            $('#search_by_product_type').on('change', function() {
                table.ajax.reload();
            });
        });
    });
</script>
@endpush