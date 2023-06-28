<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Authentication info -->
    <meta name="user-id" content="{{ Auth::id() }}" >

    <title>{{ config('app.name', 'Laravel') }} @yield('title')</title>

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudfare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <link href="{{ asset('css/customBootstrap.min.css') }}" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.2.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" rel="stylesheet" />

    <link href="{{ asset('css/AppTheme.css') }}" rel="stylesheet">
    <link href="{{ asset('css/milligram.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- This Stylesheet needs to be placed after app.css  -->
    <link href="{{ asset('css/mobileStyles.css') }}" rel="stylesheet">  
</head>

<body>
    <main>
        @include('partials.navbar')

        <div id="notificationContainer" class="toast-container position-fixed top-0 end-0 p-3"></div>

        <div id="contentContainer" class="pt-3">
            @yield('content')
        </div>

        @include('partials.footer')
    </main>

    @include('layouts.scripts')
</body>

</html>
