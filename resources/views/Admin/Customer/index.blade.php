@extends('site.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')

    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title"> Customer Lists</h3>
            </div>

            <div class="col-auto">
                {{-- Search handled by DataTables --}}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Customer List</h4>
                    <div>
                        <a href="{{ route('admin.customer-create') }}" class="btn btn-info btn-sm">Create</a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Branch Filter -->
                    <form method="GET" action="{{ route('admin.customer-list') }}" class="mb-4" id="customerFilterForm" onsubmit="return false;">
                        <div class="row align-items-end">
                            <div class="col-md-4 col-lg-3">
                                <div class="form-group mb-0">
                                    <label for="branch_id" class="form-label fw-bold">Select Branch</label>
                                    <select name="branch_id" id="branch_id" class="form-select">
                                        <option value="">All Branches</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" {{ (isset($branch_id) && $branch_id == $branch->id) ? 'selected' : '' }}>
                                                {{ $branch->branch_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped" id="customerTable">
                            <thead class="table-primary">
                                <tr>
                                    <th>S.No</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Loan Collection Type</th>
                                    <th>Total Payable Amount</th>
                                    <th>Remaining Amount</th>
                                    <th>Branch</th>
                                    <th>Route</th>
                                    @canany(['customer-edit', 'customer-view'])
                                        <th>Actions</th>
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody id="customerTableBody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            var table = $('#customerTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.customer-list') }}",
                    data: function(d) {
                        d.branch_id = $('#branch_id').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'phone', name: 'phone' },
                    { data: 'collection_type', name: 'collection_type', orderable: false, searchable: false },
                    { data: 'total_payable', name: 'total_payable', orderable: false, searchable: false },
                    { data: 'remaining_amount', name: 'remaining_amount', orderable: false, searchable: false },
                    { data: 'branch_name', name: 'branch.branch_name' },
                    { data: 'route_name', name: 'route.route_name' },
                    @canany(['customer-edit', 'customer-view'])
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                    @endcanany
                ],
                lengthChange: true,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                buttons: [
                    { extend: 'copy', className: 'btn btn-default', title: 'Pannai - Customer List' },
                    { extend: 'csv', className: 'btn btn-default', title: 'Pannai - Customer List' },
                    { extend: 'excel', className: 'btn btn-default', title: 'Pannai - Customer List' },
                    { extend: 'pdf', className: 'btn btn-default', title: 'Pannai - Customer List' },
                    { extend: 'print', className: 'btn btn-default', title: 'Pannai - Customer List' }
                ]
            });

            $('#branch_id').on('change', function() {
                table.draw();
            });

            table.buttons().container()
                .appendTo('#customerTable_wrapper .col-md-6:eq(0)');
        });
    </script>

@endsection




