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
    </script>

    <div class="container">
        <div class="row sign-in-content-bg">
            <div class="col-lg-6 image-contentbox d-none d-lg-block">
                <div class="form-container">
                    <div class="signup-content mt-4 text-center">
                        <span class="d-flex justify-content-center gap-4">
                            <img alt="" class="img-fluid" src="{{ asset('assets') }}/images/logo/sinarmeadow.png" style="width: 90px; height: auto; margin-right: -5px; margin-left: -5px; display: inline-block; vertical-align: middle;">
                            {{-- <img alt="" class="img-fluid" src="{{ asset('assets') }}/images/logo/logors.png" style="width: 120px; height: auto; margin-right: -5px; margin-left: -5px; display: inline-block; vertical-align: middle;"> --}}
                            <img alt="" class="img-fluid" src="{{ asset('assets') }}/images/logo/sindy.png" style="width: 90px; height: auto; margin-right: -5px; margin-left: -5px; display: inline-block; vertical-align: middle;">
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
                                    <h2 class="text-primary-dark f-w-600">Selamat Datang di Portal Bank Garansi & Customer</h2>
                                    <p>Masuk menggunakan akun yang terdaftar untuk mengelola Bank Garansi dan pembuatan Customer.</p>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <x-input-label class="form-label" for="nik" :value="__('NIK')" />
                                    <x-text-input id="nik" class="form-control" type="text" name="nik" :value="old('nik')" required autofocus autocomplete="username" placeholder="Masukkan NIK Anda" />
                                    <x-input-error :messages="$errors->get('nik')" class="mt-2" />
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
                                    <button type="submit" class="btn btn-light-primary w-100">
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
