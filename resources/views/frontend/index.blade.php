<!doctype html>
<html lang="en">

<head>

    <meta charset="utf-8" />
    <title>Kular fashion POS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.png">

    <!-- Bootstrap Css -->
    <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
    <!-- App js -->
    <script src="assets/js/plugin.js"></script>

</head>

<body data-topbar="dark" data-layout="horizontal" data-layout-size="boxed">

    <!-- Begin page -->
    <div id="layout-wrapper">

        <header id="page-topbar">
            <div class="navbar-header">
                <div class="d-flex">
                    <!-- LOGO -->
                    <div class="navbar-brand-box">
                        <a href="#" class="logo logo-dark">
                            <span class="logo-sm">
                                <img src="assets/images/logo.png" alt="" height="22">
                            </span>
                            <span class="logo-lg">
                                <img src="assets/images/logo.png" alt="" height="17">
                            </span>
                        </a>

                        <a href="#" class="logo logo-light">
                            <span class="logo-sm">
                                <img src="assets/images/logo.png" alt="" height="22">
                            </span>
                            <span class="logo-lg">
                                <img src="assets/images/logo.png" class=" bg-white" alt="" height="50">
                            </span>
                        </a>
                    </div>

                    <button type="button" class="btn btn-sm px-3 font-size-16 d-lg-none header-item waves-effect waves-light" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                        <i class="fa fa-fw fa-bars"></i>
                    </button>
                </div>

                <div class="d-flex">
                    <div class="dropdown d-none d-lg-inline-block ms-1">
                        <button type="button" class="btn header-item noti-icon waves-effect" data-bs-toggle="fullscreen">
                            <i class="bx bx-fullscreen"></i>
                        </button>
                    </div>

                    <div class="dropdown d-inline-block">
                        <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="rounded-circle header-profile-user" src="assets/images/user.jpg"
                                alt="Header Avatar">
                            <span class="d-none d-xl-inline-block ms-1">Pummy</span>
                            <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <!-- item-->
                            <a class="dropdown-item" href="#"><i class="bx bx-user font-size-16 align-middle me-1"></i> <span key="t-profile">Profile</span></a>
                            <a class="dropdown-item" href="#"><i class="bx bx-lock-open font-size-16 align-middle me-1"></i> <span key="t-lock-screen">Lock screen</span></a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="#"><i class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i> <span key="t-logout">Logout</span></a>
                        </div>
                    </div>

                </div>
            </div>
        </header>

        <div class="topnav">
            <div class="container-fluid">
                <nav class="navbar navbar-light navbar-expand-lg topnav-menu">

                    <div class="collapse navbar-collapse" id="topnav-menu-content">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a href="#" class="nav-link dropdown-toggle arrow-none" role="button">
                                    <i class="bx bx bx-money me-2"></i><span>Sales List</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link dropdown-toggle arrow-none" role="button">
                                    <i class="bx bx-gift me-2"></i><span>Gift Voucher</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="search-box mb-2">
                                <div class="position-relative">
                                    <input type="text" class="form-control" id="searchProductList" autocomplete="off" placeholder="Search...">
                                    <i class="bx bx-search-alt search-icon"></i>

                                    <ul id="productDropdown" class="dropdown-menu w-100 overflow-y-auto" style="top: 100%; left: 0; max-height: 400px; ">
                                        <li class="container">
                                            <div class="card">
                                                <div class="row w-100 align-items-center">
                                                    <div class="col-md-2">
                                                        <img class="card-img img-fluid" src="https://encrypted-tbn2.gstatic.com/shopping?q=tbn:ANd9GcRt5xXodlpwlG7i65h_RFq87-JTGF2KEBrO5HQ9j0g-kQXNT4-19XV_0nqAnXcjbVFu3tGHQ0-kKRr9pxXv6OXl4hkSC8Qv1TbSgJhGAIxN55WS1B9Ge9uew_VMpgvYtjZvBtszv1A&usqp=CAc" alt="Card image">
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
                                                        <img class="card-img img-fluid" src="https://encrypted-tbn2.gstatic.com/shopping?q=tbn:ANd9GcRt5xXodlpwlG7i65h_RFq87-JTGF2KEBrO5HQ9j0g-kQXNT4-19XV_0nqAnXcjbVFu3tGHQ0-kKRr9pxXv6OXl4hkSC8Qv1TbSgJhGAIxN55WS1B9Ge9uew_VMpgvYtjZvBtszv1A&usqp=CAc" alt="Card image">
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
                    <!-- end row -->

                </div> <!-- container-fluid -->
            </div>
            <!-- End Page-content -->

            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <script>
                                document.write(new Date().getFullYear())
                            </script> © Kular Fashion.
                        </div>
                    </div>
                </div>
            </footer>
        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->


    <!-- JAVASCRIPT -->
    <script src="assets/libs/jquery/jquery.min.js"></script>
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/metismenu/metisMenu.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>

    <script src="assets/js/app.js"></script>
</body>

</html>