<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dunia Coating')</title>

    {{-- Google Font --}}
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    {{-- AdminLTE --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    
    {{-- DataTables --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css">

    {{-- Custom Style Stack --}}
    @stack('styles')
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

    @if (!request()->routeIs('login'))
        {{-- Sidebar --}}
        @include('layouts.sidebar')

        {{-- Content Wrapper --}}
        <div class="content-wrapper">
            {{-- Topbar / Header --}}
            <div class="content-header">
                <div class="container-fluid">
                    @yield('content-header')
                </div>
            </div>

            {{-- Main Content --}}
            <div class="content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>
        </div>
    @else
        {{-- Login Page without Sidebar --}}
        <div class="content-wrapper" style="margin-left: 0;">
            <div class="content d-flex align-items-center justify-content-center min-vh-100 bg-light">
                @yield('login-content')
            </div>
        </div>
    @endif

</div>

{{-- Scripts --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

{{-- DataTables --}}
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap4.min.js"></script>

{{-- Custom Script Stack --}}
@stack('scripts')
</body>
</html>
