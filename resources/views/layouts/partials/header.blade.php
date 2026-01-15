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
                            {{-- [FIX 1] Ubah f-s-20 jadi f-s-26 agar besar sama seperti avatar/icon lain --}}
                            <i class="iconoir-bell f-s-26"></i>

                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notif-count" style="display: none; font-size: 9px;">
                                0
                            </span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-end p-0 border-0 shadow-lg" style="width: 320px; max-height: 400px; overflow-y: auto;">
                            <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-white sticky-top">
                                <h6 class="mb-0 fw-bold">Notifications</h6>
                                <a href="#" id="mark-all-read" class="text-decoration-none small text-primary" style="font-size: 11px;">Mark all read</a>
                            </div>

                            <div class="list-group list-group-flush" id="notification-list">
                                <div class="text-center p-3 text-muted">Loading...</div>
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

        // Init & Interval
        checkNotificationCount();
        setInterval(checkNotificationCount, 3000);

        // --- 2. FETCH LIST NOTIFICATION ---
        $('#notifDropdownToggle').on('show.bs.dropdown', function () {
            // Hanya fetch jika list masih kosong atau ada indikator loading
            // (Opsional: bisa dihapus if-nya kalau mau selalu refresh saat dibuka)
            fetchNotificationList();
        });

        function fetchNotificationList() {
            $.get("{{ route('notifications.fetch') }}", function(res) {
                let html = '';

                if (res.data && res.data.length > 0) {
                    res.data.forEach(n => {
                        // Logic Tampilan (Read vs Unread)
                        let bgClass = n.read_at ? 'bg-light' : 'bg-white';
                        let borderClass = n.read_at ? '' : 'border-start border-3 border-primary';
                        let textTitle = n.read_at ? 'text-muted' : 'fw-bold text-dark';
                        let textBody = n.read_at ? 'text-muted opacity-75' : 'text-dark';
                        let iconColor = n.data.color || 'primary';

                        // Kita pasang ID unik di elemen list agar mudah dihapus nanti
                        html += `
                            <div class="list-group-item list-group-item-action ${bgClass} ${borderClass} p-0 notif-item" id="notif-item-${n.id}">
                                <div class="d-flex w-100 justify-content-between align-items-center pe-2">

                                    <a href="${n.data.url}" class="d-flex align-items-start p-3 w-100 text-decoration-none action-read"
                                       data-id="${n.id}">

                                        <div class="flex-shrink-0 me-3">
                                            <span class="avatar-title bg-${iconColor}-subtle text-${iconColor} rounded-circle p-2 d-inline-block">
                                                <i class="${n.data.icon} fs-5"></i>
                                            </span>
                                        </div>

                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 ${textTitle}" style="font-size: 13px;">${n.data.title}</h6>
                                            <div class="mb-1 small ${textBody}" style="word-break: break-word; white-space: normal; line-height: 1.4;">
                                                ${n.data.message}
                                            </div>
                                            <small class="text-muted" style="font-size: 10px;">${n.created_at}</small>
                                        </div>
                                    </a>

                                    <button type="button" class="btn btn-sm btn-link text-muted action-delete p-2"
                                            data-id="${n.id}"
                                            title="Hapus Pesan"
                                            style="z-index: 10;">
                                        <i class="iconoir-trash f-s-16"></i>
                                    </button>
                                </div>
                            </div>
                        `;
                    });
                } else {
                    html = '<div class="text-center p-4 text-muted"><i class="iconoir-bell-off fs-3 mb-2"></i><p class="small mb-0">Tidak ada notifikasi</p></div>';
                }
                $('#notification-list').html(html);
            });
        }

        $(document).on('click', '.action-read', function(e) {
            e.preventDefault();

            let url = $(this).attr('href');
            let id = $(this).data('id');

            if(url && url !== '#' && url !== 'javascript:void(0);') {
                window.location.href = url;
            }

            $.post("{{ route('notifications.read') }}", {
                id: id,
                _token: "{{ csrf_token() }}"
            });
        });

        // --- 4. HANDLER KLIK: HAPUS NOTIFIKASI (Dropdown Tetap Buka) ---
        $(document).on('click', '.action-delete', function(e) {
            // [KUNCI 1] Mencegah browser melakukan aksi default (scroll ke atas dll)
            e.preventDefault();

            // [KUNCI 2] Mencegah Event Bubbling (PENTING AGAR DROPDOWN TIDAK MENUTUP)
            e.stopPropagation();

            let btn = $(this);
            let id = btn.data('id');
            let item = $('#notif-item-' + id); // Pastikan ID di HTML list sudah benar (lihat di bawah)

            // Ubah icon jadi loading
            btn.html('<i class="fas fa-spinner fa-spin small"></i>');

            // Gunakan Route Helper untuk URL yang akurat
            let deleteUrl = "{{ route('notifications.destroy', ':id') }}";
            deleteUrl = deleteUrl.replace(':id', id);

            $.ajax({
                url: deleteUrl,
                type: 'POST',
                data: {
                    _method: 'DELETE', // Method Spoofing
                    _token: "{{ csrf_token() }}"
                },
                success: function(result) {
                    if(result.success) {
                        // [KUNCI 3] Animasi Slide Up (Item hilang pelan-pelan)
                        // Dropdown tetap terbuka karena kita tidak me-refresh seluruh list
                        item.animate({ opacity: 0, height: 0 }, 300, function() {
                            $(this).remove(); // Hapus elemen dari DOM setelah animasi selesai

                            // Cek apakah list jadi kosong? Jika ya, tampilkan pesan kosong
                            if($('#notification-list').children().length === 0) {
                                $('#notification-list').html('<div class="text-center p-4 text-muted"><p class="small mb-0">Tidak ada notifikasi</p></div>');
                            }
                        });

                        // Update badge angka di background tanpa menutup dropdown
                        checkNotificationCount();
                    }
                },
                error: function(err) {
                    console.error('Gagal hapus:', err);
                    btn.html('<i class="iconoir-trash f-s-16"></i>'); // Balikin icon trash
                    // Optional: Toast error kecil
                }
            });
        });

        // --- 5. MARK ALL READ ---
        $('#mark-all-read').click(function(e) {
            e.preventDefault();
            e.stopPropagation();

            // Ubah icon jadi loading sementara
            let originalText = $(this).text();
            $(this).text('Processing...');

            $.post("{{ route('notifications.read.all') }}", {
                _token: "{{ csrf_token() }}"
            })
            .done(function(res) {
                // Sukses: Refresh Badge & List
                checkNotificationCount();
                fetchNotificationList();
            })
            .fail(function(xhr) {
                // Error: Munculkan pesan error biar tau kenapa gagal
                console.error(xhr);
                alert('Gagal menandai semua dibaca. Cek Console Log.');
            })
            .always(function() {
                // Kembalikan teks tombol
                $('#mark-all-read').text(originalText);
            });
        });
    });
</script>
    @endpush
</header>
