@extends('site.layouts.app')

@section('title', 'Edit Group')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Edit Group: {{ $group->group_name }}</h3>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.group-list') }}" class="btn btn-secondary btn-sm">Group List</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.group-update', $group->id) }}" method="POST">
                        @csrf
                        <div class="row mb-4">
                            <div class="col-lg-6">
                                <label class="form-label">Group Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="group_name" value="{{ $group->group_name }}" placeholder="Enter Group Name" required>
                            </div>
                        </div>

                        <hr>

                        <div class="row mb-3">
                            <div class="col-lg-12">
                                <label class="form-label">Search Customer to Add (Name or Phone)</label>
                                <div class="position-relative">
                                    <input type="text" id="customer_search" class="form-control" placeholder="Type name or phone number..." autocomplete="off">
                                    <div id="search_results" class="list-group position-absolute w-100 mt-1 shadow-lg" style="z-index: 1000; display: none; max-height: 200px; overflow-y: auto;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden inputs for totals -->
                        <input type="hidden" name="total_loan_amount" id="total_loan_amount_input" value="0">
                        <input type="hidden" name="total_remaining_amount" id="total_remaining_amount_input" value="0">

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="selected_customers_table">
                                <thead class="table-primary">
                                    <tr>
                                        <th>Customer Name</th>
                                        <th>Branch</th>
                                        <th>Route</th>
                                        <th>Loan</th>
                                        <th>Collection Type</th>
                                        <th>Loan Amount</th>
                                        <th>Remaining Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="selected_customers_body">
                                    @php
                                        $totalLoanAmount = 0;
                                        $totalRemainingAmount = 0;
                                    @endphp
                                    @foreach($group->loanAssigns as $loan)
                                        @php
                                            $emi = $loan->emiCollections->first();
                                            $remaining = $emi ? $emi->total_remaining : $loan->total_payableamt;
                                            $loanAmount = $loan->loan_amount;
                                            
                                            $collection_type = '';
                                            if ($loan->collection_type_id == 1) $collection_type = 'Daily';
                                            elseif ($loan->collection_type_id == 2) $collection_type = 'Weekly';
                                            elseif ($loan->collection_type_id == 3) $collection_type = 'Monthly';
                                            
                                            // Calculate totals
                                            $totalLoanAmount += $loanAmount;
                                            $totalRemainingAmount += $remaining;
                                        @endphp
                                        <tr id="loan_row_{{ $loan->id }}">
                                            <td>{{ $loan->client_name->name ?? '---' }} ({{ $loan->phone }})
                                                <input type="hidden" name="loanassign_id[]" value="{{ $loan->id }}">
                                                <input type="hidden" name="customer_id[]" value="{{ $loan->client_id }}">
                                                <input type="hidden" name="customer_amount_{{ $loan->id }}" value="{{ $loanAmount }}">
                                                <input type="hidden" name="customer_remaining_{{ $loan->id }}" value="{{ $remaining }}">
                                            </td>
                                            <td>{{ $loan->branches->branch_name ?? '---' }}</td>
                                            <td>{{ $loan->routes->route_name ?? '---' }}</td>
                                            <td>{{ $loan->loan->loan_name ?? '---' }}</td>
                                            <td>{{ $collection_type }}</td>
                                            <td class="loan-amount">₹{{ number_format($loanAmount, 2) }}</td>
                                            <td class="remaining-amount">₹{{ number_format($remaining, 2) }}</td>
                                            <td>
                                                <button type="button" class="btn btn-secondary btn-sm view-customer" data-id="{{ $loan->id }}">
                                                    <i class="ti ti-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm remove-customer" 
                                                        data-id="{{ $loan->id }}" 
                                                        data-amount="{{ $loanAmount }}" 
                                                        data-remaining="{{ $remaining }}">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr id="no_customer_placeholder" style="{{ $group->loanAssigns->count() > 0 ? 'display: none;' : '' }}">
                                        <td colspan="8" class="text-center">No customers selected yet.</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr id="totals_row" style="{{ $group->loanAssigns->count() > 0 ? '' : 'display: none;' }}">
                                        <td colspan="5" class="text-end fw-bold">Total:</td>
                                        <td id="total_loan_display">₹{{ number_format($totalLoanAmount, 2) }}</td>
                                        <td id="total_remaining_display">₹{{ number_format($totalRemainingAmount, 2) }}</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-primary">Update Group</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            let selectedLoanIds = {!! json_encode($group->loanAssigns->pluck('id')->toArray()) !!};
            let totalLoanAmount = parseFloat({{ $totalLoanAmount }});
            let totalRemainingAmount = parseFloat({{ $totalRemainingAmount }});
            let customerData = {};

            // Initialize customerData with existing customers
            @foreach($group->loanAssigns as $loan)
                @php
                    $emi = $loan->emiCollections->first();
                    $remaining = $emi ? $emi->total_remaining : $loan->total_payableamt;
                @endphp
                customerData[{{ $loan->id }}] = {
                    id: {{ $loan->id }},
                    amount: parseFloat({{ $loan->loan_amount }}),
                    remaining: parseFloat({{ $remaining }})
                };
            @endforeach

            function updateTotals() {
                // Update display
                $('#total_loan_display').text('₹' + totalLoanAmount.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                $('#total_remaining_display').text('₹' + totalRemainingAmount.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                
                // Update hidden inputs for form submission
                $('#total_loan_amount_input').val(totalLoanAmount);
                $('#total_remaining_amount_input').val(totalRemainingAmount);
                
                // Show/hide totals row
                if (selectedLoanIds.length > 0) {
                    $('#totals_row').show();
                } else {
                    $('#totals_row').hide();
                }
            }

            // Initialize totals on page load
            updateTotals();

            $('#customer_search').on('keyup', function() {
                let search = $(this).val();
                if (search.length >= 2) {
                    $.ajax({
                        url: "{{ route('admin.group-search-customers') }}",
                        method: 'GET',
                        data: { search: search },
                        success: function(data) {
                            let html = '';
                            if (data.length > 0) {
                                data.forEach(function(item) {
                                    html += `<a href="javascript:void(0);" class="list-group-item list-group-item-action add-customer" 
                                                data-id="${item.id}" 
                                                data-name="${item.name}" 
                                                data-phone="${item.phone}" 
                                                data-branch="${item.branch}" 
                                                data-route="${item.route}" 
                                                data-loan="${item.loan_name}" 
                                                data-type="${item.collection_type}" 
                                                data-amount="${item.loan_amount}" 
                                                data-remaining="${item.remaining_amount}">
                                                <div class="d-flex justify-content-between">
                                                    <span><strong>${item.name}</strong> (${item.phone})</span>
                                                    <span class="text-muted small">${item.loan_name} - ${item.branch}</span>
                                                </div>
                                                <div class="small text-muted">
                                                    Loan: ₹${parseFloat(item.loan_amount).toLocaleString('en-IN')} | Remaining: ₹${parseFloat(item.remaining_amount).toLocaleString('en-IN')}
                                                </div>
                                            </a>`;
                                });
                                $('#search_results').html(html).show();
                            } else {
                                $('#search_results').html('<div class="list-group-item text-muted">No results found</div>').show();
                            }
                        }
                    });
                } else {
                    $('#search_results').hide();
                }
            });

            $(document).on('click', '.add-customer', function() {
                let id = $(this).data('id');
                let name = $(this).data('name');
                let phone = $(this).data('phone');
                let branch = $(this).data('branch');
                let route = $(this).data('route');
                let loan = $(this).data('loan');
                let type = $(this).data('type');
                let amount = parseFloat($(this).data('amount'));
                let remaining = parseFloat($(this).data('remaining'));

                if (selectedLoanIds.includes(parseInt(id))) {
                    toastr.warning('This customer loan is already added to the group.');
                    return;
                }

                $('#no_customer_placeholder').hide();

                // Store customer data
                customerData[id] = {
                    id: id,
                    amount: amount,
                    remaining: remaining
                };

                let row = `<tr id="loan_row_${id}">
                                <td>${name} (${phone})
                                    <input type="hidden" name="loanassign_id[]" value="${id}">
                                    <input type="hidden" name="customer_amount_${id}" value="${amount}">
                                    <input type="hidden" name="customer_remaining_${id}" value="${remaining}">
                                </td>
                                <td>${branch}</td>
                                <td>${route}</td>
                                <td>${loan}</td>
                                <td>${type}</td>
                                <td class="loan-amount">₹${amount.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                <td class="remaining-amount">₹${remaining.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                <td>
                                    <button type="button" class="btn btn-secondary btn-sm view-customer" data-id="${id}">
                                        <i class="ti ti-eye"></i>
                                    </button>

                                    <button type="button" class="btn btn-danger btn-sm remove-customer" 
                                            data-id="${id}" 
                                            data-amount="${amount}" 
                                            data-remaining="${remaining}">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </td>
                            </tr>`;

                $('#selected_customers_body').append(row);
                selectedLoanIds.push(parseInt(id));
                
                // Update totals
                totalLoanAmount += amount;
                totalRemainingAmount += remaining;
                updateTotals();
                
                $('#search_results').hide();
                $('#customer_search').val('');
            });

            $(document).on('click', '.remove-customer', function() {
                let id = $(this).data('id');
                let amount = parseFloat($(this).data('amount'));
                let remaining = parseFloat($(this).data('remaining'));
                
                $(`#loan_row_${id}`).remove();
                selectedLoanIds = selectedLoanIds.filter(loanId => loanId !== parseInt(id));
                
                // Remove from customerData
                delete customerData[id];
                
                // Update totals
                totalLoanAmount -= amount;
                totalRemainingAmount -= remaining;
                updateTotals();

                if (selectedLoanIds.length === 0) {
                    $('#no_customer_placeholder').show();
                }
            });

            $(document).on('click', '.view-customer', function () {
            let id = $(this).data('id');
            window.location.href = "{{ route('admin.getclient-emidetails', ':id') }}".replace(':id', id);
            });

            // Close results when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#customer_search').length) {
                    $('#search_results').hide();
                }
            });
        });
    </script>
@endsection