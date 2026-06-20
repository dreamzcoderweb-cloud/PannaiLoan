@extends('site.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')

    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title"> Interest Lists</h3>
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
                                    <th>Interest</th>
                                    <th>Collection Type</th>

                                    @canany(['interest-edit', 'interest-view'])
                                        <th>Actions</th>
                                    @endcanany
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($data as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->interest_id }}</td>
                                        <td>
                                            @if($item->collection_type == 1)
                                                Daily
                                            @elseif($item->collection_type == 2)
                                                Weekly
                                            @elseif($item->collection_type == 3)
                                                Monthly
                                            @endif
                                        </td>

                                        @canany(['interest-edit', 'interest-view'])
                                            <td>
                                                @can('interest-view')
                                                    <a href="{{ route('admin.interest-view', $item->id) }}"
                                                       class="btn btn-success btn-sm">View</a>
                                                @endcan

                                                @can('interest-edit')
                                                    <a href="{{ route('admin.interest-edit', $item->id) }}"
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
