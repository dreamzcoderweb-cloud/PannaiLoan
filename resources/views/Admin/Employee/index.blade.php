@extends('site.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')

    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title"> Employee Lists</h3>
            </div>

           
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Employee List</h4>
                    <div>
                        <a href="{{ route('admin.employee-create') }}" class="btn btn-info btn-sm">Create</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped" id="employeeTable">
                            <thead class="table-primary">
                                <tr>
                                    <th>S.No</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Branch</th>
                                    <th>Route</th>
                                    <th>ID Proofs</th>
                                    @canany(['employee-edit', 'employee-delete'])
                                        <th>Actions</th>
                                    @endcanany
                                </tr>
                            </thead>

                            <tbody id="employeeTableBody">
                                @foreach ($data as $item)
                                    <tr class="employee-row">
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="employee-name">{{ $item->name }}</td>
                                        <td class="employee-phone">{{ $item->phone }}</td>
                                        <td>{{ $item->branch->branch_name ?? '---' }}</td>
                                        <td>{{ $item->route->route_name ?? '---' }}</td>
                                        <td>
                                            @if($item->idProofs->count() > 0)
                                                <span class="badge bg-info">{{ $item->idProofs->count() }} Files</span>
                                            @else
                                                ---
                                            @endif
                                        </td>
                                        @canany(['employee-edit', 'employee-delete'])
                                        <td>
                                            @can('employee-edit')
                                            <a href="{{ route('admin.employee-edit', $item->id) }}"
                                                class="btn btn-warning btn-sm">Edit
                                            </a>
                                            @endcan
                                            @can('employee-delete')
                                            <a href="{{ route('admin.employee-delete', $item->id) }}"
                                                class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this employee?')">Delete
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

 
    <script>
        $(document).ready(function() {
            var table = $('#employeeTable').DataTable({
                lengthChange: true,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                buttons: [
                    { extend: 'copy', className: 'btn btn-default', title: 'Pannai - Employee List' },
                    { extend: 'csv', className: 'btn btn-default', title: 'Pannai - Employee List' },
                    { extend: 'excel', className: 'btn btn-default', title: 'Pannai - Employee List' },
                    { extend: 'pdf', className: 'btn btn-default', title: 'Pannai - Employee List' },
                    { extend: 'print', className: 'btn btn-default', title: 'Pannai - Employee List' }
                ]
            });

            table.buttons().container()
                .appendTo('#employeeTable_wrapper .col-md-6:eq(0)');
        });
    </script>

@endsection
