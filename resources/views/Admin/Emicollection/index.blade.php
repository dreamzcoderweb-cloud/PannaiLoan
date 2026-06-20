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
                                @foreach($data as $item)
                                <tr class="emicollection-row">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->clientname->name ?? '---' }}</td>
                                    <td>{{ $item->loanassign->client_type == 1 ? 'Old' : 'New' }}</td>
                                    <td>
                                        @if($item->collection_type_id == 1) Daily
                                        @elseif($item->collection_type_id == 2) Weekly
                                        @elseif($item->collection_type_id == 3) Monthly
                                        @else ---
                                        @endif
                                    </td>
                                    <td>{{ $item->total_payable_amount }}</td>
                                    <td>{{ $item->total_remaining }}</td>
                                    <td>{{ $item->total_collected }}</td>
                                    @canany(['emicollection-view'])
                                    <td>
                                        <a href="{{ route('admin.emicollection-view', $item->id) }}" class="btn btn-success btn-sm me-2">View</a>
                                        @canany(['emicollection-delete'])
                                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $item->id }}">Delete</button>
                                        @endcanany
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

    <!-- Delete Confirmation Modals -->
    @foreach($data as $item)
    <div class="modal fade" id="deleteModal{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel{{ $item->id }}" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel{{ $item->id }}">
                        <i class="ti ti-alert-triangle me-2"></i>Confirm Delete
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">Are you sure you want to delete this EMI collection?</p>
                    <div class="alert alert-info" role="alert">
                        <strong>Customer:</strong> {{ $item->clientname->name ?? '---' }}<br>
                        <strong>Amount:</strong> ₹{{ number_format($item->total_payable_amount, 2) }}<br>
                        <strong>Type:</strong> 
                        @if($item->collection_type_id == 1) Daily
                        @elseif($item->collection_type_id == 2) Weekly
                        @elseif($item->collection_type_id == 3) Monthly
                        @else ---
                        @endif
                    </div>
                    <p class="text-danger mb-0"><strong>Warning:</strong> This action cannot be undone. All related installment records will also be deleted.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('admin.emicollection-delete', $item->id) }}" method="POST" style="display:inline;">
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
    @endforeach

    <script>
        $(document).ready(function () {
            var table = $('#emiCollectionTable').DataTable({
                lengthChange: true,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                buttons: [
                    { extend: 'copy', className: 'btn btn-default', title: 'Pannai - EMI Collection List' },
                    { extend: 'csv', className: 'btn btn-default', title: 'Pannai - EMI Collection List' },
                    { extend: 'excel', className: 'btn btn-default', title: 'Pannai - EMI Collection List' },
                    { extend: 'pdf', className: 'btn btn-default', title: 'Pannai - EMI Collection List' },
                    { extend: 'print', className: 'btn btn-default', title: 'Pannai - EMI Collection List' }
                ]
            });

            table.buttons().container()
                .appendTo('#emiCollectionTable_wrapper .col-md-6:eq(0)');
        });
    </script>

@endsection
