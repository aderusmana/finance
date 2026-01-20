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

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle position-relative" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" id="notifDropdownToggle">
                            <i class="iconoir-bell f-s-26"></i>

                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notif-count" style="display: none; font-size: 9px;">
                                0
                            </span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-end p-0 border-0 shadow-lg custom-scroll"
                             style="width: 360px; max-height: 75vh; overflow-y: auto; overscroll-behavior: contain;">

                            <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-white sticky-top"
                                 style="z-index: 100;">
                                <h6 class="mb-0 fw-bold">Notifications</h6>
                                <a href="#" id="mark-all-read" class="text-decoration-none small text-primary"
                                   style="font-size: 11px;">Mark all read</a>
                            </div>

                            <div class="list-group list-group-flush" id="notification-list">
                                <div class="text-center p-4 text-muted small">Loading...</div>
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
                // --- 1. CONFIG & POLLING ---
                function checkNotificationCount() {
                    $.get("{{ route('notifications.count') }}", function(res) {
                        if (res.count > 0) {
                            $('#notif-count').text(res.count).show();
                        } else {
                            $('#notif-count').hide();
                        }
                    });
                }
                checkNotificationCount();
                setInterval(checkNotificationCount, 3000);

                // --- 2. FETCH & BODY LOCK (PENTING DISINI) ---

                // SAAT DIBUKA: Kunci Scroll Body & Ambil Data
                $('#notifDropdownToggle').on('show.bs.dropdown', function () {
                    // 1. KUNCI Scroll Halaman Utama
                    $('body').css('overflow', 'hidden');

                    // 2. Ambil Data
                    fetchNotificationList();
                });

                // SAAT DITUTUP: Buka Kembali Scroll Body
                $('#notifDropdownToggle').on('hidden.bs.dropdown', function () {
                    $('body').css('overflow', ''); // Reset ke default (auto)
                });

                // Fungsi Render List (Sama seperti sebelumnya)
                function fetchNotificationList() {
                    $.get("{{ route('notifications.fetch') }}", function(res) {
                        let html = '';

                        if (res.data && res.data.length > 0) {
                            res.data.forEach((n, index) => {
                                let isUnread = n.read_at === null;
                                let bgStyle = isUnread ? 'background-color: #f0f7ff;' : 'background-color: #ffffff;';
                                let borderLeftStyle = isUnread ? 'border-left: 3px solid #0d6efd;' : 'border-left: 3px solid transparent;';
                                let isLastItem = index === res.data.length - 1;
                                let borderBottomStyle = isLastItem ? '' : 'border-bottom: 1px solid #eaeaea;';
                                let titleClass = isUnread ? 'fw-bold text-primary' : 'text-muted fw-semibold';
                                let textMessageColor = isUnread ? '#212529' : '#6c757d';
                                let iconOpacity = isUnread ? '1' : '0.75';

                                let iconClass = n.data.icon || 'iconoir-bell';
                                if (iconClass.includes('ph-users') || iconClass.includes('ph-user')) iconClass = 'iconoir-user';
                                else if (iconClass.includes('ph-signature')) iconClass = 'iconoir-edit-pencil';
                                else if (iconClass.includes('ph-bell')) iconClass = 'iconoir-bell';

                                let colorMap = { 'primary': 'primary', 'success': 'success', 'warning': 'warning', 'danger': 'danger', 'info': 'info' };
                                let colorKey = colorMap[n.data.color] || 'primary';
                                let avatarClass = `bg-${colorKey}-subtle text-${colorKey}`;
                                let targetUrl = n.data.url ? n.data.url : '#';

                                html += `
                                    <div class="list-group-item list-group-item-action p-0" id="notif-item-${n.id}"
                                        style="${bgStyle} ${borderLeftStyle} ${borderBottomStyle} transition: background-color 0.2s;">
                                        <div class="d-flex w-100 position-relative">
                                            <a href="${targetUrl}" class="d-flex align-items-start p-3 w-100 text-decoration-none action-read" data-id="${n.id}">
                                                <div class="flex-shrink-0 me-3">
                                                    <span class="rounded-circle d-inline-flex align-items-center justify-content-center ${avatarClass}"
                                                        style="width: 40px; height: 40px; opacity: ${iconOpacity}; border: 1px solid rgba(0,0,0,0.05);">
                                                        <i class="${iconClass} fs-5"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-grow-1 pe-4">
                                                    <span class="${titleClass}" style="font-size: 13px; display: block; margin-bottom: 3px;">${n.data.title}</span>
                                                    <div style="font-size: 12px; line-height: 1.4; color: ${textMessageColor}; margin-bottom: 5px; word-break: break-word;">${n.data.message}</div>
                                                    <div style="font-size: 10px; color: #adb5bd; display: flex; align-items: center; gap: 4px;">
                                                        <i class="iconoir-clock" style="font-size: 10px;"></i> ${n.created_at}
                                                    </div>
                                                </div>
                                            </a>
                                            <button type="button" class="action-delete" data-id="${n.id}" title="Hapus Notifikasi"
                                                    style="position: absolute; top: 10px; right: 10px; z-index: 20; border: none; background: transparent; color: #343a40; padding: 4px; cursor: pointer; transition: all 0.2s;">
                                                <i class="iconoir-xmark" style="font-size: 16px; font-weight: 700; stroke-width: 2.5;"></i>
                                            </button>
                                        </div>
                                    </div>`;
                            });
                        } else {
                            html = '<div class="text-center p-4 text-muted"><p class="small mb-0">Tidak ada notifikasi</p></div>';
                        }
                        $('#notification-list').html(html);
                    });
                }

                // --- 3. EVENT HANDLERS LAINNYA (TETAP SAMA) ---
                $(document).on('click', '.action-read', function(e) {
                    let url = $(this).attr('href');
                    let id = $(this).data('id');
                    $.post("{{ route('notifications.read') }}", { id: id, _token: "{{ csrf_token() }}" });
                    if (!url || url === '#' || url === 'javascript:void(0);') e.preventDefault();
                });

                $(document).on('click', '.action-delete', function(e) {
                    e.preventDefault(); e.stopPropagation();
                    let btn = $(this); let id = btn.data('id'); let item = $('#notif-item-' + id);
                    btn.html('<div class="spinner-border spinner-border-sm text-secondary" role="status"></div>');
                    $.ajax({
                        url: "{{ route('notifications.destroy', ':id') }}".replace(':id', id),
                        type: 'POST',
                        data: { _method: 'DELETE', _token: "{{ csrf_token() }}" },
                        success: function(result) {
                            if(result.success) {
                                item.animate({ opacity: 0, height: 0, padding: 0 }, 300, function() { $(this).remove(); if($('#notification-list').children().length === 0) $('#notification-list').html('<div class="text-center p-4 text-muted"><p class="small mb-0">Tidak ada notifikasi</p></div>'); });
                                checkNotificationCount();
                            }
                        }
                    });
                });

                $('#mark-all-read').click(function(e) {
                    e.preventDefault(); e.stopPropagation();
                    $(this).text('Processing...');
                    $.post("{{ route('notifications.read.all') }}", { _token: "{{ csrf_token() }}" })
                    .done(function() { checkNotificationCount(); fetchNotificationList(); })
                    .always(function() { $('#mark-all-read').text('Mark all read'); });
                });
            });
        </script>
    @endpush
</header>
