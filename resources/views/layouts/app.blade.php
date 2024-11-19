<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Kular fashion POS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="assets/images/favicon.png">

    <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
    <script src="assets/js/plugin.js"></script>

    @stack('styles')
    @vite('resources/js/app.js')
</head>

<body data-topbar="dark" data-layout="horizontal" data-layout-size="boxed">
    <div id="layout-wrapper">
        @include('layouts.components.header')
        
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
					@yield('content')
                </div>
            </div>

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
    </div>


    <!-- JAVASCRIPT -->
    <script src="assets/libs/jquery/jquery.min.js"></script>
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/metismenu/metisMenu.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>
    <script src="assets/js/app.js"></script>

    @stack('scripts')
</body>
</html>