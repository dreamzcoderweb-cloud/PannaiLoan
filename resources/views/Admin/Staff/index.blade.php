@extends('site.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')

    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Staff Lists</h3>
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
                                    <th>Role</th>
                                    @canany(['staff-edit'])
                                    <th>Actions</th>
                                    @endcanany

                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($data as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->name }}</td>
                                        @canany(['staff-edit'])
                                        <td>
                                            @can('staff-edit')
                                            <a href="{{ route('admin.staff-edit', $item->id) }}"
                                                class="btn btn-warning btn-sm">Edit
                                            </a>
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
