@extends('site.layouts.app')

@section('title', 'Daily EMI Collection Report')

@section('content')

    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Daily EMI Collection Report</h3>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <!-- Search Form -->
                    <form action="{{ route('admin.report-daily.filter') }}" method="POST" class="mb-4">
                        @csrf
                        <div class="row align-items-end">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="reportDate">Branch <span class="text-danger">*</span></label>
                                    <select name="branch_id" id="branch_id" class="form-control" required>
                                        <option value="">Select Branch</option>
                                        @foreach($getbranches as $branch)
                                            <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->branch_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                             {{-- <div class="col-md-4">
                                <div class="form-group">
                                    <label for="reportDate">Route <span class="text-danger">*</span></label>
                                    <select name="route_id" id="route_id" class="form-control" required>
                                        <option value="">Select Route</option>
                                        @foreach($getroutes as $route)
                                            <option value="{{ $route->id }}" {{ old('route_id') == $route->id ? 'selected' : '' }}>
                                                {{ $route->route_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> --}}
                             <div class="col-md-4">
                                <div class="form-group">
                                    <label for="reportDate">Select Date <span class="text-danger">*</span></label>
                                    <input type="date" name="monthlydue_date" id="reportDate" class="form-control"
                                        value="{{ $selectedDate ?? $today }}">
                                </div>
                            </div>
                            <div class="col-md-2 mt-2">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-search"></i> Generate Report
                                    </button>
                                </div>
                            </div>

                        </div>
                    </form>

                    <!-- Results Section -->
                    @if(isset($emiCollections) && $emiCollections->isNotEmpty())
                        <!-- Datatable Controls -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-end">
                                    <!-- Export Dropdown -->
                                    <div class="dropdown d-inline-block">
                                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="exportDropdown"
                                            data-bs-toggle="dropdown">
                                            <i class="ti ti-download"></i> Export Report
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                                            <li><a class="dropdown-item export-btn" data-type="excel" href="#">
                                                    <i class="ti ti-file-spreadsheet text-success"></i> Excel
                                                </a></li>
                                            <li><a class="dropdown-item export-btn" data-type="csv" href="#">
                                                    <i class="ti ti-file-text text-primary"></i> CSV
                                                </a></li>
                                            <li><a class="dropdown-item export-btn" data-type="pdf" href="#">
                                                    <i class="ti ti-file-pdf text-danger"></i> PDF
                                                </a></li>
                                            <li><a class="dropdown-item export-btn" data-type="print" href="#">
                                                    <i class="ti ti-printer text-info"></i> Print
                                                </a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Datatable -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="dailyEmiTable">
                                <thead class="table-primary">
                                    <tr>
                                        <th>#</th>
                                        <th>Branch</th>
                                        <th>Route</th>
                                        <th>Customer Name</th>
                                        <th>Phone Number</th>
                                        <th>Loan Type</th>
                                        <th>Loan Amount</th>
                                        <th>Paid Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   @php
                                        // Keep DB date format always as Y-m-d (do not overwrite $selectedDate)
                                        $selectedDateYmd = isset($selectedDate) && !empty($selectedDate)
                                            ? \Carbon\Carbon::parse($selectedDate)->toDateString()
                                            : ($today ?? \Carbon\Carbon::today()->toDateString());

                                        $currentDateObj = \Carbon\Carbon::parse($selectedDateYmd);
                                        $previousDateYmd = $currentDateObj->copy()->subDay()->toDateString();

                                        // Requirement: when selecting current date, show previous date final balance as opening balance
                                        $previous_totalpaidamount = $previousFinalBalance ?? 0;

                                        // Current day paid amount should always be derived from the table total;
                                        // saved summary values can be stale if more payments are added later.
                                        $currentday_totalpaidamount = $totalPaidAmount ?? 0;

                                        $totalamt = $previous_totalpaidamount + $currentday_totalpaidamount + $md_fund_in ;
                                        $finalbalanceamt = $totalamt - ($totalLoanAmountToday ?? 0) - ($totalExpenses ?? 0) - ($md_fund_out ?? 0);
                                    @endphp
                                    @foreach($emiCollections as $index => $collection)
                                        @php
                                            $dailyPaid = $collection->details->sum('paid_amount');
                                            $loanType = $collection->loanassign->collection_type_id ?? null;

                                            if ($loanType == 3) {
                                                $EmiMethod = 'Monthly';
                                            } elseif ($loanType == 2) {
                                                $EmiMethod = 'Weekly';
                                            } else {
                                                $EmiMethod = 'Daily';
                                            }

                                            $badgeColor = match ($EmiMethod) {
                                                'Weekly' => 'warning',
                                                'Monthly' => 'info',
                                                'Daily' => 'success',
                                                default => 'secondary',
                                            };
                                        @endphp
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                {{ $collection->branch->branch_name ?? 'N/A' }}
                                            </td>
                                            <td>
                                                {{ $collection->routename->route_name ?? 'N/A' }}
                                            </td>
                                            <td>
                                                <strong>{{ $collection->clientname->name ?? 'N/A' }}</strong>
                                                <br>
                                                <small class="text-muted">
                                                    Loan Name: {{ $collection->loanassign->loan->loan_name ?? 'N/A' }}
                                                </small>
                                            </td>
                                            <td>
                                                {{ $collection->clientname->phone ?? 'N/A' }}
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $badgeColor }}">
                                                    {{ $EmiMethod }}
                                                </span>
                                            </td>
                                            <td data-order="{{ $collection->total_payable_amount ?? 0 }}">
                                                ₹{{ number_format($collection->total_payable_amount ?? 0, 2) }}
                                            </td>
                                            <td data-order="{{ $dailyPaid }}">
                                                <strong class="text-success">
                                                    ₹{{ number_format($dailyPaid, 2) }}
                                                </strong>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="7" class="text-end">
                                            <strong>Total Paid Amount:</strong>
                                        </td>
                                        <td id="footerTotalPaidAmount">
                                            <strong class="text-primary">
                                                ₹{{ number_format($totalPaidAmount ?? 0, 2) }}
                                            </strong>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Collection Summary Section -->
                        <div class="row mt-4 mb-4" id="reportSummarySection">
                            <div class="col-md-6 offset-md-3">
                                <div class="card border-primary shadow-sm">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0 text-white"><i class="ti ti-report-analytics"></i> Collection Summary
                                        </h5>
                                    </div>
                                    <div class="card-body p-0">
                                        @if($errors->any())
                                            <div class="alert alert-danger m-3 mb-0">
                                                <ul class="mb-0">
                                                    @foreach($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif

                                        <form method="POST" action="{{ route('admin.collection-summary.save') }}" class="p-3">
                                            @csrf
                                            <input type="hidden" name="report_type" value="daily">
                                            <div class="row g-2">
                                                <div class="col-md-6">
                                                    <label class="form-label mb-1">Previous Date</label>
                                                    <input type="date" name="previous_date" class="form-control"
                                                        value="{{ old('previous_date', $previousDateYmd) }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label mb-1">Current Date</label>
                                                    <input type="date" name="current_date" class="form-control" readonly
                                                        value="{{ old('current_date', $selectedDateYmd) }}">
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label mb-1">Previous Total Paid Amount ({{ $previousDateLabel ?? '' }})</label>
                                                    <input type="number" step="0.01" name="previous_total_paidamount" class="form-control"
                                                       value="{{ old('previous_total_paidamount', isset($existingSummary) ? $existingSummary->previous_total_paidamount : ($previous_totalpaidamount ?? 0)) }}" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label mb-1">Current Day Total Paid Amount ({{ isset($selectedDate) ? \Carbon\Carbon::parse($selectedDate)->format('d-m-Y') : date('d-m-Y') }})</label>
                                                    <input type="number" step="0.01" name="current_total_paidamount" class="form-control"
                                                        value="{{ old('current_total_paidamount', $currentday_totalpaidamount ?? 0) }}" required>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label mb-1">Total Amount</label>
                                                    <input type="number" step="0.01" name="total_amount" class="form-control" readonly
                                                        value="{{ old('total_amount', $totalamt ?? 0) }}" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label mb-1">Total Loan (Count)</label>
                                                    <input type="number" name="total_loan" class="form-control"
                                                        value="{{ old('total_loan', $totalLoansTodayCount ?? 0) }}" required>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label mb-1">Total Loan Amount</label>
                                                    <input type="number" step="0.01" name="total_loanamount" class="form-control"
                                                        value="{{ old('total_loanamount', $totalLoanAmountToday ?? 0) }}" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label mb-1">Expense Amount (Current Date)</label>
                                                    <input type="number" step="0.01" name="expense_amount_currentdate" class="form-control"
                                                        value="{{ old('expense_amount_currentdate', $totalExpenses ?? 0) }}" required>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label mb-1">MD Fund In</label>
                                                    <input type="number" step="0.01" name="md_fund_in" id="md_fund_in" class="form-control"
                                                        value="{{ old('md_fund_in', $md_fund_in ?? 0) }}" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label mb-1">MD Fund Out</label>
                                                    <input type="number" step="0.01" name="md_fund_out" id="md_fund_out" class="form-control"
                                                        value="{{ old('md_fund_out', $md_fund_out ?? 0) }}" required>
                                                </div>


                                                <div class="col-md-12">
                                                    <label class="form-label mb-1">Final Balance Amount</label>
                                                    <input type="number" step="0.01" name="final_balance_amount" class="form-control"
                                                        value="{{ old('final_balance_amount', $finalbalanceamt ?? 0) }}">
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-end gap-2 mt-3">
                                                @if(!empty($existingSummary))
                                                    <span class="text-muted align-self-center me-auto">Already saved for {{ \Carbon\Carbon::parse($selectedDate)->format('d M, Y') }}</span>
                                                @endif
                                                <button type="submit" class="btn btn-primary">
                                                    {{ !empty($existingSummary) ? 'Update Summary' : 'Save Summary' }}
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
 @php
// Use the fully qualified class name instead of 'use' statement
 $alreadyExists = \App\Models\CollectionSummary::where('current_date', $selectedDateYmd)->first();

 if (!$alreadyExists) {
     $summary = new \App\Models\CollectionSummary();
    $summary->report_type = 'daily';
     $summary->previous_date = $previousDateYmd;
     $summary->previous_total_paidamount = $previous_totalpaidamount;
     $summary->current_date = $selectedDateYmd;
     $summary->current_total_paidamount = $currentday_totalpaidamount;
     $summary->total_amount = $totalamt;
     $summary->total_loan = $totalLoansTodayCount;
     $summary->total_loanamount = $totalLoanAmountToday;
    $summary->expense_amount_currentdate = $totalExpenses;
    $summary->md_fund_in = $md_fund_in;
    $summary->md_fund_out = $md_fund_out;
    $summary->final_balance_amount = $finalbalanceamt;

    $summary->save();
}
@endphp

                        <!-- Summary Cards (Quick Stats) -->
                        <!-- <div class="row mt-4 no-print">
                                                                    <div class="col-md-4">
                                                                        <div class="card border-primary">
                                                                            <div class="card-body text-center">
                                                                                <h3 class="text-primary">{{ $emiCollections->count() }}</h3>
                                                                                <p class="text-muted mb-0">Total Collections Count</p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="card border-success">
                                                                            <div class="card-body text-center">
                                                                                <h3 class="text-success">
                                                                                    ₹{{ number_format($currentCollection ?? 0, 2) }}
                                                                                </h3>
                                                                                <p class="text-muted mb-0">Today's Collection</p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="card border-info">
                                                                            <div class="card-body text-center">
                                                                                <h3 class="text-info">
                                                                                    ₹{{ number_format($finalBalance ?? 0, 2) }}
                                                                                </h3>
                                                                                <p class="text-muted mb-0">Closing Balance</p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div> -->

                    @elseif(request()->isMethod('post'))
                        <!-- No Results Message -->
                        <div class="alert alert-warning text-center py-4">
                            <i class="ti ti-alert-circle" style="font-size: 48px;"></i>
                            <h4 class="mt-3">No Collections Found</h4>
                            @if(!empty($branchId) || !empty($routeId))
                                <p class="mb-0">No EMI collections were found for the selected branch{{ !empty($routeId) ? ', route,' : '' }} and date.</p>
                            @else
                                <p class="mb-0">No EMI collections were found for the selected date.</p>
                            @endif
                        </div>
                    @else
                        <!-- Initial State -->
                        <div class="alert alert-info text-center py-4">
                            <i class="ti ti-report-analytics" style="font-size: 48px;"></i>
                            <h4 class="mt-3">Daily EMI Collection Report</h4>
                            <p class="mb-0">Select a date and click "Generate Report" to view daily collections.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>


    <style>
        /* Custom styles for DataTables */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1rem;
        }

        .dataTables_wrapper .dataTables_paginate {
            margin-top: 1rem;
        }

        /* Custom export buttons */
        .dt-buttons .btn {
            border-radius: 4px;
            padding: 0.25rem 0.75rem;
        }

        /* Print styling */
        @media print {

            .page-header,
            .card-header,
            .btn,
            .dataTables_length,
            .dataTables_filter,
            .dataTables_paginate,
            .no-print,
            .dropdown,
            .alert-info,
            form {
                display: none !important;
            }

            table {
                width: 100% !important;
                border-collapse: collapse !important;
            }

            table th,
            table td {
                border: 1px solid #ddd !important;
                padding: 8px !important;
            }
        }
    </style>



    <script>
        $(document).ready(function () {
            // Function to load routes for a branch
            function loadRoutesForBranch(branchId, selectedRouteId = null) {
                const routeDropdown = $('#route_id');

                // Reset route dropdown
                routeDropdown.html('<option value="">Select Route</option>');

                if (branchId) {
                    // Fetch routes for the selected branch
                    $.ajax({
                        url: '{{ route('admin.report-daily.routes-by-branch', ['branchId' => '__BRANCH_ID__']) }}'.replace('__BRANCH_ID__', branchId),
                        type: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'Accept': 'application/json'
                        },
                        success: function(response) {
                            if (response.status && response.data.length > 0) {
                                response.data.forEach(function(route) {
                                    const option = $('<option></option>')
                                        .attr('value', route.id)
                                        .text(route.route_name);

                                    if (selectedRouteId && selectedRouteId == route.id) {
                                        option.prop('selected', true);
                                    }

                                    routeDropdown.append(option);
                                });
                            }
                        },
                        error: function(xhr) {
                            console.error('Error fetching routes:', xhr);
                        }
                    });
                }
            }

            // Handle branch change to populate routes
            $('#branch_id').on('change', function() {
                const branchId = $(this).val();
                loadRoutesForBranch(branchId);
            });

            // On page load, if a branch is already selected, load its routes
            const initialBranchId = $('#branch_id').val();
            const initialRouteId = $('#route_id').val();
            if (initialBranchId) {
                loadRoutesForBranch(initialBranchId, initialRouteId);
            }

            function toNumber(value) {
                if (value === null || value === undefined) return 0;
                var cleaned = String(value).replace(/,/g, '').trim();
                var num = parseFloat(cleaned);
                return Number.isFinite(num) ? num : 0;
            }

            function recalcSummary() {
                var previousPaid = toNumber($('input[name="previous_total_paidamount"]').val());
                var currentPaid = toNumber($('input[name="current_total_paidamount"]').val());
                var mdFundIn = toNumber($('input[name="md_fund_in"]').val());
                var mdFundOut = toNumber($('input[name="md_fund_out"]').val());
                var totalLoanAmount = toNumber($('input[name="total_loanamount"]').val());
                var expenseAmount = toNumber($('input[name="expense_amount_currentdate"]').val());

                var totalAmount = previousPaid + currentPaid + mdFundIn;
                var finalBalance = totalAmount - totalLoanAmount - expenseAmount - mdFundOut;

                $('input[name="total_amount"]').val(totalAmount.toFixed(2));
                $('input[name="final_balance_amount"]').val(finalBalance.toFixed(2));
            }

            // Recalculate when amounts change in the Collection Summary form
            $(document).on('input change', 'input[name="previous_total_paidamount"], input[name="current_total_paidamount"], input[name="total_loanamount"], input[name="expense_amount_currentdate"],input[name="md_fund_in"],input[name="md_fund_out"]', recalcSummary);
            recalcSummary();

            // Initialize DataTable if table exists
            if ($('#dailyEmiTable').length) {
                var table = $('#dailyEmiTable').DataTable({
                    dom: '<"row align-items-center"<"col-sm-12 col-md-6"lB><"col-sm-12 col-md-6"f>><"row"<"col-sm-12"tr>><"row align-items-center"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                    buttons: [
                        {
                            extend: 'excelHtml5',
                            text: '<i class="ti ti-file-spreadsheet"></i> Excel',
                            className: 'btn btn-default',
                            title: 'Daily_EMI_Collection_Report_{{ $selectedDate ?? date("Y-m-d") }}',
                            footer: true,
                            exportOptions: {
                                columns: ':visible',
                                format: {
                                    footer: function (data, index, node) {
                                        // If this is one of the merged columns (0-3), return empty
                                        if (index < 4) return '';
                                        return data;
                                    }
                                }
                            },
                            customize: function (xlsx) {
                                var sheet = xlsx.xl.worksheets['sheet1.xml'];
                                $('row c[r^="E"], row c[r^="F"]', sheet).attr('s', '2'); // Format currency columns
                            },
                            messageBottom: function () {
                                return '\nCollection Summary\n' +
                                    'Previous Total Paid Amount ({{ $previousDateLabel ?? "" }}): ₹{{ number_format($previous_totalpaidamount ?? 0, 2) }}\n' +
                                    'Current Day Total Paid Amount ({{ isset($selectedDate) ? \Carbon\Carbon::parse($selectedDate)->format("d-m-Y") : date("d-m-Y") }}): ₹{{ number_format($currentday_totalpaidamount ?? 0, 2) }}\n' +
                                    'Total Amount: ₹{{ number_format($totalamt ?? 0, 2) }}\n\n' +
                                    'Total Loan: {{ $totalLoansTodayCount ?? 0 }}\n' +
                                    'Total Loan Amount: ₹{{ number_format($totalLoanAmountToday ?? 0, 2) }}\n\n' +
                                    'Expenses Amount Current Day ({{ isset($selectedDate) ? \Carbon\Carbon::parse($selectedDate)->format("d-m-Y") : date("d-m-Y") }}): ₹{{ number_format($totalExpenses ?? 0, 2) }}\n\n' +
                                    'MD Fund In: ₹{{ number_format($md_fund_in ?? 0, 2) }}\n' +
                                    'MD Fund Out: ₹{{ number_format($md_fund_out ?? 0, 2) }}\n\n' +
                                    'Final Balance Amount: ₹{{ number_format($finalbalanceamt ?? 0, 2) }}';
                            }
                        },
                        {
                            extend: 'csvHtml5',
                            text: '<i class="ti ti-file-text"></i> CSV',
                            className: 'btn btn-default',
                            title: 'Daily_EMI_Collection_Report_{{ $selectedDate ?? date("Y-m-d") }}',
                            footer: true,
                            exportOptions: {
                                columns: ':visible',
                                format: {
                                    footer: function (data, index, node) {
                                        if (index < 4) return '';
                                        return data;
                                    }
                                }
                            },
                            messageBottom: function () {
                                return '\nCollection Summary\n' +
                                    'Previous Total Paid Amount ({{ $previousDateLabel ?? "" }}): ₹{{ number_format($previous_totalpaidamount ?? 0, 2) }}\n' +
                                    'Current Day Total Paid Amount ({{ isset($selectedDate) ? \Carbon\Carbon::parse($selectedDate)->format("d-m-Y") : date("d-m-Y") }}): ₹{{ number_format($currentday_totalpaidamount ?? 0, 2) }}\n' +
                                    'Total Amount: ₹{{ number_format($totalamt ?? 0, 2) }}\n\n' +
                                    'Total Loan: {{ $totalLoansTodayCount ?? 0 }}\n' +
                                    'Total Loan Amount: ₹{{ number_format($totalLoanAmountToday ?? 0, 2) }}\n\n' +
                                    'Expenses Amount Current Day ({{ isset($selectedDate) ? \Carbon\Carbon::parse($selectedDate)->format("d-m-Y") : date("d-m-Y") }}): ₹{{ number_format($totalExpenses ?? 0, 2) }}\n\n' +
                                    'MD Fund In: ₹{{ number_format($md_fund_in ?? 0, 2) }}\n' +
                                    'MD Fund Out: ₹{{ number_format($md_fund_out ?? 0, 2) }}\n\n' +
                                    'Final Balance Amount: ₹{{ number_format($finalbalanceamt ?? 0, 2) }}';
                            }
                        },
                        {
                            extend: 'pdfHtml5',
                            text: '<i class="ti ti-file-text"></i> PDF',
                            className: 'btn btn-default',
                            title: 'Daily EMI Collection Report - {{ $selectedDate ?? date("Y-m-d") }}',
                            footer: true,
                            exportOptions: {
                                columns: ':visible'
                            },
                            customize: function (doc) {
                                doc.content[1].table.widths =
                                    Array(doc.content[1].table.body[0].length).fill('*');
                                doc.styles.tableHeader.alignment = 'center';

                                // Bold the footer row and handle colspan
                                var rowCount = doc.content[1].table.body.length;
                                var footerRow = doc.content[1].table.body[rowCount - 1];

                                // Apply colspan to "Total Paid Amount" in PDF
                                // Index 0-4 should be merged
                                footerRow[0].colSpan = 5;
                                footerRow[0].alignment = 'right';
                                footerRow[0].bold = true;

                                // Clear content of cells 1-4 as they are now part of the colspan
                                for (var i = 1; i < 5; i++) {
                                    footerRow[i].text = '';
                                }

                                // Bold the total amount cell
                                footerRow[5].bold = true;

                                // Add Summary Table to bottom of PDF (last page)
                                var summaryTable = {
                                    table: {
                                        widths: ['*', '*'],
                                        body: [
                                            [{ text: 'Collection Summary', colSpan: 2, alignment: 'center', bold: true, fillColor: '#f8f9fa' }, {}],
                                            ['Previous Total Paid Amount ({{ $previousDateLabel ?? "" }})', { text: '₹{{ number_format($previous_totalpaidamount ?? 0, 2) }}', alignment: 'right' }],
                                            ['Current Day Total Paid Amount ({{ isset($selectedDate) ? \Carbon\Carbon::parse($selectedDate)->format("d-m-Y") : date("d-m-Y") }})', { text: '₹{{ number_format($currentday_totalpaidamount ?? 0, 2) }}', alignment: 'right' }],
                                            [{ text: 'Total Amount', bold: true }, { text: '₹{{ number_format($totalamt ?? 0, 2) }}', alignment: 'right', bold: true }],
                                            ['Total Loan', { text: '{{ $totalLoansTodayCount ?? 0 }}', alignment: 'right' }],
                                            ['Total Loan Amount', { text: '₹{{ number_format($totalLoanAmountToday ?? 0, 2) }}', alignment: 'right' }],
                                            [{ text: 'Expenses Amount Current Day', color: 'red' }, { text: '₹{{ number_format($totalExpenses ?? 0, 2) }}', alignment: 'right', color: 'red' }],
                                            ['MD Fund In', { text: '₹{{ number_format($md_fund_in ?? 0, 2) }}', alignment: 'right' }],
                                            ['MD Fund Out', { text: '₹{{ number_format($md_fund_out ?? 0, 2) }}', alignment: 'right' }],
                                            [{ text: 'Final Balance Amount', bold: true, fontSize: 12, fillColor: '#e9ecef' }, { text: '₹{{ number_format($finalbalanceamt ?? 0, 2) }}', alignment: 'right', bold: true, fontSize: 12, fillColor: '#e9ecef' }]
                                        ]
                                    },
                                    margin: [0, 20, 0, 0]
                                };
                                doc.content.push(summaryTable);
                            }
                        },
                        {
                            extend: 'print',
                            text: '<i class="ti ti-printer"></i> Print',
                            className: 'btn btn-default',
                            title: '',
                            footer: true,
                            exportOptions: {
                                columns: ':visible'
                            },
                            customize: function (win) {
                                var formatCurrency = function (val) {
                                    return '₹' + toNumber(val).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                };

                                var printPrevPaid = formatCurrency($('input[name="previous_total_paidamount"]').val());
                                var printCurrPaid = formatCurrency($('input[name="current_total_paidamount"]').val());
                                var printTotalAmt = formatCurrency($('input[name="total_amount"]').val());
                                var printTotalLoan = $('input[name="total_loan"]').val() || '0';
                                var printTotalLoanAmt = formatCurrency($('input[name="total_loanamount"]').val());
                                var printExpenses = formatCurrency($('input[name="expense_amount_currentdate"]').val());
                                var printMdFundIn = formatCurrency($('input[name="md_fund_in"]').val());
                                var printMdFundOut = formatCurrency($('input[name="md_fund_out"]').val());
                                var printFinalBalance = formatCurrency($('input[name="final_balance_amount"]').val());

                                $(win.document.body)
                                    .css('font-size', '10pt')
                                    .prepend(
                                        '<div class="text-center">' +
                                        '<h2>Daily EMI Collection Report</h2>' +
                                        '<h4>Date: {{ ($selectedDate ?? null) ? \Carbon\Carbon::parse($selectedDate)->format("d M, Y") : date("d M, Y") }}</h4>' +
                                        '</div><hr>'
                                    );

                                $(win.document.body).find('table')
                                    .addClass('compact')
                                    .css('font-size', 'inherit');

                                // Fix footer colspan in print view
                                $(win.document.body).find('table').each(function() {
                                    var $table = $(this);
                                    var $footer = $table.find('tfoot tr');
                                    if ($footer.length) {
                                        var $cells = $footer.find('td, th');
                                        if ($cells.length >= 2) {
                                            // Re-apply colspan and formatting
                                            // Assuming the total is in the last cell and everything before it should be merged
                                            var totalCell = $cells.last();
                                            var labelCell = $cells.first();

                                            labelCell.attr('colspan', 5)
                                                     .addClass('text-end')
                                                     .css('text-align', 'right')
                                                     .html('<strong>Total Paid Amount:</strong>');

                                            // Remove the intermediate cells
                                            $cells.slice(1, -1).remove();
                                        }
                                    }
                                });

                                // Append Summary Table to bottom of print view (last page)
                                $(win.document.body).append(
                                    '<hr>' +
                                    '<div class="row mt-4" style="page-break-inside: avoid; break-inside: avoid;">' +
                                    '<div class="col-6 offset-3">' +
                                    '<table class="table table-bordered">' +
                                    '<tr><th colspan="2" class="text-center" style="background-color: #f8f9fa;"><strong>Collection Summary</strong></th></tr>' +
                                    '<tr><td><strong>Previous Total Paid Amount ({{ $previousDateLabel ?? "" }}):</strong></td><td class="text-end">' + printPrevPaid + '</td></tr>' +
                                    '<tr><td><strong>Current Day Total Paid Amount ({{ isset($selectedDate) ? \Carbon\Carbon::parse($selectedDate)->format("d-m-Y") : date("d-m-Y") }}):</strong></td><td class="text-end">' + printCurrPaid + '</td></tr>' +
                                    '<tr class="table-info"><td><strong>Total Amount:</strong></td><td class="text-end"><strong>' + printTotalAmt + '</strong></td></tr>' +
                                    '<tr><td><strong>Total Loan:</strong></td><td class="text-end">' + printTotalLoan + '</td></tr>' +
                                    '<tr><td><strong>Total Loan Amount:</strong></td><td class="text-end">' + printTotalLoanAmt + '</td></tr>' +
                                    '<tr><td class="text-danger"><strong>Expenses Amount Current Day:</strong></td><td class="text-end text-danger">' + printExpenses + '</td></tr>' +
                                    '<tr><td><strong>MD Fund In:</strong></td><td class="text-end">' + printMdFundIn + '</td></tr>' +
                                    '<tr><td><strong>MD Fund Out:</strong></td><td class="text-end">' + printMdFundOut + '</td></tr>' +
                                    '<tr class="table-success"><td><strong style="font-size: 1.2em;">Final Balance Amount:</strong></td><td class="text-end"><strong style="font-size: 1.2em;">' + printFinalBalance + '</strong></td></tr>' +
                                    '</table>' +
                                    '</div>' +
                                    '</div>'
                                );
                            }
                        }
                    ],
                    lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                    pageLength: 10,
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Search records...",
                        lengthMenu: "Show _MENU_ entries",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        infoEmpty: "Showing 0 to 0 of 0 entries",
                        infoFiltered: "(filtered from _MAX_ total entries)",
                        paginate: {
                            first: "First",
                            last: "Last",
                            next: "Next",
                            previous: "Previous"
                        }
                    },
                    order: [[0, 'asc']],
                    columnDefs: [
                        {
                            targets: [6, 7], // Columns for Loan Amount and Paid Amount
                            render: function (data, type) {
                                if (type === 'sort' || type === 'type') {
                                    return data.replace(/[₹,]/g, '');
                                }
                                return data;
                            }
                        }
                    ],
                    footerCallback: function (row, data, start, end, display) {
                        var api = this.api();

                        // Remove formatting to get numerical data for summation
                        var intVal = function (i) {
                            if (typeof i === 'string') {
                                var clean = i.replace(/<[^>]*>/g, '').replace(/[₹,]/g, '').trim();
                                return clean * 1;
                            }
                            return typeof i === 'number' ? i : 0;
                        };

                        // Total over all filtered pages
                        var total = api
                            .column(7, { search: 'applied' })
                            .data()
                            .reduce(function (a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);

                        // Update footer
                        $(api.column(7).footer()).html(
                            '<strong class="text-primary">₹' +
                            total.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) +
                            '</strong>'
                        );
                    }
                });

                // Custom export buttons in dropdown
                $('.export-btn').on('click', function (e) {
                    e.preventDefault();
                    var type = $(this).data('type');

                    switch (type) {
                        case 'excel':
                            $('.buttons-excel').trigger('click');
                            break;
                        case 'csv':
                            $('.buttons-csv').trigger('click');
                            break;
                        case 'pdf':
                            $('.buttons-pdf').trigger('click');
                            break;
                        case 'print':
                            $('.buttons-print').trigger('click');
                            break;
                    }
                });
            }
        });
    </script>
@endsection
