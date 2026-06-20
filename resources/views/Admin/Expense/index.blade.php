@extends('site.layouts.app')

@section('title', 'Expense List')

@section('content')

    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Expense Lists</h3>
            </div>
            <div class="col-auto float-end ms-auto">
                @can('expense-create')
                <button class="btn btn-primary btn-sm" onclick="addExpense()"><i class="fa fa-plus"></i> Add Expense</button>
                @endcan
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped" id="expenseTable">
                            <thead class="table-primary">
                                <tr>
                                    <th>S.No</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    @canany(['expense-edit', 'expense-delete'])
                                    <th>Actions</th>
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($expenses as $item)
                                    <tr id="row_{{ $item->id }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->expense_date ? $item->expense_date->format('d-m-Y') : '' }}</td>
                                        <td>{{ number_format($item->expense_amount, 2) }}</td>
                                        @canany(['expense-edit', 'expense-delete'])
                                        <td>
                                            @can('expense-edit')
                                            <button onclick="editExpense({{ $item->id }})" class="btn btn-warning btn-sm">Edit</button>
                                            @endcan
                                            @can('expense-delete')
                                            <button onclick="deleteExpense({{ $item->id }})" class="btn btn-danger btn-sm">Delete</button>
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

    <!-- Expense Modal -->
    <div class="modal fade" id="expenseModal" tabindex="-1" aria-labelledby="expenseModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="expenseModalLabel">Add Expense</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="expenseForm">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="expense_id" name="expense_id">
                        <div class="mb-3">
                            <label class="form-label">Expense Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="date" id="expense_date" required>
                            <span class="text-danger error-text date_error"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Expense Amount <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" name="amount" id="expense_amount" required>
                            <span class="text-danger error-text amount_error"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="saveBtn">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        if ($.fn.DataTable.isDataTable('#expenseTable')) {
            $('#expenseTable').DataTable().destroy();
        }
        $('#expenseTable').DataTable({
             "order": [[1, "desc"]]
        });
    });

    function addExpense() {
        $('#expenseForm')[0].reset();
        $('#expense_id').val('');
        $('#expenseModalLabel').text('Add Expense');
        $('.error-text').text('');
        $('#expenseModal').modal('show');
    }

    function editExpense(id) {
        $('.error-text').text('');
        $.ajax({
            url: "{{ url('admin/expense/edit') }}/" + id,
            type: "GET",
            success: function(response) {
                $('#expense_id').val(response.id);
                
                let dateVal = response.expense_date;
                if(typeof dateVal === 'string') {
                    dateVal = dateVal.split('T')[0];
                } else if(dateVal && dateVal.date) {
                    dateVal = dateVal.date.split(' ')[0];
                }
                
                $('#expense_date').val(dateVal);
                $('#expense_amount').val(response.expense_amount);
                $('#expenseModalLabel').text('Edit Expense');
                $('#expenseModal').modal('show');
            },
            error: function() {
                toastr.error('Failed to fetch data');
            }
        });
    }

    $('#expenseForm').on('submit', function(e) {
        e.preventDefault();
        $('#saveBtn').prop('disabled', true).text('Saving...');
        $('.error-text').text('');

        let id = $('#expense_id').val();
        let url = id ? "{{ url('admin/expense/update') }}/" + id : "{{ route('admin.expense-store') }}";
        
        $.ajax({
            url: url,
            type: "POST",
            data: $(this).serialize(),
            success: function(response) {
                $('#saveBtn').prop('disabled', false).text('Save');
                if (response.success) {
                    toastr.success(response.message);
                    $('#expenseModal').modal('hide');
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                }
            },
            error: function(xhr) {
                $('#saveBtn').prop('disabled', false).text('Save');
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, val) {
                        $('.' + key + '_error').text(val[0]);
                    });
                } else {
                    toastr.error('Something went wrong!');
                }
            }
        });
    });

    function deleteExpense(id) {
        if (confirm('Are you sure you want to delete this expense?')) {
            $.ajax({
                url: "{{ url('admin/expense/delete') }}/" + id,
                type: "DELETE",
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        $('#row_' + id).remove();
                    }
                },
                error: function() {
                    toastr.error('Delete failed');
                }
            });
        }
    }
</script>
@endpush
