<div class="modal fade" id="searchArticalModal" tabindex="-1" aria-labelledby="searchArticalModalLabel">
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
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="search_by_product_type">Product Type</label>
                        <select name="search_by_product_type" id="search_by_product_type" class="form-control">
                            <option value="">Select Product Type</option>
                            @foreach ($productTypes as $productType)
                                <option value="{{ $productType->id }}">{{ $productType->product_type_name }}</option>
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
                            <th>Action</th>
                        </tr>
                    </thead>

                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(function() {
            $(document).on('click', '.pick-product-for-sale', function() {
                let product = JSON.parse($(this).attr('data-product'));
                console.log('product',product)
            });

            var table = $('#search-article-modal').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('get.products') }}',
                    data: function(d) {
                        d.brand_id = $('#search_by_brand').val();
                        d.product_type_id = $('#search_by_product_type').val();
                        d.page = Math.floor(d.start / d.length) + 1;
                    }
                },
                columns: [{
                        title: "#",
                        data: 'id'
                    },
                    {
                        title: "Article Code",
                        data: 'article_code'
                    },
                    {
                        title: "Manufacture Code",
                        data: 'manufacture_code'
                    },
                    {
                        title: "Department",
                        data: 'department.name'
                    },
                    {
                        title: "Brand",
                        data: 'brand.name'
                    },
                    {
                        title: "Action",
                        data: null,
                        render: function(data, type, row) {
                            let tempArticle = {
                                id: row.id,
                                colors: row.colors,
                                sizes: row.sizes
                            }

                            return `<button class="btn btn-primary btn-sm py-0 px-1 pick-product-for-sale" data-product='${JSON.stringify(tempArticle)}'>
                                <i class="fas fa-hand-holding-usd"></i>
                            </button>`
                        }
                    },
                ],
                order: [
                    [0, 'desc']
                ],
                drawCallback: function(settings) {
                    $('#search-article-modal th, #search-article-modal td').addClass('p-1');
                }
            });

            $('#search_by_brand').chosen({
                width: "100%"
            });

            $('#search_by_product_type').chosen({
                width: "100%"
            });

            $('#search_by_brand').on('change', function() {
                table.ajax.reload();
            });

            $('#search_by_product_type').on('change', function() {
                table.ajax.reload();
            });
        });
    </script>
@endpush
