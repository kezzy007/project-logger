<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Laravel</title>

            <!-- Font Awesome -->
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
            <!-- Bootstrap core CSS -->
            <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
            
            <!-- For local development -->
            <!-- <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}"/> -->

            <!-- Material Design Bootstrap -->
            <!-- <link href="{{ asset('css/mdb.css') }}" rel="stylesheet"> -->
            <link href="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.5.0/css/mdb.min.css" rel="stylesheet">
        </head>
    <body>
    <div class="container-fluid p-0">
        @include('sections.header')

        @yield('content')
    </div>
    </body>
        <!-- JQuery -->
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <!-- Jquery for local development -->
        <!-- <script src="{{ asset('js/jquery.min.js') }}"></script> -->

        <!-- Bootstrap tooltips -->
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.13.0/umd/popper.min.js"></script>
        <!-- Bootstrap core JavaScript -->
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0/js/bootstrap.min.js"></script>
        
        <!-- For local development -->
        <!-- <script src="{{ asset('js/jquery.min.js') }}"></script> -->

        <!-- MDB core JavaScript -->
        <!-- <script type="text/javascript" src="{{ asset('js/mdb.js') }}"></script> -->
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.5.0/js/mdb.min.js"></script>
        <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
