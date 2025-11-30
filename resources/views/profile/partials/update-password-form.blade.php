<section class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h2 class="text-primary-dark f-w-600">{{ __('Update Password') }}</h2>
                    <p class="text-secondary">
                        {{ __('Ensure your account is using a long, random password to stay secure.') }}</p>
                </div>

                <div class="card-body">
                    <form method="post" action="{{ route('password.update') }}" class="app-form rounded-control">
                        @csrf
                        @method('put')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <x-input-label class="form-label" for="update_password_current_password"
                                        :value="__('Current Password')" />
                                    <div class="input-group">
                                        <x-text-input id="update_password_current_password" name="current_password"
                                            type="password" class="form-control" autocomplete="current-password"
                                            placeholder="Enter Current Password" />
                                        <span class="input-group-text" style="cursor: pointer;"
                                            onclick="togglePassword('current')">
                                            <i id="current-eye" class="fa fa-eye-slash"></i>
                                        </span>
                                    </div>
                                    <x-input-error class="invalid-feedback" :messages="$errors->updatePassword->get('current_password')" />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <x-input-label class="form-label" for="update_password_password"
                                        :value="__('New Password')" />
                                    <div class="input-group">
                                        <x-text-input id="update_password_password" name="password" type="password"
                                            class="form-control" autocomplete="new-password"
                                            placeholder="Enter New Password" />
                                        <span class="input-group-text" style="cursor: pointer;"
                                            onclick="togglePassword('new')">
                                            <i id="new-eye" class="fa fa-eye-slash"></i>
                                        </span>
                                    </div>
                                    <x-input-error class="invalid-feedback" :messages="$errors->updatePassword->get('password')" />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <x-input-label class="form-label" for="update_password_password_confirmation"
                                        :value="__('Confirm Password')" />
                                    <div class="input-group">
                                        <x-text-input id="update_password_password_confirmation"
                                            name="password_confirmation" type="password" class="form-control"
                                            autocomplete="new-password" placeholder="Confirm New Password" />
                                        <span class="input-group-text" style="cursor: pointer;"
                                            onclick="togglePassword('confirm')">
                                            <i id="confirm-eye" class="fa fa-eye-slash"></i>
                                        </span>
                                    </div>
                                    <x-input-error class="invalid-feedback" :messages="$errors->updatePassword->get('password_confirmation')" />
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="mt-3">
                                    <button type="submit"
                                        class="btn btn-light-primary">{{ __('Update Password') }}</button>

                                    @if (session('status') === 'password-updated')
                                        <div class="alert alert-success alert-dismissible fade show mt-3">
                                            {{ __('Password updated successfully.') }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    function togglePassword(field) {
        const passwordInput = document.getElementById('update_password_' + (field === 'new' ? 'password' : field ===
            'confirm' ? 'password_confirmation' : 'current_password'));
        const eyeIcon = document.getElementById(field + '-eye');

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
