<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Lifewill</title>
    <meta charset="utf-8">
    <!--base(href="/")-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, minmum-scale=1, maximum-scale=1">
    <!-- StyleSheet-->
    <link href="{{ URL::asset('admin_assets/css/bootstrap.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ URL::asset('admin_assets/css/fonts.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ URL::asset('admin_assets/css/font-custom-icons.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ URL::asset('admin_assets/css/button.css') }}" type="text/css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css" />
    <!--owl-carousel-->
    <link href="{{ URL::asset('admin_assets/css/owl.carousel.cs') }}s" type="text/css" rel="stylesheet">
    <link href="{{ URL::asset('admin_assets/css/owl.theme.default.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ URL::asset('admin_assets/css/style.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ URL::asset('admin_assets/css/responsive.css') }}" type="text/css" rel="stylesheet">
    <!-- Scripting-->
    <script src="{{ URL::asset('admin_assets/js/modernizr.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('admin_assets/js/jquery.min.js') }}" type="text/javascript"></script>


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">


  <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.js"></script>

  <script src="http://maps.googleapis.com/maps/api/js?v=3&amp;libraries=places&key=AIzaSyANcuv0LgkyfghddzMStubt7ZoHBxU4Hx8" type="text/javascript"></script>

</head>
<style>
li.activesx {
  color: #1f6486 !important;
}
</style>
<body>


@include('admin.header')

      <div class="main-cotainer dashboard-container">
      @include('admin.sidebar')

      @yield('content')

       </div>
    <script src="{{ URL::asset('admin_assets/js/owl.carousel.min.js') }}" type="text/javascript"></script>
	<script src="{{ URL::asset('admin_assets/js/bootstrap.min.js') }}" type="text/javascript"></script>
	<!--<script src="{{ URL::asset('admin_assets/js/jquery.nicescroll.js') }}" type="text/javascript"></script>-->
	<!--<script src="{{ URL::asset('admin_assets/js/main.js') }}" type="text/javascript"></script>-->
    <!-- <script src="{{ URL::asset('admin_assets/js/app.js') }}" type="text/javascript"></script> -->
    <!-- <script src="{{ URL::asset('admin_assets/js/routing.js') }}" type="text/javascript"></script> -->
<script>
$(document).ready(function(){
       setTimeout(function() {
         $('.alert-success').fadeOut();
       }, 5000); // <-- time in milliseconds
   });
</script>

</body>

</html>
