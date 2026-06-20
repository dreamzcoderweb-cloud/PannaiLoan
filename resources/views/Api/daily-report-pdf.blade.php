<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Report - {{ $date_formatted }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Helvetica Neue", Arial, sans-serif;
            color: #1f2937;
            background: #eef2f7;
            line-height: 1.45;
            font-size: 12px;
            padding: 18px;
        }

        .container {
            max-width: 980px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #d9e1ec;
            border-radius: 10px;
            padding: 28px;
        }

        .header {
            border: 1px solid #d6deea;
            border-left: 6px solid #0f4c81;
            background: #f7faff;
            border-radius: 8px;
            padding: 16px 18px;
            margin-bottom: 20px;
        }

        .header-top {
            display: table;
            width: 100%;
            margin-bottom: 6px;
        }

        .header-title,
        .header-date {
            display: table-cell;
            vertical-align: middle;
        }

        .header-title {
            width: 70%;
            font-size: 24px;
            font-weight: 700;
            color: #0f2d4d;
            letter-spacing: 0.3px;
        }

        .header-date {
            width: 30%;
            text-align: right;
            color: #415a77;
            font-weight: 600;
            font-size: 12px;
        }

        .header-subtitle {
            color: #5b6b80;
            font-size: 12px;
        }

        .panel-title {
            font-size: 12px;
            font-weight: 700;
            color: #0f2d4d;
            text-transform: uppercase;
            letter-spacing: 0.9px;
            margin-bottom: 10px;
        }

        .employee-panel {
            border: 1px solid #dde4ef;
            border-radius: 8px;
            padding: 14px;
            margin-bottom: 18px;
            background: #ffffff;
        }

        .employee-info {
            display: table;
            width: 100%;
        }

        .info-item {
            display: table-cell;
            width: 25%;
            padding-right: 10px;
            vertical-align: top;
            font-size: 12px;
        }

        .info-item label {
            display: block;
            color: #6b7280;
            margin-bottom: 4px;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .info-item span {
            color: #1f2937;
            font-weight: 600;
        }

        .summary-section {
            border: 1px solid #dde4ef;
            border-radius: 8px;
            padding: 14px;
            margin-bottom: 20px;
            background: #fcfdff;
        }

        .summary-grid {
            display: table;
            width: 100%;
            table-layout: fixed;
            border-spacing: 8px 0;
        }

        .summary-item {
            display: table-cell;
            border: 1px solid #e1e7f0;
            border-radius: 7px;
            padding: 12px;
            background: #ffffff;
            text-align: center;
        }

        .summary-item .label {
            font-size: 10px;
            color: #667085;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 5px;
            font-weight: 700;
        }

        .summary-item .value {
            font-size: 18px;
            font-weight: 700;
            color: #0f2d4d;
        }

        .summary-item.amount .value {
            color: #0c7a43;
        }

        .collections-section {
            margin-top: 8px;
        }

        .collections-section h3 {
            font-size: 13px;
            color: #0f2d4d;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 12px;
        }

        .collection-card {
            border: 1px solid #dbe4ef;
            border-radius: 8px;
            margin-bottom: 14px;
            overflow: hidden;
            page-break-inside: avoid;
            background: #ffffff;
        }

        .card-top {
            background: #f7faff;
            border-bottom: 1px solid #dbe4ef;
            padding: 10px 12px;
            font-size: 11px;
            font-weight: 700;
            color: #234f78;
        }

        .card-header {
            padding: 10px 12px;
            display: table;
            width: 100%;
            table-layout: fixed;
            border-bottom: 1px solid #ecf1f7;
        }

        .card-header-item {
            display: table-cell;
            padding-right: 10px;
            vertical-align: top;
            font-size: 12px;
        }

        .card-header-item label {
            display: block;
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700;
        }

        .card-header-item span {
            color: #111827;
            font-weight: 600;
        }

        .collection-type-badge {
            display: inline-block;
            background: #e7f0ff;
            color: #1f4f96;
            border: 1px solid #c7d8f3;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 700;
            padding: 3px 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table-wrap {
            padding: 10px 12px 12px;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        .details-table thead tr {
            background: #173a5e;
            color: #ffffff;
        }

        .details-table th {
            padding: 8px 6px;
            text-align: left;
            font-weight: 700;
            border: 1px solid #264d73;
            white-space: nowrap;
        }

        .details-table td {
            padding: 7px 6px;
            border: 1px solid #e6edf5;
            color: #1f2937;
        }

        .details-table tbody tr:nth-child(odd) {
            background: #fbfdff;
        }

        .details-table tbody tr:nth-child(even) {
            background: #f4f8fc;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .status-badge {
            display: inline-block;
            min-width: 62px;
            padding: 3px 7px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .status-paid {
            background: #def7e8;
            color: #166534;
            border: 1px solid #b8e8cc;
        }

        .status-pending {
            background: #fff5da;
            color: #8a5b00;
            border: 1px solid #f3e1aa;
        }

        .status-overdue {
            background: #ffe4e6;
            color: #9f1239;
            border: 1px solid #f6bcc7;
        }

        .footer {
            margin-top: 22px;
            padding-top: 12px;
            border-top: 1px solid #d7e0ec;
            color: #667085;
            font-size: 11px;
            text-align: center;
        }

        .footer p {
            margin: 4px 0;
        }

        .no-data {
            text-align: center;
            padding: 24px;
            border: 1px dashed #c8d4e4;
            border-radius: 8px;
            color: #5f6f85;
            background: #f8fbff;
        }

        @media print {
            body {
                padding: 0;
                background: #ffffff;
            }

            .container {
                margin: 0;
                max-width: 100%;
                border: none;
                border-radius: 0;
                padding: 12px;
            }

            .collection-card {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-top">
                <div class="header-title">Daily Collection Report</div>
                <div class="header-date">{{ $date_formatted }}</div>
            </div>
            <div class="header-subtitle">Pannai Loan Management System</div>
        </div>

        <div class="employee-panel">
            <h3 class="panel-title">Employee Information</h3>
            <div class="employee-info">
                <div class="info-item">
                    <label>Employee Name</label>
                    <span>{{ $employee->name }}</span>
                </div>
                <div class="info-item">
                    <label>Phone</label>
                    <span>{{ $employee->phone }}</span>
                </div>
                <div class="info-item">
                    <label>Branch</label>
                    <span>{{ $employee->branch->branch_name }}</span>
                </div>
                <div class="info-item">
                    <label>Route</label>
                    <span>{{ $employee->route->route_name }}</span>
                </div>
            </div>
        </div>

        <div class="summary-section">
            <h3 class="panel-title">Summary</h3>
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="label">Total Collections</div>
                    <div class="value">{{ $total_collections }}</div>
                </div>
                <div class="summary-item amount">
                    <div class="label">Amount Collected</div>
                    <div class="value">{{ number_format($total_paid_amount, 2) }}</div>
                </div>
                <div class="summary-item">
                    <div class="label">Total Amount</div>
                    <div class="value">{{ number_format($total_paid_amount, 2) }}</div>
                </div>
            </div>
        </div>

        <div class="collections-section">
            <h3>Collections Details</h3>

            @if(count($collections) > 0)
                @foreach($collections as $collection)
                    <div class="collection-card">
                        <div class="card-top">
                            Customer: {{ $collection['customer']['name'] ?? 'N/A' }}
                        </div>

                        <div class="card-header">
                            <div class="card-header-item">
                                <label>Customer Phone</label>
                                <span>{{ $collection['customer']['phone'] ?? 'N/A' }}</span>
                            </div>
                            <div class="card-header-item">
                                <label>Loan Type</label>
                                <span>{{ $collection['loan_type'] ?? 'N/A' }}</span>
                            </div>
                            <div class="card-header-item">
                                <label>Collection Type</label>
                                <span class="collection-type-badge">{{ $collection['collection_type'] ?? 'N/A' }}</span>
                            </div>
                            <div class="card-header-item">
                                <label>Loan Amount</label>
                                <span>{{ number_format($collection['loan_amount'] ?? 0, 2) }}</span>
                            </div>
                            <div class="card-header-item">
                                <label>Total Payable Amount</label>
                                <span>{{ number_format($collection['total_payable_amount'] ?? 0, 2) }}</span>
                            </div>
                        </div>

                        <div class="table-wrap">
                            <table class="details-table">
                                <thead>
                                    <tr>
                                        <th>Installment No</th>
                                        <th>Due Date</th>
                                        <th>EMI Amount</th>
                                        <th>Paid Amount</th>
                                        <th>Remaining</th>
                                        <th>Status</th>
                                        <th>Discount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($collection['details'] as $detail)
                                        <tr>
                                            <td class="text-center">{{ $detail['installment_no'] }}</td>
                                            <td>{{ \Carbon\Carbon::parse($detail['due_date'])->format('d-M-Y') }}</td>
                                            <td class="text-right">{{ number_format($detail['emi_amount'], 2) }}</td>
                                            <td class="text-right">{{ number_format($detail['paid_amount'], 2) }}</td>
                                            <td class="text-right">{{ number_format($detail['remaining_payable_amount'], 2) }}</td>
                                            <td class="text-center">
                                                <span class="status-badge status-{{ strtolower($detail['status']) }}">
                                                    {{ $detail['status'] }}
                                                </span>
                                            </td>
                                            <td class="text-right">{{ number_format($detail['discount'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="no-data">
                    <p>No collections found for the selected date.</p>
                </div>
            @endif
        </div>

        <div class="footer">
            <p><strong>Generated on:</strong> {{ now()->format('d-M-Y H:i:s') }}</p>
            <p>This is a confidential document. &copy; {{ now()->year }} Pannai Loan Management System</p>
        </div>
    </div>
</body>
</html>
