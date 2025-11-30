<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ url('assets/images/logo/logors.png') }}">

    <title>{{ 'Bank Garansi | Customer Create' }} - @yield('title')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!--font-awesome-css-->
    <link href="{{ asset('assets') }}/vendor/fontawesome/css/all-1.css" rel="stylesheet">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">

    <!-- iconoir icon css  -->
    <link href="{{ asset('assets') }}/vendor/ionio-icon/css/iconoir-1.css" rel="stylesheet">

    <!-- tabler icons-->
    <link href="{{ asset('assets') }}/vendor/tabler-icons/tabler-icons-1.css" rel="stylesheet" type="text/css">

    <!-- Bootstrap css-->
    <link href="{{ asset('assets') }}/vendor/bootstrap/bootstrap.min-1.css" rel="stylesheet" type="text/css">

    <!-- App css-->
    <link href="{{ asset('assets') }}/css/style-1.css" rel="stylesheet" type="text/css">

    <!-- Responsive css-->
    <link href="{{ asset('assets') }}/css/responsive-1.css" rel="stylesheet" type="text/css">
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="sign-in-bg">
    <div class="app-wrapper d-block">
        <div class="main-container">
            <!-- Body main section starts -->
            {{ $slot }}
            <!-- Body main section ends -->
        </div>
    </div>
    <!-- latest jquery-->
    <script src="{{ asset('assets') }}/js/jquery-3.6.3.min-1.js"></script>

    <!-- Bootstrap js-->
    <script src="{{ asset('assets') }}/vendor/bootstrap/bootstrap.bundle.min-1.js"></script>

</body>

</html>
