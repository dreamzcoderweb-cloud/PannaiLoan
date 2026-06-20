@extends('site.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')

    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title"> Loan Lists</h3>
            </div>
        </div>
    </div>



    <div class="row">
        <div class="col-xl-12">
            <div class="card">

                <!-- Top Buttons -->
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Loan Details</h4>

                    <div>
                        <a href="{{ route('admin.loan-create')}}" class="btn btn-info btn-sm">Create</a>
                        <a href="{{ route('admin.loan-export-pdf') }}" class="btn btn-danger btn-sm">PDF</a>
                        <a href="{{ route(name: 'admin.loan-export-excel') }}" class="btn btn-danger btn-sm">Excel</a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped">
                            <thead class="table-primary">
                                <tr>
                                    <th>S.No</th>
                                    <th>Name of Loan</th>
                                    @canany(['loan-edit', 'loan-view'])
                                    <th>Actions</th>
                                    @endcanany
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($data as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->loan_name }}</td>
                                        @canany(['loan-view', 'loan-edit'])
                                        <td>
                                            @can('loan-view')
                                            <a href="{{ route('admin.loan-view', $item->id) }}"
                                                class="btn btn-success btn-sm">View</a>
                                            @endcan

                                            @can('loan-edit')
                                            <a href="{{ route('admin.loan-edit', $item->id) }}"
                                                class="btn btn-warning btn-sm">Edit</a>
                                            @endcan

                                        </td>
                                        @endcanany

                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection
