<section class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h2 class="text-primary-dark f-w-600">{{ __('Profile Information') }}</h2>
                    <p class="text-secondary">{{ __("Update your account's profile information and email address.") }}</p>
                </div>

                <div class="card-body">
                    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                        @csrf
                    </form>

                    <form method="post" action="{{ route('profile.update') }}" class="app-form rounded-control" enctype="multipart/form-data">
                        @csrf
                        @method('patch')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <x-input-label class="form-label" for="nik" :value="__('NIK')" />
                                    <x-text-input id="nik" name="nik" type="text" class="form-control" :value="old('nik', $user->nik)" required autofocus autocomplete="nik" placeholder="Enter Your NIK" readonly/>
                                    <x-input-error class="invalid-feedback" :messages="$errors->get('nik')" />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <x-input-label class="form-label" for="name" :value="__('Name')" />
                                    <x-text-input id="name" name="name" type="text" class="form-control" :value="old('name', $user->name)" required autocomplete="name" placeholder="Enter Your Name"/>
                                    <x-input-error class="invalid-feedback" :messages="$errors->get('name')" />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <x-input-label class="form-label" for="avatar" :value="__('Avatar')" />
                                    <input id="avatar" name="avatar" type="file" class="form-control" accept="image/*" onchange="previewImage(event)"/>
                                    <x-input-error class="invalid-feedback" :messages="$errors->get('avatar')" />
                                    <div class="mt-2">
                                        <img id="preview" src="{{ $user->avatar ? asset($user->avatar) : asset('assets/images/logo/sinarmeadow.png') }}"
                                             alt="Avatar Preview" class="rounded" style="max-width: 100px; max-height: 100px;" />
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <x-input-label class="form-label" for="email" :value="__('Email')" />
                                    <x-text-input id="email" name="email" type="email" class="form-control" :value="old('email', $user->email)" required autocomplete="username" placeholder="Enter Your Email"/>
                                    <x-input-error class="invalid-feedback" :messages="$errors->get('email')" />
                                </div>

                                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                    <div class="alert alert-warning">
                                        <p class="mb-0">
                                            {{ __('Your email address is unverified.') }}
                                            <button form="send-verification" class="btn btn-link p-0 m-0 align-baseline">
                                                {{ __('Click here to re-send the verification email.') }}
                                            </button>
                                        </p>
                                    </div>

                                    @if (session('status') === 'verification-link-sent')
                                        <div class="alert alert-success">
                                            {{ __('A new verification link has been sent to your email address.') }}
                                        </div>
                                    @endif
                                @endif
                            </div>

                            <div class="col-12">
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-light-primary">
                                        {{ __('Save Changes') }}
                                    </button>

                                    @if (session('status') === 'profile-updated')
                                        <div class="alert alert-success alert-dismissible fade show mt-3">
                                            {{ __('Profile updated successfully.') }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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
function previewImage(event) {
    const input = event.target;
    const preview = document.getElementById('preview');

    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            preview.src = e.target.result;
        }

        reader.readAsDataURL(input.files[0]);
    }
}
</script>
