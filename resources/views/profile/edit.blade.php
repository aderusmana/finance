<x-app-layout>
    @section('title')
        Profile
    @endsection
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="container space-y-6">
            <section class="container-fluid p-0">
                <div class="row">
                    <div class="col-12 mb-4">
                        <div class="card h-100 shadow-sm border-0 rounded-4">
                            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                                <h4 class="text-primary-dark f-w-600 mb-4">{{ __('Profile Details') }}</h4>
                                
                                <!-- Nav Tabs -->
                                <ul class="nav nav-pills border-bottom pb-3 gap-2" id="profile-tabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active rounded-pill px-4 fw-semibold" id="info-tab" data-bs-toggle="pill" data-bs-target="#info-content" type="button" role="tab" aria-controls="info-content" aria-selected="true">
                                            <i class="ph-bold ph-user-circle me-1"></i> Profile Information
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link rounded-pill px-4 fw-semibold" id="password-tab" data-bs-toggle="pill" data-bs-target="#password-content" type="button" role="tab" aria-controls="password-content" aria-selected="false">
                                            <i class="ph-bold ph-key me-1"></i> Update Password
                                        </button>
                                    </li>
                                    @role('super-admin')
                                    <li class="nav-item ms-auto" role="presentation">
                                        <style>
                                            #delete-tab.active {
                                                background-color: var(--bs-danger) !important;
                                                color: #fff !important;
                                            }
                                        </style>
                                        <button class="nav-link rounded-pill px-4 text-danger fw-semibold" id="delete-tab" data-bs-toggle="pill" data-bs-target="#delete-content" type="button" role="tab" aria-controls="delete-content" aria-selected="false">
                                            <i class="ph-bold ph-trash me-1"></i> Delete Account
                                        </button>
                                    </li>
                                    @endrole
                                </ul>
                            </div>
                            
                            <div class="card-body pt-4">
                                <div class="tab-content" id="profile-tabs-content">
                                    <div class="tab-pane fade show active" id="info-content" role="tabpanel" aria-labelledby="info-tab" tabindex="0">
                                        @include('profile.partials.update-profile-information-form')
                                    </div>
                                    <div class="tab-pane fade" id="password-content" role="tabpanel" aria-labelledby="password-tab" tabindex="0">
                                        @include('profile.partials.update-password-form')
                                    </div>
                                    @role('super-admin')
                                    <div class="tab-pane fade" id="delete-content" role="tabpanel" aria-labelledby="delete-tab" tabindex="0">
                                        @include('profile.partials.delete-user-form')
                                    </div>
                                    @endrole
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
