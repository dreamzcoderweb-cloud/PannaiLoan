@extends('site.layouts.app')

@section('content')

    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Group List</h3>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Group List</h4>
                    <div>
                        <a href="{{ route('admin.group-create') }}" class="btn btn-info btn-sm">Create</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="groupTable">
                            <thead class="table-primary">
                                <tr>
                                    <th>S.No</th>
                                    <th>Group Name</th>
                                    <th>Total Members</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($groups as $index => $group)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $group->group_name }}</td>
                                        <td><span class="badge bg-info">{{ $group->loan_assigns_count }} Members</span></td>
                                        <td>{{ $group->created_at->format('d-m-Y') }}</td>
                                        <td>
                                            <a href="{{ route('admin.group-view', $group->id) }}" class="btn btn-info btn-sm">
                                                <i class="ti ti-eye"></i> View
                                            </a>

                                            <a href="{{ route('admin.group-edit', $group->id) }}" class="btn btn-warning btn-sm">
                                                <i class="ti ti-edit"></i> Edit
                                            </a>

                                            <a href="{{ route('admin.group-delete', $group->id) }}"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Are you sure you want to delete this group?')">
                                                <i class="ti ti-trash"></i> Delete
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

   
    <script>
        $(document).ready(function() {
            var table = $('#groupTable').DataTable({
                lengthChange: true,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                buttons: [
                    { extend: 'copy', className: 'btn btn-default', title: 'Pannai - Group List' },
                    { extend: 'csv', className: 'btn btn-default', title: 'Pannai - Group List' },
                    { extend: 'excel', className: 'btn btn-default', title: 'Pannai - Group List' },
                    { extend: 'pdf', className: 'btn btn-default', title: 'Pannai - Group List' },
                    { extend: 'print', className: 'btn btn-default', title: 'Pannai - Group List' }
                ]
            });

            table.buttons().container()
                .appendTo('#groupTable_wrapper .col-md-6:eq(0)');
        });
    </script>

@endsection
