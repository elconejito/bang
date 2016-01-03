<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <meta name="description" content="@yield('description')">
    <meta name="author" content="@yield('author')">

    <title>@yield('title') | Bang</title>

    <!-- Bootstrap core CSS -->
    <link href="{{ asset('/assets/css/app.css') }}" rel="stylesheet">
    
</head>

<body>

@include('partials.errors')

@include('partials.header')

@section('content')
    This is the default content
@show

@include('partials.footer')

<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="{{ asset('/assets/js/vendors.js') }}"></script>
<script src="{{ asset('/assets/js/app.js') }}"></script>
</body>
</html>
