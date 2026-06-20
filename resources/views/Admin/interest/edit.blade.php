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
                <h3 class="page-title">Interest Update Form</h3>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Center Form -->
    <div class="row center-page">
        <div class="col-xl-6 d-flex">
            <div class="card flex-fill">

                <div class="card-body">
                    <form action="{{ route('admin.interest-update', $item->id) }}" method='POST'>

                        @csrf
                        <div class="row mb-3">
                            <label class="col-lg-3 form-label"> Interest <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input type="text" name="interest_id" class="form-control" value="{{ old('interest_id',$item->interest_id) }}">
                                @error('interest_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror

                            </div>
                        </div>



                        <div class="row mb-3">
                            <label class="col-lg-3 form-label">Collection Type <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <select class="form-control" name="collection_type">
                                    <option value="">Select</option>
                                    <option value="1" {{ old('collection_type', $item->collection_type) == 1 ? 'selected' : '' }}>Daily</option>
                                    <option value="2" {{ old('collection_type', $item->collection_type) == 2 ? 'selected' : '' }}>Weekly</option>
                                    <option value="3" {{ old('collection_type', $item->collection_type) == 3 ? 'selected' : '' }}>Monthly</option>
                                </select>
                                @error('collection_type')
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

