@extends('site.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <style>
        .center-page {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
            /* adjust as needed */
        }
    </style>
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Staff Update Form</h3>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Center Form -->
    <div class="row center-page">
        <div class="col-xl-6 d-flex">
            <div class="card flex-fill">

                <div class="card-body">
                    <form action="{{ route('admin.staff-update',$original->id) }}" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <label class="col-lg-3 form-label"> Name : <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="name" value="{{ old('name',$original->name) }}">
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-lg-3 form-label">email : <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="emai" value="{{ old('email',$original->email) }}">
                                @error('email')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-lg-3 form-label">Password : <span class="text-danger"></span></label>
                            <div class="col-lg-9">
                                <input type="password" class="form-control" name="password" >
                                @error('password')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-lg-3 form-label">Role : <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <select class="form-control" name="role_name">
                                    <option value="">Select Role</option>
                                    @foreach ($roles as $item)
                                        <option value="{{ $item->name }}"
                                            {{ old('role_name', $roleName) === $item->name ? 'selected' : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('role_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-lg-3 form-label">
                                Permission : <span class="text-danger">*</span>
                            </label>

                            <div class="col-lg-9">
                                <div class="row">
                                    <div class="form-check mb-3">
                                        <input type="checkbox" id="selectAll" class="form-check-input" {{ count($userPermissions) === $permissions->count() ? 'checked' : '' }}>
                                        <label for="selectAll" class="form-check-label">Select All</label>
                                    </div>
                                    @foreach ($permissions as $permission)
                                        <div class="col-lg-4 col-md-6 col-sm-12">
                                            <div class="form-check mb-2">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    name="permissions[]"
                                                    value="{{ $permission->name }}"
                                                    id="perm_{{ $permission->id }}"
                                                    {{ in_array($permission->name, $userPermissions) ? 'checked' : '' }}
                                                >
                                                <label class="form-check-label" for="perm_{{ $permission->id }}">
                                                    {{ $permission->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>
    <script>
        document.getElementById('selectAll').addEventListener('change', function () {
            document.querySelectorAll('.form-check-input[name="permissions[]"]').forEach(cb => {
                cb.checked = this.checked;
            });
        });
    </script>
@endsection
