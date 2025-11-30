<x-guest-layout>
    @section('title')
        Reset Your Password
    @endsection
    <!-- Reset Your Password start -->
    <div class="container">
        <div class="row sign-in-content-bg">
            <div class="col-lg-6 image-contentbox d-none d-lg-block">
                <div class="form-container">
                    <div class="signup-content mt-4 text-center">
                        <span class="d-flex justify-content-center gap-4">
                            <img alt="" class="img-fluid" src="{{ asset('assets') }}/images/logo/sinarmeadow.png" style="width: 90px; height: auto; margin-right: -5px; margin-left: -5px; display: inline-block; vertical-align: middle;">
                            <img alt="" class="img-fluid" src="{{ asset('assets') }}/images/logo/logors.png" style="width: 120px; height: auto; margin-right: -5px; margin-left: -5px; display: inline-block; vertical-align: middle;">
                            <img alt="" class="img-fluid" src="{{ asset('assets') }}/images/logo/sindy.png" style="width: 90px; height: auto; margin-right: -5px; margin-left: -5px; display: inline-block; vertical-align: middle;">
                        </span>
                    </div>
                    <div class="signup-bg-img">
                        <img alt="" class="img-fluid" src="{{ asset('assets') }}/images/login/05.png">
                    </div>
                </div>
            </div>
            <div class="col-lg-6 form-contentbox">
                <div class="form-container">
                    <!-- Session Status -->
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <form method="POST" action="{{ route('password.store') }}" class="app-form rounded-control">
                        @csrf

                        <!-- Password Reset Token -->
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-5 text-center text-lg-start">
                                    <h2 class="text-primary-dark f-w-600">Reset Your Password</h2>
                                    <p>{{ __('Please enter your email and new password below.') }}</p>
                                </div>
                            </div>
                            <!-- Email Address -->
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label" for="email">{{ __('Email') }}</label>
                                    <input class="form-control" id="email" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" placeholder="Enter your email">
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>
                            </div>
                            <!-- Password -->
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label" for="password">{{ __('Password') }}</label>
                                    <input class="form-control" id="password" type="password" name="password" required autocomplete="new-password" placeholder="Enter new password">
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </div>
                            </div>
                            <!-- Confirm Password -->
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label" for="password_confirmation">{{ __('Confirm Password') }}</label>
                                    <input class="form-control" id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm new password">
                                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <button class="btn btn-light-primary w-100" type="submit">
                                        {{ __('Reset Password') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Reset Your Password end -->
</x-guest-layout>
