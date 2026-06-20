@extends('site.layouts.app')

@section('title', 'Total Collection Report')

@section('content')

    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Total Collection Report</h3>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <!-- Search Form -->
                    <form action="{{ route('admin.report-total.filter') }}" method="POST" class="mb-4">
                        @csrf
                        <div class="row align-items-end">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="fromdate">From Date <span class="text-danger">*</span></label>
                                    <input type="date" name="from_date" id="from_date" class="form-control"
                                        value="{{ old('from_date', $fromDate ?? '') }}">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="todate">To Date <span class="text-danger">*</span></label>
                                    <input type="date" name="to_date" id="to_date" class="form-control"
                                        value="{{ old('to_date', $toDate ?? '') }}">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-search"></i> Generate Report
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                @php
                                    // Ensure a date variable exists for older codepaths that expect $selectedDate
                                    $selectedDate = $selectedDate ?? ($toDate ?? null);
                                @endphp

                                @if(isset($fromDate) && isset($toDate))
                                    <div class="alert alert-info mb-0">
                                        <i class="ti ti-calendar"></i>
                                        Showing report for:
                                        <strong>{{ \Carbon\Carbon::parse($fromDate)->format('d M, Y') }} -
                                            {{ \Carbon\Carbon::parse($toDate)->format('d M, Y') }}</strong>
                                    </div>
                                @elseif(isset($selectedDate))
                                    <div class="alert alert-info mb-0">
                                        <i class="ti ti-calendar"></i>
                                        Showing report for:
                                        <strong>{{ \Carbon\Carbon::parse($selectedDate)->format('d M, Y') }}</strong>
                                    </div>
                                @endif
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
                                        <th>Customer Name</th>
                                        <th>Phone Number</th>
                                        <th>Loan Type</th>
                                        <th>Loan Amount</th>
                                        <th>Paid Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalPaidAmount = 0;
                                        $totalAmt = $CtotalPaidAmount + $collections['md_fund_in'] - $collections['md_fund_out'];
                                        $finalbalanceamt = $totalAmt - $totalLoanAmount - $totalExpenseAmount;
                                    @endphp
                                    @foreach($emiCollections as $index => $collection)
                                        @php
                                            $paidForRange = $collection->details->sum('paid_amount');
                                            $totalPaidAmount += $paidForRange;

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
                                            <td data-order="{{ $paidForRange }}">
                                                <strong class="text-success">
                                                    ₹{{ number_format($paidForRange, 2) }}
                                                </strong>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="5" class="text-end">
                                            <strong>Total Paid Amount:</strong>
                                        </td>
                                        <td id="footerTotalPaidAmount">
                                            <strong class="text-primary">
                                                ₹{{ number_format($totalPaidAmount, 2) }}
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
                                            <input type="hidden" name="report_type" value="total">
                                            <input type="hidden" name="from_date" value="{{ $fromDate }}">
                                            <input type="hidden" name="to_date" value="{{ $toDate }}">
                                            <div class="row g-2">

                                                {{-- Current Date --}}
                                                <div class="col-md-12">
                                                    <label class="form-label mb-1">Selected Date Range</label>
                                                    <input type="text" class="form-control" readonly
                                                        value="{{ \Carbon\Carbon::parse($fromDate)->format('d-m-Y') }} - {{ \Carbon\Carbon::parse($toDate)->format('d-m-Y') }}">
                                                    {{-- Hidden Inputs --}}

                                                </div>

                                                {{-- Current Total Paid Amount --}}
                                                <div class="col-md-6">
                                                    <label class="form-label mb-1">
                                                        Current Day Total Paid Amount
                                                        ({{ \Carbon\Carbon::parse($fromDate)->format('d-m-Y') }}
                                                        -
                                                        {{ \Carbon\Carbon::parse($toDate)->format('d-m-Y') }})
                                                    </label>

                                                    <input type="number" step="0.01" name="current_total_paidamount"
                                                        class="form-control"
                                                        value="{{ old('current_total_paidamount', $CtotalPaidAmount) }}"
                                                        required>
                                                </div>

                                                {{-- MD Fund In --}}
                                                <div class="col-md-6">
                                                    <label class="form-label mb-1">MD Fund In</label>
                                                    <input type="number" step="0.01" name="md_fund_in" class="form-control"
                                                        value="{{ old('md_fund_in', $collections->md_fund_in ?? '') }}" required>
                                                </div>

                                                {{-- MD Fund Out --}}
                                                <div class="col-md-6">
                                                    <label class="form-label mb-1">MD Fund Out</label>
                                                    <input type="number" step="0.01" name="md_fund_out" class="form-control"
                                                        value="{{ old('md_fund_out', $collections->md_fund_out ?? '') }}" required>
                                                </div>

                                                {{-- Total Amount --}}
                                                <div class="col-md-6">
                                                    <label class="form-label mb-1">Total Amount</label>
                                                    <input type="number" step="0.01" name="total_amount" class="form-control"
                                                        readonly value="{{ old('total_amount',$totalAmt ) }}" required>
                                                </div>

                                                {{-- Total Loan --}}
                                                <div class="col-md-6">
                                                    <label class="form-label mb-1">Total Loan (Count)</label>
                                                    <input type="number" name="total_loan" class="form-control" value="{{ old('total_loan', $totalLoanCount) }}"
                                                        required>
                                                </div>

                                                {{-- Total Loan Amount --}}
                                                <div class="col-md-6">
                                                    <label class="form-label mb-1">Total Loan Amount</label>
                                                    <input type="number" step="0.01" name="total_loanamount"
                                                        class="form-control" value="{{ old('total_loanamount', $totalLoanAmount) }}" required>
                                                </div>

                                                {{-- Expense --}}
                                                <div class="col-md-6">
                                                    <label class="form-label mb-1">
                                                        Expense Amount
                                                        ({{ \Carbon\Carbon::parse($fromDate)->format('d-m-Y') }}
                                                        -
                                                        {{ \Carbon\Carbon::parse($toDate)->format('d-m-Y') }})
                                                    </label>

                                                    <input type="number" step="0.01" name="expense_amount_currentdate"
                                                        class="form-control"
                                                        value="{{ old('expense_amount_currentdate', $totalExpenseAmount ?? 0) }}"
                                                        required>
                                                </div>

                                                {{-- Final Balance --}}
                                                <div class="col-md-12">
                                                    <label class="form-label mb-1">Final Balance Amount</label>
                                                    <input type="number" step="0.01" name="final_balance_amount"
                                                        class="form-control" value="{{ old('final_balance_amount', $finalbalanceamt ?? 0) }}" required>
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-end gap-2 mt-3">

                                                <span class="text-muted align-self-center me-auto">
                                                    Already saved for
                                                    {{ \Carbon\Carbon::parse($fromDate)->format('d M, Y') }}
                                                    -
                                                    {{ \Carbon\Carbon::parse($toDate)->format('d M, Y') }}
                                                </span>


                                                <button type="submit" class="btn btn-primary">
                                                    Save Summary
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>


                    @elseif(request()->isMethod('post'))
                        <!-- No Results Message -->
                        <div class="alert alert-warning text-center py-4">
                            <i class="ti ti-alert-circle" style="font-size: 48px;"></i>
                            <h4 class="mt-3">No Collections Found</h4>
                            <p class="mb-0">No EMI collections were found for the selected date range.</p>
                        </div>
                    @else
                        <!-- Initial State -->
                        <div class="alert alert-info text-center py-4">
                            <i class="ti ti-report-analytics" style="font-size: 48px;"></i>
                            <h4 class="mt-3">Total EMI Collection Report</h4>
                            <p class="mb-0">Select date range and click "Generate Report" to view collections.</p>
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
            // Initialize DataTable if table exists
            if ($('#dailyEmiTable').length) {
                var table = $('#dailyEmiTable').DataTable({
                    dom: '<"row align-items-center"<"col-sm-12 col-md-6"lB><"col-sm-12 col-md-6"f>><"row"<"col-sm-12"tr>><"row align-items-center"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                    buttons: [
                        {
                            extend: 'excelHtml5',
                            text: '<i class="ti ti-file-spreadsheet"></i> Excel',
                            className: 'btn btn-default',
                            title: 'Total_EMI_Collection_Report_{{ isset($fromDate) && isset($toDate) ? $fromDate . "_to_" . $toDate : date("Y-m-d") }}',
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
                            customize: function (xlsx) {
                                var sheet = xlsx.xl.worksheets['sheet1.xml'];
                                $('row c[r^="E"], row c[r^="F"]').attr('s', '2');
                            }
                        },
                        {
                            extend: 'csvHtml5',
                            text: '<i class="ti ti-file-text"></i> CSV',
                            className: 'btn btn-default',
                            title: 'Total_EMI_Collection_Report_{{ isset($fromDate) && isset($toDate) ? $fromDate . "_to_" . $toDate : date("Y-m-d") }}',
                            footer: true,
                            exportOptions: {
                                columns: ':visible',
                                format: {
                                    footer: function (data, index, node) {
                                        if (index < 4) return '';
                                        return data;
                                    }
                                }
                            }
                        },
                        {
                            extend: 'pdfHtml5',
                            text: '<i class="ti ti-file-text"></i> PDF',
                            className: 'btn btn-default',
                            title: 'Total EMI Collection Report - {{ isset($fromDate) && isset($toDate) ? $fromDate . " to " . $toDate : date("Y-m-d") }}',
                            footer: true,
                            exportOptions: {
                                columns: ':visible'
                            },
                            customize: function (doc) {
                                doc.content[1].table.widths =
                                    Array(doc.content[1].table.body[0].length).fill('*');
                                doc.styles.tableHeader.alignment = 'center';

                                var rowCount = doc.content[1].table.body.length;
                                var footerRow = doc.content[1].table.body[rowCount - 1];

                                footerRow[0].colSpan = 5;
                                footerRow[0].alignment = 'right';
                                footerRow[0].bold = true;

                                for (var i = 1; i < 5; i++) {
                                    footerRow[i].text = '';
                                }

                                footerRow[5].bold = true;
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
                                $(win.document.body)
                                    .css('font-size', '10pt')
                                    .prepend(
                                        '<div class="text-center">' +
                                        '<h2>Total EMI Collection Report</h2>' +
                                        '<h4>Date: {{ isset($fromDate) && isset($toDate) ? \Carbon\Carbon::parse($fromDate)->format("d M, Y") . " - " . \Carbon\Carbon::parse($toDate)->format("d M, Y") : date("d M, Y") }}</h4>' +
                                        '</div><hr>'
                                    );

                                $(win.document.body).find('table')
                                    .addClass('compact')
                                    .css('font-size', 'inherit');

                                $(win.document.body).find('table').each(function () {
                                    var $table = $(this);
                                    var $footer = $table.find('tfoot tr');
                                    if ($footer.length) {
                                        var $cells = $footer.find('td, th');
                                        if ($cells.length >= 2) {
                                            var totalCell = $cells.last();
                                            var labelCell = $cells.first();

                                            labelCell.attr('colspan', 5)
                                                .addClass('text-end')
                                                .css('text-align', 'right')
                                                .html('<strong>Total Paid Amount:</strong>');

                                            $cells.slice(1, -1).remove();
                                        }
                                    }
                                });
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
                            targets: [4, 5],
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

                        var intVal = function (i) {
                            if (typeof i === 'string') {
                                var clean = i.replace(/<[^>]*>/g, '').replace(/[₹,]/g, '').trim();
                                return clean * 1;
                            }
                            return typeof i === 'number' ? i : 0;
                        };

                        var total = api
                            .column(5, { search: 'applied' })
                            .data()
                            .reduce(function (a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);

                        $(api.column(5).footer()).html(
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
