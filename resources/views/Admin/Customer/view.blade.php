@extends('site.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <style>
        .center-page {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
        }
    </style>

    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Customer View</h3>
            </div>
        </div>
    </div>

    <div class="row center-page">
        <div class="col-xl-6 d-flex">
            <div class="card flex-fill">

                <div class="card-body">

                    <h4 class="mb-4">Customer Details</h4>

                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th>Name</th>
                                <td>{{ $data->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Phone</th>
                                <td>{{ $data->phone ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Branch </th>

                                <td>{{  $data->branch->branch_name}}</td>
                            </tr>
                            <tr>
                                <th>Route</th>
                                <td>{{ $data->route->route_name  }}</td>
                            </tr>

                            <tr>
                                <th>Created at</th>
                                <td>{{ $data->created_at->format('d M Y, h:i A') }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <a href="{{ route('admin.customer-list') }}" class="btn btn-primary mt-3">Back</a>

                </div>

            </div>
        </div>
    </div>
@endsection
