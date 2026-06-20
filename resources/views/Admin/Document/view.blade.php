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
                <h3 class="page-title">Document View</h3>
            </div>
        </div>
    </div>

    <div class="row center-page">
        <div class="col-xl-6 d-flex">
            <div class="card flex-fill">

                <div class="card-body">

                    <h4 class="mb-4">Document Details</h4>

                    <table class="table table-bordered">
                        <tbody>
                            {{-- <tr>
                                <th>User Name</th>
                                <td>{{ $item->user->name ?? 'N/A' }}</td>
                            </tr> --}}
                            {{-- <tr>
                                <th>Email</th>
                                <td>{{ $item->user->email ?? 'N/A' }}</td>
                            </tr> --}}
                            <tr>
                                <th>Document </th>

                                <td>{{  $item->document_name}}</td>
                            </tr>
                            {{-- <tr>
                                <th>Description</th>
                                <td>{{ $item->description ?? 'N/A' }}</td>
                            </tr> --}}
                            {{-- <tr>
                                <th>Status</th>
                                <td>
                                    @if($item->status == 1)
                                    <span class="badge bg-success">Active</span>
                                    @else
                                    <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                            </tr> --}}
                            <tr>
                                <th>Created at</th>
                                <td>{{ $item->created_at->format('d M Y, h:i A') }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <a href="{{ route('admin.document-list') }}" class="btn btn-primary mt-3">Back</a>

                </div>

            </div>
        </div>
    </div>
@endsection
