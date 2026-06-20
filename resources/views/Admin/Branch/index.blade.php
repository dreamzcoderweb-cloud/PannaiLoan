@extends('site.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')

    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title"> Branch Lists</h3>
            </div>
        </div>
    </div>



    <div class="row">
        <div class="col-xl-12">
            <div class="card">



                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped">
                            <thead class="table-primary">
                                <tr>
                                    <th>S.No</th>
                                    <th>Branch Name</th>
                                    <th>Area</th>
                                    @canany(['branch-edit', 'branch-view'])
                                        <th>Actions</th>
                                    @endcanany
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($data as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>

                                        <td>{{ $item->branch_name }}</td>
                                        <td>{{ $item->city_name }}</td>
                                        @canany(['branch-edit', 'branch-view'])
                                        <td>
                                            @can('branch-view')
                                            <a href="{{ route('admin.branch-view', $item->id) }}"
                                                class="btn btn-success btn-sm">View</a>
                                            @endcan

                                                @can('branch-edit')
                                                <a href="{{ route('admin.branch-edit', $item->id) }}"
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
