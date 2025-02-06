<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="{{ route('/') }}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="assets/images/logo.png" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="assets/images/logo.png" alt="" height="17">
                    </span>
                </a>

                <a href="{{ route('/') }}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="assets/images/logo.png" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="assets/images/logo.png" class=" bg-white" alt="" height="50">
                    </span>
                </a>
            </div>

            <button type="button" class="btn btn-sm px-3 font-size-16 d-lg-none header-item waves-effect waves-light"
                data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                <i class="fa fa-fw fa-bars"></i>
            </button>

            <div class="dropdown dropdown-mega d-none d-lg-block ms-2">
                <button type="button" class="btn header-item waves-effect" data-bs-toggle="modal" data-bs-target="#searchArticalModal">
                    <span>Search Article</span>
                </button>
            
                <button type="button" class="btn header-item waves-effect" data-bs-toggle="modal" data-bs-target="#salesListModal">
                    <span>Sales List</span>
                </button>
                
                <button type="button" class="btn header-item waves-effect" data-bs-toggle="modal" data-bs-target="#onHoldSalesModal">
                    <span>On Hold Sales</span>
                </button>
                
                <button type="button" class="btn header-item waves-effect" data-bs-toggle="modal" data-bs-target="#transferInventoryModal" :data-current-user-store="{{ auth()->user()->branch_id }}">
                    <span>Transfer Inventory</span>
                </button>
            </div>
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
                    <img class="rounded-circle header-profile-user" src="assets/images/user.jpg" alt="Header Avatar">
                    <span class="d-none d-xl-inline-block ms-1">{{ Auth::user()->name }}</span>
                    <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <!-- item-->
                    <a class="dropdown-item" href="{{ route('profile.index') }}"><i class="bx bx-user font-size-16 align-middle me-1"></i>
                        <span key="t-profile">Profile</span></a>
                    <a class="dropdown-item" href="#"><i
                            class="bx bx-lock-open font-size-16 align-middle me-1"></i> <span key="t-lock-screen">Lock
                            screen</span></a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="{{ route('logout') }}"><i
                            class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i> <span
                            key="t-logout">Logout</span></a>
                </div>
            </div>

        </div>
    </div>
</header>
