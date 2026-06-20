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
                <h3 class="page-title">Customer Update Form</h3>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Center Form -->
    <div class="row center-page">
        <div class="col-xl-6 d-flex">
            <div class="card flex-fill">

                <div class="card-body">
                    <form action="{{ route('admin.customer-update',$original->id) }}" method="POST">
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
                            <label class="col-lg-3 form-label">Phone : <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="phone" value="{{ old('phone',$original->phone) }}">
                                @error('phone')
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

                        <div class="row mb-3 form-group">
                            <label class="col-lg-3 form-label">Select Branch <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <select name="branch_id" class="form-control select">
                                    <option value="">Select</option>
                                    @foreach ($branches as $item)
                                        <option value="{{ $item->id }}"
                                            {{ old('branch_id', $original->branch_id ?? '') == $item->id ? 'selected' : '' }}>
                                            {{ $item->branch_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="error-container" id="branch_id_error"></div>
                                @error('branch_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3 form-group">
                            <label class="col-lg-3 form-label">Select Routes <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <select name="route_id" class="form-control select">
                                    <option value="">Select</option>
                                    @foreach ($routes as $item)
                                        <option value="{{ $item->id }}"
                                            {{ old('route_id', $original->route_id ?? '') == $item->id ? 'selected' : '' }}>
                                            {{ $item->route_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="error-container" id="route_id_error"></div>
                                @error('route_id')
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
