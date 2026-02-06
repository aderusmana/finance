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
                        </li> --}}

                    {{-- <li class="header-dark">
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
                                
                                <div class="d-flex align-items-center gap-2">
                                    <button class="btn btn-sm text-danger p-0" id="delete-all-btn" title="Hapus Semua" style="display: none; margin-right: 10px;">
                                        <i class="iconoir-trash fs-5"></i>
                                    </button>

                                    <button aria-label="Close" class="btn-close" data-bs-dismiss="offcanvas" type="button"></button>
                                </div>
                            </div>

                            <div class="offcanvas-body notification-offcanvas-body app-scroll p-0">
                                <div id="notification-list-container" class="head-container notification-head-container">
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
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit"
                                                class="dropdown-item f-w-500 bg-transparent border-0 p-0">
                                                <i class="ph-duotone ph-sign-out pe-1 f-s-20"></i>
                                                {{ __('Log Out') }}
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            const listContainer = $('#notification-list-container');
            const loadingEl = $('#notification-loading');
            const emptyEl = $('#notification-empty');
            const badgeEl = $('#notification-badge');
            const countEl = $('#notification-count');
            const markAllReadBtn = $('#mark-all-read-btn');
            const deleteAllBtn = $('#delete-all-btn');

            // 1. CHECK COUNT
            function checkNotificationCount() {
                $.getJSON("{{ route('notifications.count') }}", function(response) {
                    const count = response.count;
                    countEl.text(count);
                    if (count > 0) {
                        badgeEl.text(count > 9 ? '9+' : count).show();
                        markAllReadBtn.show();
                        deleteAllBtn.show();
                    } else {
                        badgeEl.hide();
                        markAllReadBtn.hide();
                        deleteAllBtn.hide();
                    }
                });
            }

            // 2. FETCH LIST
            function fetchNotificationList() {
                loadingEl.show();
                listContainer.hide().empty();
                emptyEl.hide();

                $.getJSON("{{ route('notifications.fetch') }}", function(response) {
                    const notifications = response.notifications || response.data;

                    if (notifications && notifications.length > 0) {
                        deleteAllBtn.show();
                        notifications.forEach(function(notif) {
                            let data = notif.data || {};
                            let title = data.title || 'Notification'; 
                            let message = data.message || '-';
                            let icon = data.icon || 'iconoir-bell';
                            let color = data.color || 'info';
                            let url = data.url || '#';
                            let time = notif.created_at || 'Just now';
                            let isRead = notif.read_at ? 'opacity-75' : 'fw-bold';

                            let bgClass = 'bg-primary-subtle text-primary';
                            if(color === 'danger') bgClass = 'bg-danger-subtle text-danger';
                            if(color === 'warning') bgClass = 'bg-warning-subtle text-warning';
                            if(color === 'success') bgClass = 'bg-success-subtle text-success';

                            const notifHtml = `
                            <div class="notification-message d-flex align-items-start p-2 mb-2 rounded border-bottom position-relative ${isRead}" 
                                style="transition: background 0.2s;">
                                
                                <div class="d-flex align-items-start flex-grow-1 mark-as-read" 
                                        data-id="${notif.id}" 
                                        data-url="${url}"
                                        style="cursor: pointer;">
                                        
                                    <div class="flex-shrink-0 me-3">
                                        <span class="${bgClass} d-flex align-items-center justify-content-center rounded" style="width: 40px; height: 40px;">
                                            <i class="${icon} fs-5"></i>
                                        </span>
                                    </div>

                                    <div class="flex-grow-1 overflow-hidden pe-2">
                                        <h6 class="mb-1 f-s-14 text-dark ${isRead === 'fw-bold' ? 'fw-bolder' : ''}">${title}</h6>
                                        <p class="mb-1 f-s-13 text-secondary text-truncate-2" style="line-height: 1.4;">${message}</p>
                                        <small class="text-muted f-s-11"><i class="ph-clock me-1"></i>${time}</small>
                                    </div>
                                </div>

                                <button class="btn btn-link text-danger p-1 ms-1 delete-single-notif" 
                                        data-id="${notif.id}" 
                                        title="Hapus Pesan"
                                        style="z-index: 5;">
                                    <i class="iconoir-cancel fs-5"></i>
                                </button>
                            </div>`;
                            
                            listContainer.append(notifHtml);
                        });
                        listContainer.show();
                    } else {
                        emptyEl.show();
                        deleteAllBtn.hide();
                    }
                }).fail(function() {
                    emptyEl.show().find('h6').text('Gagal memuat notifikasi.');
                }).always(function() {
                    loadingEl.hide();
                });
            }

            // EVENT LISTENER
            checkNotificationCount();
            
            $('#notification-bell').on('click', function() {
                checkNotificationCount();
                fetchNotificationList();
            });

            // --- AKSI READ ---
            $(document).on('click', '.mark-as-read', function() {
                const notifEl = $(this);
                const id = notifEl.data('id');
                const url = notifEl.data('url');

                notifEl.closest('.notification-message').addClass('opacity-50'); 

                $.post("{{ route('notifications.read') }}", { id: id, _token: "{{ csrf_token() }}" })
                .done(function(res) {
                    if(res.success) {
                        if (url && url !== '#' && url !== 'null') {
                            window.location.href = url;
                        } else {
                            checkNotificationCount();
                            fetchNotificationList();
                        }
                    }
                });
            });

            // --- [UPDATE] HAPUS SATU PESAN (SWEETALERT CENTER) ---
            $(document).on('click', '.delete-single-notif', function(e) {
                e.stopPropagation(); // Stop agar tidak klik 'read'
                
                const btn = $(this);
                const id = btn.data('id');
                const item = btn.closest('.notification-message');

                Swal.fire({
                    title: 'Hapus notifikasi ini?',
                    text: "Pesan yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "/notifications/" + id,
                            type: 'DELETE',
                            data: { _token: "{{ csrf_token() }}" },
                            success: function(res) {
                                // Hapus elemen dari list dengan animasi
                                item.slideUp(200, function() {
                                    $(this).remove();
                                    checkNotificationCount();
                                    if(listContainer.children().length === 0) {
                                        emptyEl.show();
                                        deleteAllBtn.hide();
                                    }
                                });
                                
                                // Optional: Tampilkan notifikasi sukses kecil di pojok
                                const Toast = Swal.mixin({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 2000
                                });
                                Toast.fire({ icon: 'success', title: 'Notifikasi dihapus' });
                            },
                            error: function() {
                                Swal.fire('Error!', 'Gagal menghapus notifikasi.', 'error');
                            }
                        });
                    }
                });
            });

            // --- [UPDATE] HAPUS SEMUA PESAN (SWEETALERT CENTER) ---
            deleteAllBtn.on('click', function() {
                Swal.fire({
                    title: 'Bersihkan semua notifikasi?',
                    text: "Semua riwayat notifikasi Anda akan dihapus permanen.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Bersihkan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Tampilkan loading di tombol
                        const originalIcon = deleteAllBtn.html();
                        deleteAllBtn.html('<i class="spinner-border spinner-border-sm"></i>').prop('disabled', true);

                        $.ajax({
                            url: "{{ route('notifications.delete.all') }}",
                            type: 'DELETE',
                            data: { _token: "{{ csrf_token() }}" },
                            success: function(res) {
                                checkNotificationCount();
                                fetchNotificationList();
                                
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Semua notifikasi telah dihapus.',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            },
                            error: function() {
                                Swal.fire('Error!', 'Terjadi kesalahan sistem.', 'error');
                            },
                            complete: function() {
                                deleteAllBtn.html(originalIcon).prop('disabled', false);
                            }
                        });
                    }
                });
            });

            // --- MARK ALL READ ---
            markAllReadBtn.on('click', function() {
                let btn = $(this);
                btn.prop('disabled', true).text('Processing...');
                $.post("{{ route('notifications.read.all') }}", { _token: "{{ csrf_token() }}" }, function(res) {
                    checkNotificationCount();
                    fetchNotificationList();
                }).always(function(){
                    btn.prop('disabled', false).text('Mark all as read');
                });
            });
        });
    </script>

    <style>
        .text-truncate-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: normal; 
        }
        .notification-message:hover {
            background-color: #f8f9fa;
        }
        .delete-single-notif {
            opacity: 0.5;
            transition: opacity 0.2s;
        }
        .delete-single-notif:hover {
            opacity: 1;
            background-color: rgba(220, 53, 69, 0.1);
            border-radius: 50%;
        }
        /* Pastikan Swal muncul di atas offcanvas/modal bootstrap */
        .swal2-container {
            z-index: 99999 !important;
        }
    </style>
    @endpush
</header>