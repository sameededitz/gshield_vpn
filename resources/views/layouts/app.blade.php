<!doctype html>
<html lang="en">

<head>
	@include('partials.head')
</head>

<body class="bg-theme bg-theme3">

	<!--wrapper-->
	<div class="wrapper">
		<!--sidebar wrapper -->
		@include('partials.sidebar')
		<!--end sidebar wrapper -->
		<!--start header -->
		@include('partials.navbar')
		<!--end header -->
		<!--start page wrapper -->
		<div class="page-wrapper">
			<div class="page-content">
				
				<!--start page content -->
				@yield('content')
				<!--end page content -->

			</div>
		</div>
		<!--end page wrapper -->
		<!--start overlay-->
		<div class="overlay toggle-icon"></div>
		<!--end overlay-->
		<!--End Back To Top Button-->
		<footer class="page-footer">
			<p class="mb-0">Copyright © {{ date('Y') }}. All right reserved.</p>
		</footer>
	</div>
	<!--end wrapper-->
	
    @include('partials.scripts')
</body>

</html>