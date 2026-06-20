@extends('site.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')

            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title"> Routes Lists</h3>
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
                                        <th>Route Name</th>
                                        <th>Branch</th>
                                        @canany(['route-edit'])
                                        <th>Actions</th>
                                        @endcanany
                                    </tr>
                                </thead>

                                <tbody>
                                @foreach ($data as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->route_name }}</td>
                                    <td>{{ $item->branch->branch_name }}</td>
                                    @canany(['route-edit'])
                                    <td>
                                        @can('route-edit')
                                        <a href="{{ route('admin.route-edit',$item->id) }}" class="btn btn-warning btn-sm">Edit</a>
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
