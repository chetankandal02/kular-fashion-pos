@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="search-box mb-2">
                <div class="position-relative">
                    <input type="text" class="form-control" id="searchProductList" autocomplete="off"
                        placeholder="Enter barcode number">
                    <i class="bx bx-barcode search-icon"></i>

                    <ul id="productDropdown" class="dropdown-menu w-100 overflow-y-auto"
                        style="top: 100%; left: 0; max-height: 400px; ">
                        <li class="container">
                            <div class="card">
                                <div class="row w-100 align-items-center">
                                    <div class="col-md-2">
                                        <img class="card-img img-fluid"
                                            src="https://encrypted-tbn2.gstatic.com/shopping?q=tbn:ANd9GcRt5xXodlpwlG7i65h_RFq87-JTGF2KEBrO5HQ9j0g-kQXNT4-19XV_0nqAnXcjbVFu3tGHQ0-kKRr9pxXv6OXl4hkSC8Qv1TbSgJhGAIxN55WS1B9Ge9uew_VMpgvYtjZvBtszv1A&usqp=CAc"
                                            alt="Card image">
                                    </div>
                                    <div class="col-md-10">
                                        <div class="card-body">
                                            <h5 class="card-title">TShirt</h5>
                                            <p class="card-text">Short description will be appear here!</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="container">
                            <div class="card">
                                <div class="row w-100 align-items-center">
                                    <div class="col-md-2">
                                        <img class="card-img img-fluid"
                                            src="https://encrypted-tbn2.gstatic.com/shopping?q=tbn:ANd9GcRt5xXodlpwlG7i65h_RFq87-JTGF2KEBrO5HQ9j0g-kQXNT4-19XV_0nqAnXcjbVFu3tGHQ0-kKRr9pxXv6OXl4hkSC8Qv1TbSgJhGAIxN55WS1B9Ge9uew_VMpgvYtjZvBtszv1A&usqp=CAc"
                                            alt="Card image">
                                    </div>
                                    <div class="col-md-10">
                                        <div class="card-body">
                                            <h5 class="card-title">TShirt 2nd product</h5>
                                            <p class="card-text">Short description will be appear here!</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Order Items</h4>
                    <div class="table-responsive">
                        <table class="table align-middle table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="align-middle">Item Code</th>
                                    <th class="align-middle">Description</th>
                                    <th class="align-middle">Color</th>
                                    <th class="align-middle">Size</th>
                                    <th class="align-middle">Brand</th>
                                    <th class="align-middle">Price</th>
                                    <th class="align-middle">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><a href="javascript: void(0);" class="text-body fw-bold">#SK2540</a> </td>
                                    <td>Neal Matthews</td>
                                    <td>Grey</td>
                                    <td>S</td>
                                    <td>Puma</td>
                                    <td>45</td>
                                    <td>
                                        <button type="button" class="btn btn-primary btn-danger waves-effect waves-light">
                                            <i class="bx bx-trash-alt fs-5"></i>
                                        </button>
                                    </td>
                                </tr>

                                <tr>
                                    <td><a href="javascript: void(0);" class="text-body fw-bold">#SK2541</a> </td>
                                    <td>Jamal Burnett</td>
                                    <td>Black</td>
                                    <td>L</td>
                                    <td>Nike</td>
                                    <td>470</td>
                                    <td>
                                        <button type="button" class="btn btn-primary btn-danger waves-effect waves-light">
                                            <i class="bx bx-trash-alt fs-5"></i>
                                        </button>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                    <!-- end table-responsive -->
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h4>Order Summary</h4>

                    <div class="d-flex justify-content-between">
                        <h5>Sub Total</h5>
                        <h5>15</h5>
                    </div>

                    <div class="d-flex justify-content-between">
                        <h5>Total Items</h5>
                        <h5>2</h5>
                    </div>

                    <div class="d-flex justify-content-between">
                        <h5>Grand Total</h5>
                        <h5>30</h5>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-2 pe-1">
                            <button class="btn btn-warning w-100">
                                <i class="mdi mdi-pause font-size-14 me-1"></i>
                                Hold Order
                            </button>
                        </div>
                        <div class="col-6 mb-2 ps-1">
                            <button class="btn btn-danger w-100">
                                <i class="mdi mdi-close align-middle font-size-14"></i>
                                Cancel
                            </button>
                        </div>
                        <div class="col-6 mb-2 pe-1">
                            <button class="btn btn-secondary w-100">
                                <i class="mdi mdi-account-cash font-size-14"></i>
                                Layway
                            </button>
                        </div>
                        <div class="col-6 mb-2 ps-1">
                            <button class="btn btn-dark w-100">
                                <i class="mdi mdi-notebook-edit-outline align-middle font-size-16"></i>
                                Credit Notes
                            </button>
                        </div>
                        <div class="col-6 mb-2 pe-1">
                            <button class="btn btn-success w-100 p-0">
                                <i class="mdi mdi-cash-plus align-middle font-size-24"></i>
                                Tendor
                            </button>
                        </div>
                        <div class="col-6 ps-1">
                            <button class="btn btn-danger w-100">
                                <i class="mdi mdi-keyboard-return align-middle font-size-14"></i>
                                Return Sale
                            </button>
                        </div>
                        <div class="col-6 pe-1">
                            <button class="btn btn-info w-100">
                                <i class="mdi mdi-printer align-middle font-size-16"></i>
                                EOD
                            </button>
                        </div>
                        <div class="col-6 ps-1">
                            <button class="btn btn-dark w-100">
                                <i class="mdi mdi-gift align-middle font-size-16"></i>
                                Gift Voucher
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
