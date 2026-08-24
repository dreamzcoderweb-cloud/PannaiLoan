@extends('site.layouts.app')

@section('title', 'Profile Settings')

@section('content')
    <style>
        .center-page {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
        }
        .profile-avatar-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: 700;
            margin: 0 auto 15px auto;
            box-shadow: 0 4px 12px rgba(7f, 56, 237, 0.2);
        }
    </style>

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title fw-bold text-dark mb-1">Admin Profile Settings</h3>
                <p class="text-muted fs-14 mb-0">View and update your personal account information and password.</p>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="ti ti-check-circle me-2 fs-18"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row center-page">
        <div class="col-xl-7 col-lg-9 col-md-11">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-user-circle fs-24 text-primary me-2"></i>
                        <h5 class="card-title mb-0 fw-bold">Update Profile Details</h5>
                    </div>
                </div>

                <div class="card-body p-4">
                    <!-- User Header Info -->
                    <div class="text-center mb-4 pb-3 border-bottom">
                        <div class="profile-avatar-circle">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
                        <span class="badge bg-primary-transparent text-primary px-3 py-1 fs-13 rounded-pill">
                            {{ $user->roles->first()?->name ?? 'User' }}
                        </span>
                        <p class="text-muted fs-13 mt-1 mb-0">{{ $user->email }}</p>
                    </div>

                    <form action="{{ route('admin.profile-update') }}" method="POST">
                        @csrf

                        <h6 class="fw-bold text-uppercase text-secondary fs-12 tracking-wider mb-3">Basic Information</h6>

                        <!-- Name -->
                        <div class="row mb-3">
                            <label class="col-lg-4 form-label fw-medium text-dark">Name <span class="text-danger">*</span></label>
                            <div class="col-lg-8">
                                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $user->name) }}" placeholder="Enter full name" required>
                                @error('name')
                                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="row mb-4">
                            <label class="col-lg-4 form-label fw-medium text-dark">Email Address <span class="text-danger">*</span></label>
                            <div class="col-lg-8">
                                <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $user->email) }}" placeholder="Enter email address" required>
                                @error('email')
                                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4 opacity-50">

                        <h6 class="fw-bold text-uppercase text-secondary fs-12 tracking-wider mb-3">Security & Password</h6>

                        <!-- Current Password -->
                        <div class="row mb-3">
                            <label class="col-lg-4 form-label fw-medium text-dark">Current Password</label>
                            <div class="col-lg-8">
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" name="current_password" placeholder="Enter current password to verify">
                                <small class="text-muted d-block mt-1 fs-12">Required only if changing password.</small>
                                @error('current_password')
                                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <!-- New Password -->
                        <div class="row mb-3">
                            <label class="col-lg-4 form-label fw-medium text-dark">New Password</label>
                            <div class="col-lg-8">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Leave blank to keep existing password">
                                @error('password')
                                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="row mb-4">
                            <label class="col-lg-4 form-label fw-medium text-dark">Confirm New Password</label>
                            <div class="col-lg-8">
                                <input type="password" class="form-control" name="password_confirmation" placeholder="Re-enter new password">
                            </div>
                        </div>

                        <div class="text-end pt-3 border-top">
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-light me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="ti ti-device-floppy me-1"></i> Save Changes
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
