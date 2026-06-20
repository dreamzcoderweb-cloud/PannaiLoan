@extends('site.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<style>
    .center-page {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 80vh; /* adjust as needed */
    }
    </style>
<!-- Page Header -->
<div class="page-header">
    <div class="row">
        <div class="col">
            <h3 class="page-title">Routes Create Form</h3>
        </div>
    </div>
</div>
<!-- /Page Header -->

<!-- Center Form -->
<div class="row center-page">
    <div class="col-xl-6 d-flex">
        <div class="card flex-fill">

            <div class="card-body">
                <form action="{{ route('admin.route-store') }}" method="POST">
                    @csrf
                    <div class="row mb-3">
                        <label class="col-lg-3 form-label">Route Name : <span class="text-danger">*</span></label>
                        <div class="col-lg-9">
                            <input type="text" class="form-control" name="route_name" id="route_name"
                                value="{{ old('route_name', $original->route_name ?? '') }}">
                                @error('route_name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-lg-3 form-label">Select Branch <span class="text-danger">*</span></label>
                        <div class="col-lg-9">
                            <select class="select" name="branch_id" id="branch_id">
                                <option value="">Select</option>
                                @foreach ($branches as $item)
                                        <option value="{{ $item->id }}" data-rate="{{ $item->branch_name }}"
                                            {{ old('branch_id', $original->id ?? '') == $item->id ? 'selected' : '' }}>
                                            {{ $item->branch_name }}
                                        </option>
                                @endforeach
                            </select>
                            @error('branch_id')
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
