<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="icon" href="{{ url('assets/images/logo/logors.png') }}">


    <meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
    <meta content="IE=edge" http-equiv="X-UA-Compatible">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Website untuk form pengeluaran barang di Sinarmeadow" name="description">
    <meta content="requisition slip, sinarmeadow, pengeluaran barang, form, inventory, admin" name="keywords">
    <meta content="Sinarmeadow" name="author">
    <link href="{{ asset('assets/') }}/images/logo/favicon.png" rel="icon" type="image/x-icon">
    <link href="{{ asset('assets/') }}/images/logo/favicon.png" rel="shortcut icon" type="image/x-icon">

    <title>Bank Garansi | Customer Create - @yield('title')</title>

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

    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/2.3.4/css/dataTables.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.css">

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
    {{-- @vite(['resources/js/app.js']) --}}
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

    <!--customizer-->
    {{-- <button class="customizer-btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#customizerOptions"
        aria-controls="customizerOptions">
        <i class="ti ti-settings-2"></i>
    </button>

    <div class="offcanvas offcanvas-end app-customizer" data-bs-scroll="true" tabindex="-1" id="customizerOptions"
        aria-labelledby="customizerOptionsLabel">

        <div class="offcanvas-header flex-wrap bg-primary">
            <h5 class="offcanvas-title text-white" id="customizerOptionsLabel"> Admin Customizer </h5>
            <p class="d-block text-white opacity-75">its time to style according to your choice ..!</p>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <div class="offcanvas-body">
            <div class="app-divider-v secondary py-3">
                <h6 class="mt-2">Sidebar option</h6>
            </div>
            <ul class="sidebar-option">
                <li class="vertical-sidebar">
                    <ul>
                        <li class="header"></li>
                        <li class="sidebar"></li>
                        <li class="body"> <span class="badge text-bg-secondary b-r-6"> Vertical</span> </li>
                    </ul>
                </li>
                <li class="horizontal-sidebar">
                    <ul>
                        <li class="header h-20"><span class="badge text-bg-secondary b-r-6"> Horizontal</span></li>
                        <li class="body w-100"></li>
                    </ul>
                </li>
                <li class="dark-sidebar">
                    <ul>
                        <li class="header"></li>
                        <li class="sidebar bg-dark-600"></li>
                        <li class="body"><span class="badge text-bg-secondary b-r-6"> Dark </span></li>
                    </ul>
                </li>
            </ul>

            <div class="app-divider-v secondary py-3">
                <h6 class="mt-2">Layout option</h6>
            </div>
            <ul class="layout-option">
                <li class="ltr">
                    <ul>
                        <li class="header"></li>
                        <li class="sidebar"></li>
                        <li class="body"><span class="badge text-bg-secondary b-r-6"> LTR </span></li>
                    </ul>
                </li>
                <li class="rtl">
                    <ul>
                        <li class="header"></li>
                        <li class="body"> <span class="badge text-bg-secondary b-r-6"> RTL </span> </li>
                        <li class="sidebar"></li>
                    </ul>
                </li>
                <li class="box-layout">
                    <ul>
                        <li class="header"></li>
                        <li class="sidebar"></li>
                        <li class="body"> <span class="badge text-bg-secondary b-r-6"> Box </span> </li>
                    </ul>
                </li>
            </ul>
            <h6 class="mt-3">Color Hint</h6>
            <ul class="color-hint p-0">
                <li class="default">
                    <div></div>
                </li>
                <li class="gold">
                    <div></div>
                </li>
                <li class="warm">
                    <div></div>
                </li>
                <li class="happy">
                    <div></div>
                </li>
                <li class="nature">
                    <div></div>
                </li>
                <li class="hot">
                    <div></div>
                </li>
            </ul>
            <div class="app-divider-v secondary py-3">
                <h6 class="mt-2 font-primary">Text size</h6>
            </div>
            <ul class="text-size">
                <li class="small-text"> sm </li>
                <li class="medium-text"> md </li>
                <li class="large-text"> lg </li>
            </ul>
        </div>

        <div class="offcanvas-footer">
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-danger w-100" onclick="resetCustomizer()">Reset</button>
                <a type="button" class="btn btn-success w-100"
                    href="https://themeforest.net/user/la-themes/portfolio" target="_blank">Buy Now</a>
            </div>
            <div class="d-flex gap-2 mt-2">
                <a type="button" class="btn btn-primary w-100" href="mailto:teqlathemes@gmail.com"
                    target="_blank">Support</a>
                <a type="button" class="btn btn-dark w-100" href="document.html" target="_blank">Document</a>
            </div>

        </div>

    </div> --}}

    <!-- latest jquery-->
    <script src="{{ asset('assets') }}/js/jquery-3.6.3.min.js"></script>

    <script src="//cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>

    <!-- Simple bar js-->
    <script src="{{ asset('assets') }}/vendor/simplebar/simplebar.js"></script>

    <!-- phosphor js -->
    <script src="{{ asset('assets') }}/vendor/phosphor/phosphor.js"></script>

    <!-- Bootstrap js-->
    <script src="{{ asset('assets') }}/vendor/bootstrap/bootstrap.bundle.min.js"></script>

    <!-- App js-->
    <script src="{{ asset('assets') }}/js/script.js"></script>

    <!-- Customizer js-->
    <script src="{{ asset('assets') }}/js/customizer.js"></script>

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- latest jquery-->
    <script src="{{ asset('assets') }}/vendor/datatable/jquery.dataTables.min.js"></script>

    <!-- Select2 -->
    <script src="{{ asset('assets/vendor/select/select2.min.js') }}"></script>
    <!--js-->
    <script src="{{ asset('assets') }}/js/select.js"></script>


    @stack('scripts')

    {{-- // weather js --}}
    {{-- <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        navigator.geolocation.getCurrentPosition(function(position) {
            let lat = position.coords.latitude;
            let lon = position.coords.longitude;

            fetch(
                    `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current_weather=true&daily=temperature_2m_max,precipitation_probability_mean&timezone=auto`
                )
                .then(res => res.json())
                .then(data => {
                    // === tampilkan suhu sekarang di header ===
                    document.getElementById("current-temp").innerHTML =
                        `${data.current_weather.temperature} <sup class="f-s-10">°C</sup>`;

                    // === tampilkan forecast harian ===
                    let forecast = data.daily;
                    let container = document.getElementById("forecast-box");
                    container.innerHTML = ""; // reset isi dulu

                    forecast.time.forEach((date, i) => {
                        let day = new Date(date).toLocaleDateString('en-US', {
                            weekday: 'short'
                        });
                        let temp = forecast.temperature_2m_max[i];
                        let rain = forecast.precipitation_probability_mean[i];

                        container.innerHTML += `
                    <div class="cloud-box bg-primary-${900 - (i*100)}">
                        <p class="mb-3">${day}</p>
                        <h6 class="mt-4 f-s-13">+${temp}°C</h6>
                        <span>
                            <i class="ph-duotone ph-sun-dim text-white f-s-25"></i>
                        </span>
                        <p class="f-s-13 mt-3"><i class="wi wi-raindrop"></i> ${rain}%</p>
                    </div>
                `;
                    });
                });
        }, function(error) {
            console.error("Geolocation error:", error);
        });
    </script> --}}
</body>

</html>
