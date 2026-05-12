<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="icon" href="{{ url('assets/images/logo/set-logo.png') }}">


    <meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
    <meta content="IE=edge" http-equiv="X-UA-Compatible">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta
        content="Portal pelanggan untuk manajemen customer, pembuatan customer baru, bank garansi, dan layanan terkait"
        name="description">
    <meta content="customer portal, customer create, bank garansi, manajemen pelanggan, admin, finance" name="keywords">
    <meta content="Sinarmeadow" name="author">
    <link href="{{ asset('assets/') }}/images/logo/set-logo.png" rel="icon" type="image/x-icon">
    <link href="{{ asset('assets/') }}/images/logo/set-logo.png" rel="shortcut icon" type="image/x-icon">

    <title>Customer Portal - @yield('title')</title>

    <!-- Fonts -->

    <!--font-awesome-css-->
    <link href="{{ asset('assets/') }}/vendor/fontawesome/css/all.css" rel="stylesheet">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">


    <!-- iconoir icon css  -->
    <link href="{{ asset('assets/') }}/vendor/ionio-icon/css/iconoir.css" rel="stylesheet">

    <!-- tabler icons-->
    <link href="{{ asset('assets/') }}/vendor/tabler-icons/tabler-icons.css" rel="stylesheet" type="text/css">

    <!--animation-css-->
    <link href="{{ asset('assets/') }}/vendor/animation/animate.min.css" rel="stylesheet">

    <!--flag Icon css-->
    <link href="{{ asset('assets/') }}/vendor/flag-icons-master/flag-icon.css" rel="stylesheet" type="text/css">

    <!-- Bootstrap css-->
    <link href="{{ asset('assets/') }}/vendor/bootstrap/bootstrap.min.css" rel="stylesheet" type="text/css">

    <!-- simplebar css-->
    <link href="{{ asset('assets/') }}/vendor/simplebar/simplebar.css" rel="stylesheet" type="text/css">

    <!-- App css-->
    <link href="{{ asset('assets/') }}/css/style.css" rel="stylesheet" type="text/css">

    <!-- Responsive css-->
    <link href="{{ asset('assets/') }}/css/responsive.css" rel="stylesheet" type="text/css">

    <!-- Data Table css-->
    <link href="{{ asset('assets') }}/vendor/datatable/jquery.dataTables.min.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets') }}/vendor/datatable/datatable2/buttons.dataTables.min.css" rel="stylesheet"
        type="text/css">

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.7/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.8/css/responsive.bootstrap5.css">

    <style>
        /* --- Definisi Warna Status --- */
        :root {
            --bs-primary-rgb: 72, 94, 222;
            --bs-success-rgb: 26, 172, 110;
            --bs-warning-rgb: 247, 184, 75;
            --bs-danger-rgb: 239, 71, 111;
            --bs-info-rgb: 23, 162, 184;
        }

        /* --- Kartu Metrik KPI --- */
        .metric-card {
            border: 1px solid #e9ecef;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background-color: #ffffff;
        }

        .metric-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(var(--bs-primary-rgb), 0.08);
        }

        .metric-icon {
            padding: 12px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(var(--bs-primary-rgb), 0.1);
            color: rgb(var(--bs-primary-rgb));
            font-size: 1.5rem;
            /* Ukuran icon ti */
        }

        .metric-card.success .metric-icon {
            background-color: rgba(var(--bs-success-rgb), 0.1);
            color: rgb(var(--bs-success-rgb));
        }

        .metric-card.warning .metric-icon {
            background-color: rgba(var(--bs-warning-rgb), 0.1);
            color: rgb(var(--bs-warning-rgb));
        }

        .metric-card.danger .metric-icon {
            background-color: rgba(var(--bs-danger-rgb), 0.1);
            color: rgb(var(--bs-danger-rgb));
        }

        .metric-card.info .metric-icon {
            background-color: rgba(var(--bs-info-rgb), 0.1);
            color: rgb(var(--bs-info-rgb));
        }

        .metric-value {
            font-size: 2rem;
            font-weight: 700;
            line-height: 1.2;
        }

        .metric-change {
            font-size: 0.85rem;
            font-weight: 500;
        }

        /* --- Daftar Tindakan & Log Aktivitas --- */
        .action-list-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid #f0f0f0;
            transition: background-color 0.2s ease;
        }

        .action-list-item:hover {
            background-color: #fcfcfc;
            cursor: pointer;
        }

        .action-list-item:last-child {
            border-bottom: none;
        }

        .action-icon {
            flex-shrink: 0;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            margin-right: 1rem;
        }

        /* --- Badge Status --- */
        .status-badge {
            padding: 0.3em 0.7em;
            font-size: 0.8rem;
            font-weight: 600;
            border-radius: 20px;
        }

        .status-pending {
            background-color: rgba(var(--bs-warning-rgb), 0.15);
            color: #a1741c;
        }

        .status-approved {
            background-color: rgba(var(--bs-success-rgb), 0.1);
            color: rgb(var(--bs-success-rgb));
        }

        .status-rejected {
            background-color: rgba(var(--bs-danger-rgb), 0.1);
            color: rgb(var(--bs-danger-rgb));
        }

        .status-processing {
            background-color: rgba(var(--bs-info-rgb), 0.1);
            color: rgb(var(--bs-info-rgb));
        }

        /* --- Visualisasi Alur Kerja --- */
        .workflow-guide .nav-tabs .nav-link {
            font-weight: 600;
            color: #6c757d;
            border-bottom-width: 3px;
            border-color: transparent;
            padding: 0.75rem 1rem;
        }

        .workflow-guide .nav-tabs .nav-link.active {
            color: rgb(var(--bs-primary-rgb));
            border-color: rgb(var(--bs-primary-rgb));
        }

        .workflow-step {
            display: flex;
            align-items: center;
            position: relative;
            padding: 0.75rem 0;
        }

        .workflow-step .step-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #eef2ff;
            color: #1e40af;
            font-weight: 600;
            flex-shrink: 0;
            margin-right: 1rem;
            z-index: 2;
        }

        .workflow-step .step-content strong {
            display: block;
            font-size: 1rem;
            color: #333;
        }

        .workflow-step .step-content small {
            color: #555;
            font-size: 0.9rem;
        }

        .workflow-connector {
            position: absolute;
            left: 20px;
            /* Tengahkan dengan ikon */
            top: 0;
            width: 2px;
            height: 100%;
            background-color: #dbeafe;
            z-index: 1;
        }

        .workflow-step:first-child .workflow-connector {
            top: 50%;
            height: 50%;
        }

        .workflow-step:last-child .workflow-connector {
            height: 50%;
        }

        /* Styling Titik Keputusan */
        .workflow-decision {
            border-left: 3px solid rgb(var(--bs-warning-rgb));
            background-color: rgba(var(--bs-warning-rgb), 0.05);
            padding: 1rem;
            margin-left: 1rem;
            border-radius: 8px;
            margin-top: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .workflow-decision .branch {
            margin-left: 2.5rem;
            position: relative;
            padding: 0.25rem 0;
        }

        .workflow-decision .branch::before {
            content: '';
            position: absolute;
            left: -20px;
            top: 12px;
            width: 15px;
            height: 2px;
            background-color: rgb(var(--bs-warning-rgb));
        }

        .workflow-decision.bg-light-danger {
            border-left-color: rgb(var(--bs-danger-rgb));
            background-color: rgba(var(--bs-danger-rgb), 0.05);
        }

        .workflow-decision.bg-light-danger .branch::before {
            background-color: rgb(var(--bs-danger-rgb));
        }

        /* Penyesuaian Responsif untuk Garis Pemisah */
        @media (max-width: 991.98px) {
            .border-end-lg {
                border-right: none !important;
                border-bottom: 1px solid #dee2e6;
                padding-bottom: 1rem;
                margin-bottom: 1rem;
            }
        }
    </style>

    @stack('css')
    <!-- Scripts -->
    @vite(['resources/js/app.js'])
</head>

<body>
    <div class="app-wrapper gold">

        <div class="loader-wrapper">
            <div class="app-loader">
                <span></span>
                <span></span>
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>

        <!-- Menu Navigation starts -->
        @include('layouts.partials.nav')
        <!-- Menu Navigation ends -->


        <div class="app-content">
            <div class="">

                <!-- Header Section starts -->
                @include('layouts.partials.header')
                <!-- Header Section ends -->

                <!-- Body main section starts -->
                <main>
                    <div class="container-fluid mt-3">

                        {{ $slot }}

                    </div>
                </main>
                <!-- Body main section ends -->

                <!-- tap on top -->
                <div class="go-top">
                    <span class="progress-value">
                        <i class="ti ti-chevron-up"></i>
                    </span>
                </div>

                <!-- Footer Section starts-->
                @include('layouts.partials.footer')
                <!-- Footer Section ends-->

            </div>
        </div>
    </div>

    <!-- latest jquery-->
    <script src="{{ asset('assets') }}/js/jquery-3.6.3.min.js"></script>


    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/datatable/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.8/js/dataTables.responsive.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.8/js/responsive.bootstrap5.js"></script>

    <!-- Simple bar js-->
    <script src="{{ asset('assets') }}/vendor/simplebar/simplebar.js"></script>

    <!-- phosphor js -->
    <script src="{{ asset('assets') }}/vendor/phosphor/phosphor.js"></script>

    <!-- Bootstrap js-->
    <script src="{{ asset('assets') }}/vendor/bootstrap/bootstrap.bundle.min.js"></script>

    <!-- App js-->
    <script src="{{ asset('assets') }}/js/script.js"></script>

    <!-- Customizer js-->
    {{-- <script src="{{ asset('assets') }}/js/customizer.js"></script> --}}

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- local DataTables core removed to avoid conflict with CDN version -->

    <!-- Select2 -->
    <script src="{{ asset('assets/vendor/select/select2.min.js') }}"></script>
    <!--js-->
    <script src="{{ asset('assets') }}/js/select.js"></script>




    @stack('scripts')
</body>

</html>
