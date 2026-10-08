<x-guest-layout>
    @section('title')
        Login
    @endsection

    <style>
        /* Tab Navigation Styling */
        #authTabs .nav-link {
            color: #64748b;
            border-radius: 8px;
            transition: all 0.2s ease-in-out;
            font-size: 0.9rem;
        }
        #authTabs .nav-link.active {
            background-color: #2563eb;
            color: #ffffff;
            box-shadow: 0 4px 8px rgba(37, 99, 235, 0.25);
        }
        #authTabs .nav-link:hover:not(.active) {
            background-color: #f1f5f9;
            color: #1e293b;
        }

        /* Distributor Modal & Matrix Styling (Reused from internal detail page) */
        .detail-header-card {
            background: linear-gradient(135deg, #152238 0%, #1e3a5f 100%);
            border-radius: 0.75rem;
            color: #fff;
            padding: 1.25rem 1.5rem;
            box-shadow: 0 4px 12px rgba(21, 34, 56, 0.15);
        }

        .distributor-code-badge-light {
            font-family: monospace;
            font-size: 0.85rem;
            font-weight: 600;
            color: #93c5fd;
            background-color: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(147, 197, 253, 0.3);
            padding: 2px 8px;
            border-radius: 4px;
        }

        .nav-tabs-custom {
            border-bottom: 2px solid #e2e8f0;
            gap: 8px;
        }

        .nav-tabs-custom .nav-link {
            border: none;
            border-bottom: 3px solid transparent;
            color: #64748b;
            font-weight: 600;
            padding: 0.75rem 1.25rem;
            border-radius: 0;
            background: transparent;
            transition: all 0.2s ease;
        }

        .nav-tabs-custom .nav-link:hover {
            color: #1e3a8a;
            border-bottom-color: #cbd5e1;
        }

        .nav-tabs-custom .nav-link.active {
            color: #1e3a8a;
            border-bottom-color: #2563eb;
            background: transparent;
        }

        .month-card {
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            background: #ffffff;
            transition: box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .month-card:hover {
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.06);
            border-color: #cbd5e1;
        }

        .month-card-header {
            padding: 0.75rem 1.2rem;
            border-bottom: 1px solid #f1f5f9;
            background: #fafbfc;
            border-top-left-radius: 0.75rem;
            border-top-right-radius: 0.75rem;
        }

        .doc-section {
            border: 1px solid #f1f5f9;
            background: #f8fafc;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
        }

        .doc-item-row {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 0.375rem;
            padding: 0.5rem 0.75rem;
            margin-bottom: 0.5rem;
        }
        .doc-item-row:last-child {
            margin-bottom: 0;
        }

        .badge-status-complete {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .badge-status-partial {
            background-color: #fef3c7;
            color: #92400e;
            border: 1px solid #fcd34d;
        }

        .badge-status-empty {
            background-color: #f3f4f6;
            color: #6b7280;
            border: 1px solid #e5e7eb;
        }

        /* OTP Input custom style */
        .otp-input-field {
            font-family: 'Courier New', Courier, monospace;
            font-size: 2rem;
            letter-spacing: 0.5rem;
            text-align: center;
            font-weight: 700;
            height: 56px;
            border-radius: 10px;
        }
    </style>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="container">
        <div class="row sign-in-content-bg" style="background: rgba(255, 255, 255, 0.719); backdrop-filter: blur(5px); border-radius: 10px; box-shadow: 0 4px 6px rgba(255, 255, 255, 0.1);">
            <div class="col-lg-6 image-contentbox d-none d-lg-block">
                <div class="form-container">
                    <div class="signup-content mt-4 text-center">
                        <span class="d-flex justify-content-center gap-4">
                            <img alt="" class="img-fluid" src="{{ asset('assets') }}/images/logo/sinarmeadow.png"
                                style="width: 90px; height: auto; margin-right: -5px; margin-left: -5px; display: inline-block; vertical-align: middle;">
                            <img alt="" class="img-fluid" src="{{ asset('assets')}}/images/logo/set-logo.png" style="width: 130px; height: auto; margin-right: -5px; margin-left: -5px; display: inline-block; vertical-align: middle;">
                            <img alt="" class="img-fluid" src="{{ asset('assets') }}/images/logo/sindy.png"
                                style="width: 90px; height: auto; margin-right: -5px; margin-left: -5px; display: inline-block; vertical-align: middle;">
                        </span>
                    </div>
                    <div class="signup-bg-img">
                        <img alt="" class="img-fluid" src="{{ asset('assets') }}/images/login/bg.png" width="600">
                    </div>
                </div>
            </div>

            <div class="col-lg-6 form-contentbox">
                <div class="form-container">
                    <div class="mb-4 text-center">
                        <img alt="Logo" class="img-fluid mb-2" src="{{ asset('assets/images/logo/logos.png') }}" style="width: 220px; height: auto; display: block; margin: 0 auto;">
                        <h2 class="h4 fw-bold text-dark">Welcome to Customer Portal</h2>
                    </div>

                    {{-- Tab Navigation: Login Karyawan vs Portal Dokumen Distributor --}}
                    <ul class="nav nav-pills nav-justified mb-4 p-1 rounded-3 bg-white border shadow-sm" id="authTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-semibold py-2 d-flex align-items-center justify-content-center gap-2"
                                    id="employee-login-tab"
                                    data-bs-toggle="pill"
                                    data-bs-target="#employee-login-pane"
                                    type="button"
                                    role="tab"
                                    aria-controls="employee-login-pane"
                                    aria-selected="true">
                                <i class="fa-solid fa-user-tie"></i>
                                <span>Login Karyawan</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-semibold py-2 d-flex align-items-center justify-content-center gap-2"
                                    id="distributor-portal-tab"
                                    data-bs-toggle="pill"
                                    data-bs-target="#distributor-portal-pane"
                                    type="button"
                                    role="tab"
                                    aria-controls="distributor-portal-pane"
                                    aria-selected="false">
                                <i class="fa-solid fa-file-invoice-dollar"></i>
                                <span>Portal Dokumen Distributor</span>
                            </button>
                        </li>
                    </ul>

                    {{-- TAB CONTENT --}}
                    <div class="tab-content" id="authTabsContent">
                        {{-- PANE 1: LOGIN KARYAWAN (INTERNAL) --}}
                        <div class="tab-pane fade show active" id="employee-login-pane" role="tabpanel" aria-labelledby="employee-login-tab">
                            <form method="POST" action="{{ route('login') }}" class="app-form rounded-control">
                                @csrf
                                <div class="row">
                                    @if ($errors->has('nik'))
                                        @php
                                            $errorText = $errors->first('nik');
                                            $countdownSeconds = session('lockout_seconds');
                                            if (!$countdownSeconds && preg_match('/(\d+)\s*(?:seconds|second|detik)/i', $errorText, $matches)) {
                                                $countdownSeconds = (int) $matches[1];
                                            }
                                        @endphp
                                        <div class="col-12 mb-3">
                                            <div id="login-alert" class="alert alert-danger d-flex align-items-center mb-0 py-2 px-3 text-danger-emphasis bg-danger-subtle border border-danger-subtle rounded-3" role="alert" style="font-size: 0.875rem;" data-seconds="{{ $countdownSeconds ?? 0 }}">
                                                <i id="login-alert-icon" class="fa-solid fa-triangle-exclamation me-2 fs-5 flex-shrink-0 text-danger"></i>
                                                <div id="login-alert-text">
                                                    @if ($countdownSeconds && $countdownSeconds > 0)
                                                        Terlalu banyak percobaan login. Silakan coba lagi dalam <span id="countdown-timer" class="badge bg-danger fs-6 mx-1 font-monospace">{{ $countdownSeconds >= 60 ? ceil($countdownSeconds / 60) . ' menit (' . $countdownSeconds . 's)' : $countdownSeconds . ' detik' }}</span>
                                                    @else
                                                        {{ $errorText }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <x-input-label class="form-label" for="nik" :value="__('NIK')" />
                                            <x-text-input id="nik" class="form-control" type="text" name="nik" :value="old('nik')" required autofocus autocomplete="username" placeholder="Masukkan NIK Anda" />
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <x-input-label class="form-label" for="password" :value="__('Password')" />
                                            @if (Route::has('password.request'))
                                                <a class="link-primary-dark float-end" href="{{ route('password.request') }}">
                                                    {{ __('Forgot your password?') }}
                                                </a>
                                            @endif
                                            <div class="input-group">
                                                <x-text-input id="password" class="form-control" type="password" name="password" required autocomplete="current-password" placeholder="Masukkan Password Anda" />
                                                <span class="input-group-text" style="cursor: pointer;" onclick="togglePassword()">
                                                    <i id="eye-icon" class="fa fa-eye-slash"></i>
                                                </span>
                                            </div>
                                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check mb-3">
                                            <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                                            <label class="form-check-label text-secondary" for="remember_me">
                                                {{ __('Remember me') }}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <button type="submit" id="login-submit-btn" class="btn btn-light-primary w-100">
                                                {{ __('Log in') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        {{-- PANE 2: PORTAL DOKUMEN DISTRIBUTOR (EKSTERNAL / PUBLIK) --}}
                        <div class="tab-pane fade" id="distributor-portal-pane" role="tabpanel" aria-labelledby="distributor-portal-tab">
                            <div class="app-form rounded-control">
                                <div class="p-3 mb-3 bg-primary-subtle border border-primary-subtle rounded-3">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <i class="fa-solid fa-shield-halved text-primary"></i>
                                        <span class="fw-bold text-primary">Akses Mandiri Distributor</span>
                                    </div>
                                    <p class="small text-secondary mb-0">
                                        Unduh Bukti Potong (BuPot), TOP Insentif & Penjelasan Transfer dengan verifikasi OTP ke <strong>Email BuPot</strong> terdaftar.
                                    </p>
                                </div>

                                {{-- Alert Container for Portal --}}
                                <div id="portal-alert-container" class="mb-3" style="display: none;"></div>

                                {{-- Step 1: Input Code, Bupot Email & Year --}}
                                <div id="portal-step-credentials">
                                    {{-- Anti-bot honeypot --}}
                                    <input type="text" id="portal_website_hp" name="website_hp" style="display: none !important;" tabindex="-1" autocomplete="off">

                                    <div class="mb-3">
                                        <label for="portal_code" class="form-label fw-semibold">
                                            Kode Distributor <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa-solid fa-id-badge text-secondary"></i></span>
                                            <input type="text"
                                                   class="form-control text-uppercase font-monospace"
                                                   id="portal_code"
                                                   placeholder="Contoh: ID3455"
                                                   autocomplete="off"
                                                   required>
                                        </div>
                                        <small class="text-muted">Masukkan kode unik distributor yang tercatat di Finance.</small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="portal_email" class="form-label fw-semibold">
                                            Email BuPot Terdaftar <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa-solid fa-envelope text-secondary"></i></span>
                                            <input type="email"
                                                   class="form-control"
                                                   id="portal_email"
                                                   placeholder="Email penerima bukti potong"
                                                   autocomplete="email"
                                                   required>
                                        </div>
                                        <small class="text-muted">OTP akan dikirimkan ke alamat Email BuPot ini.</small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="portal_year" class="form-label fw-semibold">
                                            Tahun Dokumen <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="portal_year">
                                            @for ($optYear = now()->year + 1; $optYear >= now()->year - 5; $optYear--)
                                                <option value="{{ $optYear }}" @selected(now()->year === $optYear)>Tahun {{ $optYear }}</option>
                                            @endfor
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <button type="button" id="btn-request-otp" class="btn btn-primary w-100 py-2 fw-semibold" onclick="requestPortalOtp()">
                                            <i class="fa-solid fa-paper-plane me-1"></i> Kirim Kode OTP
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modals for Distributor Documents & PDF Preview --}}
    @include('auth.partials.portal_distributor_modal')

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Internal Login lockout countdown timer
            const alertBox = document.getElementById('login-alert');
            if (alertBox) {
                let seconds = parseInt(alertBox.getAttribute('data-seconds'), 10);
                if (!isNaN(seconds) && seconds > 0) {
                    const timerSpan = document.getElementById('countdown-timer');
                    const alertText = document.getElementById('login-alert-text');
                    const alertIcon = document.getElementById('login-alert-icon');
                    const submitBtn = document.getElementById('login-submit-btn');

                    function formatTime(sec) {
                        if (sec < 60) return sec + ' detik';
                        const m = Math.floor(sec / 60);
                        const s = sec % 60;
                        return m + 'm ' + (s < 10 ? '0' : '') + s + 's (' + sec + ' detik)';
                    }

                    function formatBtnTime(sec) {
                        if (sec < 60) return sec + 's';
                        const m = Math.floor(sec / 60);
                        const s = sec % 60;
                        return m + 'm ' + s + 's';
                    }

                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.classList.add('disabled');
                        const originalBtnText = submitBtn.innerHTML;
                        submitBtn.innerHTML = `<i class="fa fa-spinner fa-spin me-1"></i> Tunggu (${formatBtnTime(seconds)})`;

                        const interval = setInterval(function () {
                            seconds--;
                            if (seconds > 0) {
                                if (timerSpan) timerSpan.textContent = formatTime(seconds);
                                submitBtn.innerHTML = `<i class="fa fa-spinner fa-spin me-1"></i> Tunggu (${formatBtnTime(seconds)})`;
                            } else {
                                clearInterval(interval);
                                alertBox.className = 'alert alert-success d-flex align-items-center mb-0 py-2 px-3 text-success-emphasis bg-success-subtle border border-success-subtle rounded-3';
                                if (alertIcon) {
                                    alertIcon.className = 'fa-solid fa-circle-check me-2 fs-5 flex-shrink-0 text-success';
                                }
                                if (alertText) {
                                    alertText.innerHTML = '<strong>Waktu tunggu telah selesai!</strong> Silakan coba login kembali sekarang.';
                                }
                                submitBtn.disabled = false;
                                submitBtn.classList.remove('disabled');
                                submitBtn.innerHTML = originalBtnText;
                            }
                        }, 1000);
                    }
                }
            }

            // Enter key on credentials inputs
            ['portal_code', 'portal_email'].forEach(function (id) {
                const el = document.getElementById(id);
                if (el) {
                    el.addEventListener('keyup', function (e) {
                        if (e.key === 'Enter') {
                            requestPortalOtp();
                        }
                    });
                }
            });

            // Enter key on OTP modal input
            const modalOtpInput = document.getElementById('portal_modal_otp');
            if (modalOtpInput) {
                modalOtpInput.addEventListener('keyup', function (e) {
                    if (e.key === 'Enter') {
                        confirmPortalOtp();
                    }
                });
            }
        });

        // ==========================================
        // DISTRIBUTOR PORTAL LOGIC
        // ==========================================
        let resendInterval = null;
        let activeDistributorId = null;
        let otpModalInstance = null;
        let docsModalInstance = null;
        let pdfModalInstance = null;

        function showPortalAlert(message, type = 'danger') {
            const container = document.getElementById('portal-alert-container');
            if (!container) return;
            const iconClass = type === 'success' ? 'fa-circle-check text-success' : 'fa-triangle-exclamation text-danger';
            const bgClass = type === 'success' ? 'alert-success text-success-emphasis bg-success-subtle border-success-subtle' : 'alert-danger text-danger-emphasis bg-danger-subtle border-danger-subtle';

            container.innerHTML = `
                <div class="alert ${bgClass} d-flex align-items-center py-2 px-3 rounded-3 border" role="alert" style="font-size: 0.85rem;">
                    <i class="fa-solid ${iconClass} me-2 fs-5 flex-shrink-0"></i>
                    <div>${message}</div>
                </div>
            `;
            container.style.display = 'block';
        }

        function clearPortalAlert() {
            const container = document.getElementById('portal-alert-container');
            if (container) {
                container.innerHTML = '';
                container.style.display = 'none';
            }
        }

        function showPortalModalOtpAlert(message, type = 'danger') {
            const container = document.getElementById('portal-otp-modal-alert');
            if (!container) return;
            const iconClass = type === 'success' ? 'fa-circle-check text-success' : 'fa-triangle-exclamation text-danger';
            const bgClass = type === 'success' ? 'alert-success text-success-emphasis bg-success-subtle border-success-subtle' : 'alert-danger text-danger-emphasis bg-danger-subtle border-danger-subtle';

            container.innerHTML = `
                <div class="alert ${bgClass} d-flex align-items-center py-2 px-3 rounded-3 border" role="alert" style="font-size: 0.85rem;">
                    <i class="fa-solid ${iconClass} me-2 fs-5 flex-shrink-0"></i>
                    <div>${message}</div>
                </div>
            `;
            container.style.display = 'block';
        }

        function clearPortalModalOtpAlert() {
            const container = document.getElementById('portal-otp-modal-alert');
            if (container) {
                container.innerHTML = '';
                container.style.display = 'none';
            }
        }

        function requestPortalOtp(isResend = false) {
            if (!isResend) {
                clearPortalAlert();
            } else {
                clearPortalModalOtpAlert();
            }

            const code = document.getElementById('portal_code').value.trim();
            const email = document.getElementById('portal_email').value.trim();
            const honeypot = document.getElementById('portal_website_hp').value.trim();
            const btn = isResend ? document.getElementById('btn-modal-resend-otp') : document.getElementById('btn-request-otp');

            if (!code) {
                showPortalAlert('Silakan masukkan Kode Distributor terlebih dahulu.');
                return;
            }
            if (!email) {
                showPortalAlert('Silakan masukkan Email BuPot yang terdaftar.');
                return;
            }

            const originalBtnHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = `<i class="fa fa-spinner fa-spin me-1"></i> ${isResend ? 'Mengirim ulang...' : 'Mengirim OTP...'}`;

            fetch('{{ route('portal.distributor.request.otp') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    code: code,
                    email: email,
                    website_hp: honeypot
                })
            })
            .then(res => res.json().then(data => ({ status: res.status, data })))
            .then(({ status, data }) => {
                btn.disabled = false;
                btn.innerHTML = originalBtnHtml;

                if (status === 200 && data.success) {
                    // Update masked email in OTP modal
                    const maskedEl = document.getElementById('portalOtpModalMaskedEmail');
                    if (maskedEl && data.masked_email) {
                        maskedEl.textContent = data.masked_email;
                    }

                    // Reset OTP input
                    const otpInput = document.getElementById('portal_modal_otp');
                    if (otpInput) {
                        otpInput.value = '';
                    }

                    // Open OTP Modal if not already shown
                    const otpModalEl = document.getElementById('portalOtpModal');
                    if (!otpModalInstance) {
                        otpModalInstance = new bootstrap.Modal(otpModalEl);
                    }
                    otpModalInstance.show();

                    if (isResend) {
                        showPortalModalOtpAlert(data.message, 'success');
                    } else {
                        clearPortalAlert();
                    }

                    // Start cooldown timer
                    startModalResendTimer(data.cooldown || 60);

                    // Focus OTP input
                    setTimeout(() => {
                        if (otpInput) otpInput.focus();
                    }, 400);
                } else {
                    if (isResend) {
                        showPortalModalOtpAlert(data.message || 'Gagal mengirim ulang kode OTP.');
                    } else {
                        showPortalAlert(data.message || 'Gagal mengirim kode OTP. Silakan periksa kembali data Anda.');
                    }
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = originalBtnHtml;
                const msg = 'Terjadi gangguan koneksi. Silakan coba lagi.';
                if (isResend) {
                    showPortalModalOtpAlert(msg);
                } else {
                    showPortalAlert(msg);
                }
            });
        }

        function resendPortalOtp() {
            requestPortalOtp(true);
        }

        function startModalResendTimer(seconds) {
            const resendBtn = document.getElementById('btn-modal-resend-otp');
            const timerSpan = document.getElementById('modal-resend-timer');
            if (!resendBtn || !timerSpan) return;

            if (resendInterval) clearInterval(resendInterval);

            resendBtn.disabled = true;
            resendBtn.classList.add('text-muted');
            resendBtn.classList.remove('text-primary');
            timerSpan.textContent = seconds;

            resendInterval = setInterval(function () {
                seconds--;
                if (seconds > 0) {
                    timerSpan.textContent = seconds;
                } else {
                    clearInterval(resendInterval);
                    resendBtn.disabled = false;
                    resendBtn.classList.remove('text-muted');
                    resendBtn.classList.add('text-primary');
                    resendBtn.innerHTML = `<i class="fa-solid fa-rotate-right me-1"></i> Kirim Ulang OTP`;
                }
            }, 1000);
        }

        function confirmPortalOtp() {
            clearPortalModalOtpAlert();

            const code = document.getElementById('portal_code').value.trim();
            const email = document.getElementById('portal_email').value.trim();
            const otpInput = document.getElementById('portal_modal_otp');
            const otp = otpInput ? otpInput.value.trim() : '';
            const year = document.getElementById('portal_year').value;
            const honeypot = document.getElementById('portal_website_hp').value.trim();
            const btn = document.getElementById('btn-modal-confirm-otp');

            if (!otp || otp.length !== 6) {
                showPortalModalOtpAlert('Silakan masukkan 6 digit kode OTP yang telah dikirim ke email Anda.');
                if (otpInput) otpInput.focus();
                return;
            }

            const originalBtnHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = `<i class="fa fa-spinner fa-spin me-1"></i> Memverifikasi...`;

            fetch('{{ route('portal.distributor.verify.otp') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    code: code,
                    email: email,
                    otp: otp,
                    year: year,
                    website_hp: honeypot
                })
            })
            .then(res => res.json().then(data => ({ status: res.status, data })))
            .then(({ status, data }) => {
                btn.disabled = false;
                btn.innerHTML = originalBtnHtml;

                if (status === 200 && data.success) {
                    clearPortalModalOtpAlert();
                    activeDistributorId = data.distributor.id;

                    // Set header info in distributor docs modal
                    const labelEl = document.getElementById('distributorDocsModalLabel');
                    if (labelEl) {
                        labelEl.innerHTML = `
                            ${data.distributor.name}
                            <span class="badge bg-primary ms-2 font-monospace">${data.distributor.code}</span>
                        `;
                    }

                    // Render content into modal
                    const containerEl = document.getElementById('distributorDocsContainer');
                    if (containerEl) {
                        containerEl.innerHTML = data.html;
                    }

                    // Close OTP Modal
                    if (!otpModalInstance) {
                        otpModalInstance = bootstrap.Modal.getInstance(document.getElementById('portalOtpModal')) || new bootstrap.Modal(document.getElementById('portalOtpModal'));
                    }
                    otpModalInstance.hide();

                    // Open Distributor Documents Modal
                    const docsModalEl = document.getElementById('distributorDocsModal');
                    if (!docsModalInstance) {
                        docsModalInstance = new bootstrap.Modal(docsModalEl);
                    }
                    docsModalInstance.show();
                } else {
                    showPortalModalOtpAlert(data.message || 'Verifikasi OTP gagal. Silakan coba lagi.');
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = originalBtnHtml;
                showPortalModalOtpAlert('Terjadi gangguan jaringan saat memverifikasi OTP.');
            });
        }

        // ==========================================
        // MODAL INTERACTION (YEAR, TAB, PDF PREVIEW)
        // ==========================================
        function changePortalYear(distributorId, newYear, tab = 'monthly') {
            loadPortalDocuments(distributorId, newYear, tab);
        }

        function switchPortalTab(distributorId, year, newTab) {
            loadPortalDocuments(distributorId, year, newTab);
        }

        function changePortalTransferYear(distributorId, year, transferYear) {
            loadPortalDocuments(distributorId, year, 'transfer', transferYear);
        }

        function loadPortalDocuments(distributorId, year, tab = 'monthly', transferYear = 'all') {
            const container = document.getElementById('distributorDocsContainer');
            if (container) {
                container.style.opacity = '0.5';
            }

            fetch(`{{ route('portal.distributor.documents') }}?distributor_id=${distributorId}&year=${year}&tab=${tab}&transfer_year=${transferYear}`, {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (container) container.style.opacity = '1';
                if (data.success) {
                    if (container) container.innerHTML = data.html;
                } else {
                    alert(data.message || 'Gagal memuat dokumen.');
                }
            })
            .catch(() => {
                if (container) container.style.opacity = '1';
                alert('Gagal memuat dokumen distributor.');
            });
        }

        function openPublicPdfPreview(previewUrl, title, downloadUrl) {
            document.getElementById('previewPdfModalLabel').textContent = title || 'Pratinjau Dokumen PDF';
            document.getElementById('previewPdfModalSubtitle').textContent = 'Memuat berkas PDF...';
            document.getElementById('previewPdfDownloadBtn').href = downloadUrl;
            document.getElementById('previewPdfNewTabBtn').href = previewUrl;

            const iframe = document.getElementById('previewPdfIframe');
            iframe.src = previewUrl;
            iframe.onload = function () {
                document.getElementById('previewPdfModalSubtitle').textContent = 'Selesai dimuat.';
            };

            const pdfEl = document.getElementById('previewPdfModal');
            if (!pdfModalInstance) {
                pdfModalInstance = new bootstrap.Modal(pdfEl);
            }
            pdfModalInstance.show();
        }

        function closePortalModalAndLogout() {
            fetch('{{ route('portal.distributor.logout') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            if (docsModalInstance) {
                docsModalInstance.hide();
            }

            // Reset portal inputs
            const modalOtpInput = document.getElementById('portal_modal_otp');
            if (modalOtpInput) modalOtpInput.value = '';
            clearPortalAlert();
            clearPortalModalOtpAlert();
        }
    </script>
</x-guest-layout>
