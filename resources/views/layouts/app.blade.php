<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Kular fashion POS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="assets/images/favicon.png">

    <link href="{{ asset('assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

    @stack('styles')
    @vite('resources/js/app.js')

    <link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
</head>

<body data-topbar="dark" data-layout="horizontal" data-layout-size="boxed">
    <div id="layout-wrapper">
        @include('layouts.components.header')

        <div class="main-content">
            <div class="page-content mt-2">
                <div class="container-fluid">
                    @yield('content')

                    @if(Route::currentRouteName()==='home')
                        <on-hold-sales-modal></on-hold-sales-modal>
                        <search-article-modal :brands="{{ json_encode($brands) }}" :product-types="{{ json_encode($productTypes) }}" main-url="{{ env('ADMIN_URL') }}"></search-article-modal>
                        <inventory-transfer-modal></inventory-transfer-modal>
                    @endif

                    <!-- Virtual Keypad -->
                    <div id="keypad" class="keypad">
                        <button class="key" data-value="1">1</button>
                        <button class="key" data-value="2">2</button>
                        <button class="key" data-value="3">3</button>
                        <button class="key" data-value="4">4</button>
                        <button class="key" data-value="5">5</button>
                        <button class="key" data-value="6">6</button>
                        <button class="key" data-value="7">7</button>
                        <button class="key" data-value="8">8</button>
                        <button class="key" data-value="9">9</button>
                        <button class="key" data-value="0">0</button>
                        <button id="clearKey" class="key">Clear</button>
                    </div>
                </div>
            </div>

            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            {{ date('Y') }} © Kular Fashion.
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>


    <!-- JAVASCRIPT -->
    <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/metismenu/metisMenu.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>

    @stack('scripts')

    <script>
        window.config = {
            userId: '{{ Auth::id() }}',
            currentUserStore: {{ auth()->user()->branch_id ?? 'null' }},
            euro_to_pound: '{{ setting("euro_to_pound") }}',
            currentStoreName: @json(auth()->user()->branch ? auth()->user()->branch->name : "Default Store"),  // Store Name
            orderReceiptHeader: @json(auth()->user()->branch ? auth()->user()->branch->order_receipt_header ?? setting("order_receipt_header") : setting("order_receipt_header")), 
            storeDetail: @json(auth()->user()->branch), 
        }
    </script>
</body>

</html>
