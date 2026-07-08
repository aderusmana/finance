<div class="row">
    <!-- Kiri: Data User -->
    <div class="col-md-4 mb-4">
        <div class="card bg-primary text-white h-100 shadow-sm border-0 rounded-4">
            <div class="card-body text-center d-flex flex-column justify-content-center align-items-center py-5">
                <div class="mb-4 position-relative">
                    <img id="profile-avatar" src="{{ $user->avatar ? asset($user->avatar) : asset('assets/images/logo/sinarmeadow.png') }}" 
                         alt="Avatar" class="rounded-circle border border-3 border-white shadow" style="width: 150px; height: 150px; object-fit: cover; background: #fff;">
                </div>
                <h3 class="text-white mb-1 f-w-600">{{ $user->name }}</h3>
                <p class="mb-3 d-flex align-items-center justify-content-center gap-2" style="font-size: 0.95rem; opacity: 0.9;">
                    <i class="ph-bold ph-envelope text-white"></i> {{ $user->email }}
                </p>
                <div class="bg-white bg-opacity-10 rounded px-3 py-2 mt-2 w-100 text-start d-flex align-items-center gap-2">
                    <i class="ph-bold ph-identification-card text-white fs-5"></i>
                    <div>
                        <small class="d-block text-white" style="opacity: 0.8;">NIK</small>
                        <span class="text-white fw-bold">{{ $user->nik ?? '-' }}</span>
                    </div>
                </div>
                
                <div class="bg-white bg-opacity-10 rounded px-3 py-2 mt-2 w-100 text-start d-flex align-items-center gap-2">
                    <i class="ph-bold ph-buildings text-white fs-5"></i>
                    <div>
                        <small class="d-block text-white" style="opacity: 0.8;">Department</small>
                        <span class="text-white fw-bold">{{ $user->department->name ?? '-' }}</span>
                    </div>
                </div>
                
                <div class="bg-white bg-opacity-10 rounded px-3 py-2 mt-2 w-100 text-start d-flex align-items-center gap-2">
                    <i class="ph-bold ph-briefcase text-white fs-5"></i>
                    <div>
                        <small class="d-block text-white" style="opacity: 0.8;">Position</small>
                        <span class="text-white fw-bold">{{ $user->position->position_name ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kanan: Form Profile -->
    <div class="col-md-8 mb-4">
        <p class="text-secondary small mb-4">{{ __("Update your account's profile information and email address.") }}</p>

        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
            @csrf
        </form>

        <form method="post" action="{{ route('profile.update') }}" class="app-form rounded-control" enctype="multipart/form-data">
            @csrf
            @method('patch')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <x-input-label class="form-label fw-bold text-muted" for="nik" :value="__('NIK')" />
                    <x-text-input id="nik" name="nik" type="text" class="form-control bg-light" :value="old('nik', $user->nik)" required autofocus autocomplete="nik" placeholder="Enter Your NIK" readonly/>
                    <x-input-error class="invalid-feedback" :messages="$errors->get('nik')" />
                </div>

                <div class="col-md-6 mb-3">
                    <x-input-label class="form-label fw-bold text-muted" for="name" :value="__('Name')" />
                    <x-text-input id="name" name="name" type="text" class="form-control" :value="old('name', $user->name)" required autocomplete="name" placeholder="Enter Your Name"/>
                    <x-input-error class="invalid-feedback" :messages="$errors->get('name')" />
                </div>

                <div class="col-md-6 mb-3">
                    <x-input-label class="form-label fw-bold text-muted" for="email" :value="__('Email')" />
                    <x-text-input id="email" name="email" type="email" class="form-control" :value="old('email', $user->email)" required autocomplete="username" placeholder="Enter Your Email"/>
                    <x-input-error class="invalid-feedback" :messages="$errors->get('email')" />
                    
                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <div class="alert alert-warning mt-2 small p-2">
                            <p class="mb-0">
                                {{ __('Your email address is unverified.') }}
                                <button form="send-verification" class="btn btn-link p-0 m-0 align-baseline text-decoration-none">
                                    {{ __('Click here to re-send the verification email.') }}
                                </button>
                            </p>
                        </div>

                        @if (session('status') === 'verification-link-sent')
                            <div class="alert alert-success mt-2 small p-2">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </div>
                        @endif
                    @endif
                </div>

                <div class="col-md-6 mb-3">
                    <x-input-label class="form-label fw-bold text-muted" for="avatar" :value="__('Avatar')" />
                    <input id="avatar" name="avatar" type="file" class="form-control" accept="image/*" onchange="previewImage(event)"/>
                    <x-input-error class="invalid-feedback" :messages="$errors->get('avatar')" />
                    <div class="mt-2 text-center d-none" id="preview-container">
                        <img id="preview" src="" alt="Avatar Preview" class="rounded shadow-sm" style="max-width: 100px; max-height: 100px; object-fit: cover;" />
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary text-white rounded-pill px-4 py-2 shadow-sm d-flex align-items-center gap-2">
                    <i class="ph-bold ph-floppy-disk text-white"></i> {{ __('Save Changes') }}
                </button>
            </div>
            
            @if (session('status') === 'profile-updated')
                <div class="alert alert-success alert-dismissible fade show mt-3 mb-0 shadow-sm border-0 d-flex align-items-center">
                    <i class="ph-bold ph-check-circle me-2 fs-5"></i> {{ __('Profile updated successfully.') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </form>
    </div>
</div>

<script>
function previewImage(event) {
    const input = event.target;
    const preview = document.getElementById('preview');
    const container = document.getElementById('preview-container');
    const sideAvatar = document.getElementById('profile-avatar');

    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            preview.src = e.target.result;
            if(sideAvatar) sideAvatar.src = e.target.result;
            if(container) container.classList.remove('d-none');
        }

        reader.readAsDataURL(input.files[0]);
    }
}
</script>
