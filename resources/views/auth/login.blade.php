<x-guest-layout>
    @section('title')
        Login
    @endsection
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

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
            const alertBox = document.getElementById('login-alert');
            if (!alertBox) return;

            let seconds = parseInt(alertBox.getAttribute('data-seconds'), 10);
            if (isNaN(seconds) || seconds <= 0) return;

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
                        // Update alert box to success state
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
        });
    </script>

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
                    <form method="POST" action="{{ route('login') }}" class="app-form rounded-control">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-5 text-center">
                                    <img alt="Logo" class="img-fluid mb-3" src="{{ asset('assets/images/logo/logos.png') }}" style="width: 250px; height: auto; display: block; margin: 0 auto;">
                                    <h2>Welcome to Customer Portal</h2>
                                </div>
                            </div>
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
            </div>
        </div>
</div>
</x-guest-layout>
