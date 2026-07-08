<div>
    <p class="text-secondary small mb-4">{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}</p>

    <button class="btn btn-danger text-white rounded-pill px-4 py-2 shadow-sm d-flex align-items-center gap-2" id="account_delete" type="button" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
        <i class="ph-bold ph-trash text-white"></i> {{ __('Delete Account') }}
    </button>

    <!-- Delete Account Modal -->
    <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-bottom-0 pt-4 px-4">
                    <h5 class="modal-title text-danger fw-bold" id="deleteAccountModalLabel">
                        {{ __('Are you sure you want to delete your account?') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')
                    <div class="modal-body px-4">
                        <p class="text-secondary small">
                            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                        </p>

                        <div class="mt-4">
                            <x-input-label for="password" value="{{ __('Password') }}" class="form-label fw-bold text-muted" />
                            <div class="input-group">
                                <x-text-input
                                    id="password"
                                    name="password"
                                    type="password"
                                    class="form-control"
                                    placeholder="{{ __('Enter your password to confirm') }}"
                                />
                                <span class="input-group-text bg-white" style="cursor: pointer;" onclick="toggleDeletePassword()">
                                    <i id="delete-eye" class="fa fa-eye-slash text-muted"></i>
                                </span>
                            </div>
                            <x-input-error :messages="$errors->userDeletion->get('password')" class="invalid-feedback" />
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pb-4 px-4">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" class="btn btn-danger text-white rounded-pill px-4 shadow-sm d-flex align-items-center gap-2">
                            <i class="ph-bold ph-trash text-white"></i> {{ __('Delete Account') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleDeletePassword() {
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('delete-eye');

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

// Show modal if there are validation errors
@if($errors->userDeletion->isNotEmpty())
    document.addEventListener('DOMContentLoaded', function() {
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteAccountModal'));
        deleteModal.show();
    });
@endif
</script>
