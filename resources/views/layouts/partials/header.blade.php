<header class="header-main">
    <div class="container-fluid">
        <div class="row">
            <div class="col-6 col-sm-4 d-flex align-items-center header-left p-0">
                <span class="header-toggle me-3">
                    <i class="iconoir-view-grid"></i>
                </span>
            </div>

            <div class="col-6 col-sm-8 d-flex align-items-center justify-content-end header-right p-0">

                <ul class="d-flex align-items-center">

                    {{-- <li class="header-cloud">
                        <a aria-controls="cloudoffcanvasTops" class="head-icon" data-bs-target="#cloudoffcanvasTops"
                            data-bs-toggle="offcanvas" href="#" role="button">
                            <i class="iconoir-dew-point text-primary f-s-26 me-1"></i>
                            <span id="current-temp" class="f-w-600">-- <sup class="f-s-10">°C</sup></span>
                        </a>

                        <div aria-labelledby="cloudoffcanvasTops" class="offcanvas offcanvas-end header-cloud-canvas"
                            id="cloudoffcanvasTops" tabindex="-1">
                            <div class="offcanvas-body p-0">
                                <div class="cloud-body">
                                    <div id="forecast-box" class="cloud-content-box">
                                        <!-- isi forecast akan di-generate JS -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li> --}}

                    {{-- <li class="header-dark">
                        <div class="sun-logo head-icon">
                            <i class="iconoir-sun-light"></i>
                        </div>
                        <div class="moon-logo head-icon">
                            <i class="iconoir-half-moon"></i>
                        </div>
                    </li> --}}

                    <li class="header-notification">
                        <a aria-controls="notificationcanvasRight" class="d-block head-icon position-relative"
                            data-bs-target="#notificationcanvasRight" data-bs-toggle="offcanvas" href="#"
                            role="button" id="notification-bell">
                            <i class="iconoir-bell"></i>
                            <span id="notification-badge"
                                class="position-absolute top-0 start-90 translate-middle badge rounded-pill bg-success border border-light"
                                style="display: none; font-size: 0.55em; padding: 0.35em 0.6em;"></span>
                        </a>
                        <div aria-labelledby="notificationcanvasRightLabel"
                            class="offcanvas offcanvas-end header-notification-canvas" id="notificationcanvasRight"
                            tabindex="-1">
                            <div class="offcanvas-header">
                                <h5 class="offcanvas-title">Notifications (<span id="notification-count">0</span>)</h5>
                                <button aria-label="Close" class="btn-close" data-bs-dismiss="offcanvas"
                                    type="button"></button>
                            </div>
                            <div class="offcanvas-body notification-offcanvas-body app-scroll p-0">
                                <div id="notification-list-container"
                                    class="head-container notification-head-container">
                                    {{-- Notifikasi akan diisi oleh JavaScript --}}
                                </div>
                                {{-- Template Loading & Empty State --}}
                                <div id="notification-loading" class="text-center py-5">
                                    <div class="spinner-border text-primary" role="status"></div>
                                </div>
                                <div id="notification-empty" class="hidden-massage py-4 px-3" style="display: none;">
                                    <img alt="" class="w-25 h25 mb-3 mt-2"
                                        src="{{ asset('assets/images/icons/bell.png') }}">
                                    <div>
                                        <h6 class="mb-0">No New Notifications</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="offcanvas-footer p-3 border-top">
                                <button class="btn btn-primary w-100" id="mark-all-read-btn">Mark all as read</button>
                            </div>
                        </div>
                    </li>

                    <li class="header-profile">
                        <a aria-controls="profilecanvasRight" class="d-block head-icon"
                            data-bs-target="#profilecanvasRight" data-bs-toggle="offcanvas" href="#"
                            role="button">
                            @if (Auth::user()->avatar)
                                <img alt="avtar" class="b-r-50 h-35 w-35 bg-dark"
                                    src="{{ asset(Auth::user()->avatar) }}">
                            @else
                                <img alt="avtar" class="b-r-50 h-35 w-35 bg-dark"
                                    src="{{ asset('assets/images/logo/sinarmeadow.png') }}">
                            @endif
                        </a>

                        <div aria-labelledby="profilecanvasRight" class="offcanvas offcanvas-end header-profile-canvas"
                            id="profilecanvasRight" tabindex="-1">
                            <div class="offcanvas-body app-scroll">
                                <ul class="">
                                    <li class="d-flex gap-3 mb-3">
                                        <div class="d-flex-center">
                                            <span class="h-45 w-45 d-flex-center b-r-10 position-relative">
                                                @if (Auth::user()->avatar)
                                                    <img alt="" class="img-fluid b-r-10"
                                                        src="{{ asset(Auth::user()->avatar) }}">
                                                @else
                                                    <img alt="" class="img-fluid b-r-10"
                                                        src="{{ asset('assets/images/logo/sinarmeadow.png') }}">
                                                @endif
                                            </span>
                                        </div>
                                        <div class="text-center mt-2">
                                            <h6 class="mb-0"> {{ Auth::user()->name }}
                                            </h6>
                                            <p class="f-s-12 mb-0 text-secondary">{{ Auth::user()->email }}</p>
                                        </div>
                                    </li>

                                    <li>
                                        <a class="f-w-500" href="{{ route('profile.edit') }}" target="_blank">
                                            <i class="iconoir-user-love pe-1 f-s-20"></i> Profile
                                            Details
                                        </a>
                                    </li>
                                    <!-- Authentication -->
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit"
                                                class="dropdown-item f-w-500 bg-transparent border-0 p-0">
                                                <i class="ph-duotone ph-sign-out pe-1 f-s-20"></i> {{ __('Log Out') }}
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // === BAGIAN YANG DIGUNAKAN ===
                const listContainer = $('#notification-list-container');
                const loadingEl = $('#notification-loading');
                const emptyEl = $('#notification-empty');
                const badgeEl = $('#notification-badge');
                const countEl = $('#notification-count');
                const markAllReadBtn = $('#mark-all-read-btn');

                // =================================================================
                // [BARU] FUNGSI UNTUK MEMERIKSA JUMLAH NOTIFIKASI SAAT PAGE LOAD
                // =================================================================
                function checkNotificationCount() {
                    $.getJSON("{{ route('notifications.count') }}", function(response) {
                        const count = response.count;
                        countEl.text(count); // Update angka di dalam dropdown

                        if (count > 0) {
                            // Tampilkan badge jika ada notifikasi
                            badgeEl.text(count > 9 ? '9+' : count).show();
                            markAllReadBtn.show();
                        } else {
                            // Sembunyikan badge jika tidak ada
                            badgeEl.hide();
                            markAllReadBtn.hide();
                        }
                    }).fail(function() {
                        console.error('Failed to check notification count.');
                    });
                }

                // === FUNGSI LAMA (TETAP DIPERLUKAN) ===
                // Fungsi ini sekarang HANYA untuk mengambil dan menampilkan list detail
                function fetchNotificationList() {
                    loadingEl.show();
                    listContainer.hide().empty();
                    emptyEl.hide();

                    $.getJSON("{{ route('notifications.fetch') }}", function(response) {
                        const notifications = response.notifications;

                        if (notifications.length > 0) {
                            notifications.forEach(function(notif) {
                                const notifHtml = `
                                <div class="notification-message head-box mark-as-read" data-id="${notif.id}" data-url="${notif.url}" style="cursor: pointer;">
                                    <div class="message-images">
                                        <span class="${notif.color} h-35 w-35 d-flex-center b-r-10">
                                            <i class="${notif.icon}"></i>
                                        </span>
                                    </div>
                                    <div class="message-content-box flex-grow-1 ps-2">
                                        <p class="f-s-14 mb-0">${notif.text}</p>
                                        <span class="f-s-12 text-muted">${notif.time}</span>
                                    </div>
                                </div>`;
                                listContainer.append(notifHtml);
                            });
                            listContainer.show();
                        } else {
                            emptyEl.show();
                        }
                    }).fail(function() {
                        emptyEl.show().find('h6').text('Failed to load notifications.');
                    }).always(function() {
                        loadingEl.hide();
                    });
                }

                // === PANGGILAN FUNGSI & EVENT LISTENERS ===

                // [MODIFIKASI] Panggil fungsi count saat dokumen siap (page load)
                checkNotificationCount();

                // [MODIFIKASI] Saat lonceng diklik, panggil kedua fungsi
                $('#notification-bell').on('click', function() {
                    checkNotificationCount(); // Perbarui count untuk jaga-jaga
                    fetchNotificationList(); // Ambil list detailnya
                });

                // Tandai satu notifikasi sebagai dibaca saat diklik (TIDAK ADA PERUBAHAN)
                $(document).on('click', '.mark-as-read', function() {
                    const notifEl = $(this);
                    const id = notifEl.data('id');
                    const url = notifEl.data('url');

                    $.post("{{ route('notifications.read') }}", {
                            id: id,
                            _token: "{{ csrf_token() }}"
                        })
                        .done(function(res) {
                            if (res.success) {
                                if (url && url !== '#') {
                                    window.location.href = url;
                                } else {
                                    checkNotificationCount(); // Muat ulang count
                                    fetchNotificationList(); // Muat ulang list
                                }
                            }
                        }).fail(function() {
                            alert('Failed to mark as read. Please try again.');
                        });
                });

                // Tandai semua sebagai dibaca (TIDAK ADA PERUBAHAN)
                markAllReadBtn.on('click', function() {
                    $.post("{{ route('notifications.read.all') }}", {
                        _token: "{{ csrf_token() }}"
                    }, function(res) {
                        if (res.success) {
                            checkNotificationCount(); // Muat ulang count
                            fetchNotificationList(); // Muat ulang list
                        }
                    });
                });
            });
        </script>
    @endpush
</header>
