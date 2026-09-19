@extends('site.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')

    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">EMI Collection Lists</h3>
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
                    <h4 class="card-title mb-0">EMI Collection List</h4>
                    <div>
                        <a href="{{ route('admin.emicollection-create') }}" class="btn btn-info btn-sm">Create</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped" id="emiCollectionTable">
                            <thead class="table-primary">
                                <tr>
                                    <th>S.No</th>
                                    <th>Customer</th>
                                    <th>Customer Type</th>
                                    <th>Loan Collection Type</th>
                                    <th>Total Payable Amount</th>
                                    <th>Remaining Amount</th>
                                    <th>Paid Amount</th>
                                    @canany([ 'emicollection-view'])
                                        <th>Actions</th>
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody id="emiCollectionTableBody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reusable Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel">
                        <i class="ti ti-alert-triangle me-2"></i>Confirm Delete
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">Are you sure you want to delete this EMI collection?</p>
                    <div class="alert alert-info" role="alert">
                        <strong>Customer:</strong> <span id="modalCustomerName">---</span><br>
                        <strong>Amount:</strong> ₹<span id="modalAmount">0.00</span><br>
                        <strong>Type:</strong> <span id="modalType">---</span>
                    </div>
                    <p class="text-danger mb-0"><strong>Warning:</strong> This action cannot be undone. All related installment records will also be deleted.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="modalDeleteForm" action="" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="ti ti-trash me-2"></i>Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            var table = $('#emiCollectionTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.emicollection-list') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'client_name', name: 'clientname.name' },
                    { data: 'customer_type', name: 'customer_type', orderable: false, searchable: false },
                    { data: 'collection_type', name: 'collection_type', orderable: false, searchable: false },
                    { data: 'total_payable_amount', name: 'total_payable_amount' },
                    { data: 'total_remaining', name: 'total_remaining' },
                    { data: 'total_collected', name: 'total_collected' },
                    @canany(['emicollection-view'])
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                    @endcanany
                ],
                lengthChange: true,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                buttons: [
                    { extend: 'copy', className: 'btn btn-default', title: 'Pannai - EMI Collection List' },
                    { extend: 'csv', className: 'btn btn-default', title: 'Pannai - EMI Collection List' },
                    { extend: 'excel', className: 'btn btn-default', title: 'Pannai - EMI Collection List' },
                    { extend: 'pdf', className: 'btn btn-default', title: 'Pannai - EMI Collection List' },
                    { extend: 'print', className: 'btn btn-default', title: 'Pannai - EMI Collection List' }
                ]
            });

            // Handle dynamic delete modal population
            $(document).on('click', '.delete-btn', function () {
                var url = $(this).data('url');
                var customer = $(this).data('customer');
                var amount = $(this).data('amount');
                var type = $(this).data('type');

                $('#modalCustomerName').text(customer);
                $('#modalAmount').text(amount);
                $('#modalType').text(type);
                $('#modalDeleteForm').attr('action', url);

                var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
                deleteModal.show();
            });

            table.buttons().container()
                .appendTo('#emiCollectionTable_wrapper .col-md-6:eq(0)');
        });
    </script>

@endsection
