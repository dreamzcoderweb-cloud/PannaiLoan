<!-- Results Section -->
@if(isset($loanAssignments) && $loanAssignments->isNotEmpty())
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
        <table class="table table-bordered table-hover" id="loanCollectionTable">
            <thead class="table-primary">
                <tr>
                    <th>S.No</th>
                    <th>Customer Name</th>
                    <th>Employee Name</th>
                    <th>Loan Type</th>
                    <th>Loan Amount</th>
                    <th>Branch</th>
                    <th>Route</th>
                </tr>
            </thead>
            <tbody>
                @php $totalLoanAmount = 0; @endphp
                @foreach($loanAssignments as $index => $loan)

                    @php
                        $finalamt = 0;

                        if ($loan->collection_type_id == 2) {
                            $finalamt = $loan->total_distribution ?? 0;
                        } elseif ($loan->collection_type_id == 3) {
                            $finalamt = $loan->loan_amount ?? 0;
                        }
                        $totalLoanAmount += $finalamt;
                    @endphp

                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $loan->client_name->name ?? 'N/A' }}</strong>
                            <br>
                            <small class="text-muted">{{ $loan->phone ?? 'N/A' }}</small>
                        </td>
                        <td>{{ $loan->employee->name ?? 'N/A' }}</td>
                        <td>{{ $loan->loan->loan_name ?? 'N/A' }}</td>
                        <td data-order="{{ $finalamt }}" class="text-end">
                            ₹{{ number_format($finalamt, 2) }}
                        </td>
                        <td>{{ $loan->branches->branch_name ?? 'N/A' }}</td>
                        <td>{{ $loan->routes->route_name ?? 'N/A' }}</td>
                    </tr>

                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-end"><strong>Total Loan Amount:</strong></td>
                    <td class="text-start"><strong>₹{{ number_format($totalLoanAmount, 2) }}</strong></td>
                    <td></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
@elseif(isset($from_date) && isset($to_date))
    <!-- No Results Message -->
    <div class="alert alert-warning text-center py-4">
        <i class="ti ti-alert-circle" style="font-size: 48px;"></i>
        <h4 class="mt-3">No Records Found</h4>
        <p class="mb-0">No loan assignments were found for the selected date range.</p>
    </div>
@else
    <!-- Initial State -->
    <div class="alert alert-info text-center py-4">
        <i class="ti ti-report-analytics" style="font-size: 48px;"></i>
        <h4 class="mt-3">Loan Collection Report</h4>
        <p class="mb-0">Select a date range and click "Generate Report" to view loan assignments.</p>
    </div>
@endif