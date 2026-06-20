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
                <h3 class="page-title">Loan View</h3>
            </div>
        </div>
    </div>

    <div class="row center-page">
        <div class="col-xl-6 d-flex">
            <div class="card flex-fill">

                <div class="card-body">

                    <h4 class="mb-4">Loan Details</h4>

                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th>Loan Name</th>
                                <td>{{ $item->loan_name ?? 'N/A' }}</td>
                            </tr>

                            <tr>
                                <th>Created at</th>
                                <td>{{ $item->created_at->format('d M Y, h:i A') }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <a href="{{ route('admin.loan-list') }}" class="btn btn-primary mt-3">Back</a>

                </div>

            </div>
        </div>
    </div>
@endsection
