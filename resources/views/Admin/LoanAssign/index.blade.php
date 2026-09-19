@extends('site.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')

    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Loan Assign Lists</h3>
            </div>
            <div class="col-auto">
                <!-- Search will now be handled by DataTable -->
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Loan Assign List</h4>
                    <div>
                        <a href="{{ route('admin.loan-assign-create') }}" class="btn btn-info btn-sm">Create</a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Branch Filter -->
                    <form method="GET" action="{{ route('admin.loan-assign-list') }}" class="mb-4" id="loanAssignFilterForm" onsubmit="return false;">
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
                        <table class="table table-hover table-bordered table-striped" id="loanAssignTable">
                            <thead class="table-primary">
                                <tr>
                                    <th>S.No</th>
                                    <th>Customer Name</th>
                                    <th>Phone</th>
                                    <th>Loan Name</th>
                                    <th>Loan Interest</th>
                                    <th>Loan Collection Type</th>
                                    <th>Document Charges</th>
                                    <th>EMI Amount</th>
                                    <th>Total Payable Amount</th>
                                    <th>Remaining Amount</th>
                                    <th>Branch</th>
                                    <th>Routes</th>
                                    <th>Loan Amount</th>
                                    @canany(['loanassign-edit','loanassign-delete'])
                                    <th>Actions</th>
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody id="loanAssignTableBody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            var table = $('#loanAssignTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.loan-assign-list') }}",
                    data: function(d) {
                        d.branch_id = $('#branch_id').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'client_name', name: 'client_name.name' },
                    { data: 'phone', name: 'phone' },
                    { data: 'loan_name', name: 'loan.loan_name' },
                    { data: 'interest_rate', name: 'int.interest_id' },
                    { data: 'collection_type', name: 'collection_type', orderable: false, searchable: false },
                    { data: 'document_charges', name: 'document_charges', orderable: false, searchable: false },
                    { data: 'emi_amount', name: 'emi_amount', orderable: false, searchable: false },
                    { data: 'total_payable', name: 'total_payable', orderable: false, searchable: false },
                    { data: 'remaining_amount', name: 'remaining_amount', orderable: false, searchable: false },
                    { data: 'branch_name', name: 'branches.branch_name' },
                    { data: 'route_name', name: 'routes.route_name' },
                    { data: 'loan_amount', name: 'loan_amount' },
                    @canany(['loanassign-edit','loanassign-delete'])
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                    @endcanany
                ],
                lengthChange: true,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                buttons: [
                    {
                        extend: 'copy',
                        className: 'btn btn-default',
                        title: 'Pannai - Loan Assign List' 
                    },
                    {
                        extend: 'csv',
                        className: 'btn btn-default',
                        title: 'Pannai - Loan Assign List' 
                    },
                    {
                        extend: 'excel',
                        className: 'btn btn-default',
                        title: 'Pannai - Loan Assign List' 
                    },
                    {
                        extend: 'pdf',
                        className: 'btn btn-default',
                        title: 'Pannai - Loan Assign List' 
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-default',
                        title: 'Pannai - Loan Assign List' 
                    }
                ]
            });

            $('#branch_id').on('change', function() {
                table.draw();
            });

            table.buttons().container()
                .appendTo('#loanAssignTable_wrapper .col-md-6:eq(0)');
        });
    </script>

@endsection