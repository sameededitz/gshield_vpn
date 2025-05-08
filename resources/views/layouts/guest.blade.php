<!doctype html>
<html lang="en">

<head>
    @include('partials.head')
</head>

<body class="bg-theme bg-theme3">

    <!--wrapper-->
    <div class="wrapper">
        <!--start page content -->
        @yield('content')
        <!--end page content -->
    </div>
    <!--end wrapper-->

    @include('partials.scripts')
</body>

</html>
