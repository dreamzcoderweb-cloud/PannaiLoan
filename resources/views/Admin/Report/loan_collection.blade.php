@extends('site.layouts.app')

@section('title', 'Loan Collection Report')

@section('content')

    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Loan Collection Report</h3>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <!-- Search Form -->
                    <form id="loanReportForm" action="{{ route('admin.report-loan.filter') }}" method="GET" class="mb-4">
                        <div class="row align-items-end">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="fromDate">From Date <span class="text-danger">*</span></label>
                                    <input type="date" name="from_date" id="fromDate" class="form-control"
                                        value="{{ $from_date ?? '' }}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="toDate">To Date <span class="text-danger">*</span></label>
                                    <input type="date" name="to_date" id="toDate" class="form-control"
                                        value="{{ $to_date ?? '' }}" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <button type="submit" id="generateBtn" class="btn btn-primary">
                                        <i class="ti ti-search"></i> Generate Report
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4 text-end" id="dateRangeLabel">
                                @if(isset($from_date) && isset($to_date) && $from_date != '' && $to_date != '')
                                    <div class="alert alert-info mb-0">
                                        <i class="ti ti-calendar"></i>
                                        Report from:
                                        <strong>{{ \Carbon\Carbon::parse($from_date)->format('d M, Y') }}</strong> to
                                        <strong>{{ \Carbon\Carbon::parse($to_date)->format('d M, Y') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </form>

                    <div id="reportResult">
                        @include('Admin.Report.partials.loan_table')
                    </div>
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

        /* Print styling */
        @media print {

            .page-header,
            .btn,
            .alert-info,
            form,
            .dropdown {
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
            // Function to initialize DataTable
            function initializeDataTable() {
                if ($('#loanCollectionTable').length) {
                    var fromDate = $('#fromDate').val();
                    var toDate = $('#toDate').val();
                    var titleDate = (fromDate && toDate) ? fromDate + '_to_' + toDate : 'Report';

                    var table = $('#loanCollectionTable').DataTable({
                        dom: '<"row align-items-center"<"col-sm-12 col-md-6"lB><"col-sm-12 col-md-6"f>><"row"<"col-sm-12"tr>><"row align-items-center"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                        buttons: [
                            {
                                extend: 'excelHtml5',
                                text: 'Excel',
                                className: 'btn btn-default d-none',
                                title: 'Loan_Collection_Report_' + titleDate,
                                footer: true,
                                exportOptions: {
                                    columns: ':visible',
                                    footer: true,
                                    format: {
                                        footer: function (data, index, node) {
                                            if (index < 3) return '';
                                            // Strip HTML tags and trim
                                            return data ? data.replace(/<[^>]*>?/gm, '').trim() : '';
                                        }
                                    }
                                }
                            },
                            {
                                extend: 'csvHtml5',
                                text: 'CSV',
                                className: 'btn btn-default d-none',
                                title: 'Loan_Collection_Report_' + titleDate,
                                footer: true,
                                exportOptions: {
                                    columns: ':visible',
                                    footer: true,
                                    format: {
                                        footer: function (data, index, node) {
                                            if (index < 3) return '';
                                            return data ? data.replace(/<[^>]*>?/gm, '').trim() : '';
                                        }
                                    }
                                }
                            },
                            {
                                extend: 'pdfHtml5',
                                text: 'PDF',
                                className: 'btn btn-default d-none',
                                title: 'Loan Collection Report (' + titleDate.replace('_to_', ' to ') + ')',
                                footer: true,
                                exportOptions: { columns: ':visible', footer: true },
                                customize: function (doc) {
                                    doc.content[1].table.widths = Array(doc.content[1].table.body[0].length).fill('*');
                                    doc.styles.tableFooter.alignment = 'right';

                                    // Bold the footer row and handle colspan
                                    var rowCount = doc.content[1].table.body.length;
                                    var footerRow = doc.content[1].table.body[rowCount - 1];
                                    
                                    // Apply colspan to "Total Loan Amount" in PDF
                                    // Index 0-3 should be merged
                                    footerRow[0].colSpan = 4;
                                    footerRow[0].alignment = 'right';
                                    footerRow[0].bold = true;
                                    
                                    // Clear content of cells 1-3 as they are now part of the colspan
                                    for (var i = 1; i < 4; i++) {
                                        footerRow[i].text = '';
                                    }
                                    
                                    // Bold the total amount cell
                                    footerRow[4].bold = true;
                                }
                            },
                            {
                                extend: 'print',
                                text: 'Print',
                                className: 'btn btn-default d-none',
                                title: '',
                                footer: true,
                                exportOptions: { columns: ':visible', footer: true },
                                customize: function (win) {
                                    var from = $('#fromDate').val();
                                    var to = $('#toDate').val();
                                    $(win.document.body).prepend(
                                        '<div class="text-center"><h2>Loan Collection Report</h2>' +
                                        '<h4>From: ' + from + ' To: ' + to + '</h4></div><hr>'
                                    );

                                    $(win.document.body).find('table')
                                        .addClass('compact')
                                        .css('font-size', 'inherit');

                                    // Fix footer colspan in print view
                                    $(win.document.body).find('table').each(function () {
                                        var $table = $(this);
                                        var $footer = $table.find('tfoot tr');
                                        if ($footer.length) {
                                            var $cells = $footer.find('td, th');
                                            if ($cells.length >= 2) {
                                                var labelCell = $cells.first();
                                                labelCell.attr('colspan', 4)
                                                    .addClass('text-end')
                                                    .css('text-align', 'right')
                                                    .html('<strong>Total Loan Amount:</strong>');

                                                // Remove the intermediate cells (indices 1, 2, 3)
                                                // Since we have colspan=4, these cells are redundant
                                                $cells.slice(1, 4).remove();
                                            }
                                        }
                                    });
                                }
                            }
                        ],
                        pageLength: 25,
                        order: [[0, 'asc']],
                        footerCallback: function (row, data, start, end, display) {
                            var api = this.api();
                            var intVal = function (i) {
                                return typeof i === 'string' ?
                                    i.replace(/[\₹,]/g, '') * 1 :
                                    typeof i === 'number' ? i : 0;
                            };

                            var total = api
                                .column(4, { search: 'applied' })
                                .data()
                                .reduce(function (a, b) {
                                    return intVal(a) + intVal(b);
                                }, 0);

                            $(api.column(4).footer()).html(
                                '<strong>₹' + total.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '</strong>'
                            );
                        }
                    });
                }
            }

            // Initialize on page load
            initializeDataTable();

            // AJAX Form Submission
            $('#loanReportForm').on('submit', function (e) {
                e.preventDefault();
                var form = $(this);
                var url = form.attr('action');
                var data = form.serialize();

                $.ajax({
                    type: 'GET',
                    url: url,
                    data: data,
                    beforeSend: function () {
                        $('#generateBtn').prop('disabled', true).html('<i class="ti ti-loader-2 spin"></i> Generating...');
                        $('#reportResult').css('opacity', '0.5');
                    },
                    success: function (response) {
                        $('#reportResult').html(response);
                        $('#reportResult').css('opacity', '1');
                        initializeDataTable();

                        // Update date range label
                        var from = $('#fromDate').val();
                        var to = $('#toDate').val();
                        if (from && to) {
                            var fromDateObj = new Date(from);
                            var toDateObj = new Date(to);
                            var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                            var fromFormatted = fromDateObj.getDate() + ' ' + months[fromDateObj.getMonth()] + ', ' + fromDateObj.getFullYear();
                            var toFormatted = toDateObj.getDate() + ' ' + months[toDateObj.getMonth()] + ', ' + toDateObj.getFullYear();

                            $('#dateRangeLabel').html(
                                '<div class="alert alert-info mb-0">' +
                                '<i class="ti ti-calendar"></i> Report from: ' +
                                '<strong>' + fromFormatted + '</strong> to <strong>' + toFormatted + '</strong>' +
                                '</div>'
                            );
                        }

                        // Update URL for persistence on reload
                        var newUrl = window.location.pathname + '?' + data;
                        window.history.pushState({ path: newUrl }, '', newUrl);
                    },
                    error: function () {
                        alert('Something went wrong. Please try again.');
                        $('#reportResult').css('opacity', '1');
                    },
                    complete: function () {
                        $('#generateBtn').prop('disabled', false).html('<i class="ti ti-search"></i> Generate Report');
                    }
                });
            });

            // Handle export buttons
            $(document).on('click', '.export-btn', function (e) {
                e.preventDefault();
                var type = $(this).data('type');
                $('.buttons-' + type).trigger('click');
            });
        });
    </script>
@endsection