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
                <h3 class="page-title">Role Update Form</h3>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Center Form -->
    <div class="row center-page">
        <div class="col-xl-6 d-flex">
            <div class="card flex-fill">

                <div class="card-body">
                    <form action="{{ route('admin.role-update',$data->id) }}" method='POST'>

                        @csrf
                        <div class="row mb-3">
                            <label class="col-lg-3 form-label">Role <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input type="text" name="role_name" class="form-control" value="{{ old('role_name',$data->name) }}">
                                @error('role_name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
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
@endsection
