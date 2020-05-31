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

    <!-- Core CSS -->
    <link href="{{ asset('/assets/css/app.css') }}" rel="stylesheet">

</head>

<body>

<div id="app" class="app">
@section('content')
    This is the default content
@show
</div><!-- /.container -->

@include('layouts.partials.footer')

<!-- JavaScript -->
<script src="{{ asset('/assets/js/manifest.js') }}"></script>
<script src="{{ asset('/assets/js/vendor.js') }}"></script>
<script src="{{ asset('/assets/js/app.js') }}"></script>
</body>
</html>
