<!-- Header -->

<div class="header">
    <div class="main-header">

        <div class="header-left">
            <a href="{{ asset('/') }}" class="logo">
                <img src="{{ asset('assets/img/logo.svg') }}" alt="Logo">
            </a>
            <a href="{{ asset('/') }}" class="dark-logo">
                <img src="{{ asset('public/assets/img/logo-white.svg') }}" alt="Logo">
            </a>
        </div>

        <a id="mobile_btn" class="mobile_btn" href="#sidebar">
            <span class="bar-icon">
                <span></span>
                <span></span>
                <span></span>
            </span>
        </a>

        <div class="header-user">
            <div class="nav user-menu nav-list">

                <div class="me-auto d-flex align-items-center" id="header-search">
                    <a id="toggle_btn" href="javascript:void(0);" class="btn btn-menubar me-1">
                        <i class="ti ti-arrow-bar-to-left"></i>
                    </a>
                </div>

                <div class="d-flex align-items-center">
                    <div class="dropdown profile-dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle d-flex align-items-center"
                           data-bs-toggle="dropdown">
                            <span class="avatar avatar-sm online">
                                <img src="{{ asset('assets/img/profiles/avatar-12.jpg') }}" alt="Img"
                                     class="img-fluid rounded-circle border border-2 border-white shadow-sm">
                            </span>
                        </a>

                        <div class="dropdown-menu shadow-lg border-0 mt-2">
                            <div class="card mb-0 bg-white">
                                <div class="card-header bg-transparent border-0 pb-0">
                                    <div class="d-flex align-items-center">
                                        <span class="avatar avatar-lg me-3 avatar-rounded border">
                                            <img src="{{ asset('assets/img/profiles/avatar-12.jpg') }}" alt="img">
                                        </span>
                                        <div>
                                            <h6 class="mb-0 fw-bold">{{ auth()->user()->name }}</h6>
                                            <p class="fs-12 text-muted mb-0">{{ auth()->user()->email }}</p>
                                        </div>
                                    </div>
                                </div>
                                <hr class="my-2 opacity-50">
                                <div class="card-body py-2">
                                    @if(strtolower(auth()->user()->roles->first()?->name ?? '') === 'admin' || auth()->user()->hasRole(['ADMIN', 'admin']))
                                        <a class="dropdown-item d-flex align-items-center rounded-2 py-2 px-3 mb-1" href="{{ route('admin.profile') }}">
                                            <i class="ti ti-user-circle me-2 fs-18 text-primary"></i>
                                            <span class="fw-medium">My Profile</span>
                                        </a>
                                    @endif
                                    <a class="dropdown-item d-flex align-items-center rounded-2 py-2 px-3" href="{{ route('admin.logout') }}">
                                        <i class="ti ti-login me-2 fs-18 text-danger"></i>
                                        <span class="fw-medium">Logout</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
<!-- /Header -->
