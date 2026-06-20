<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EMI Paid History</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #1f2937;
            background: #f5f7fb;
            padding: 14px;
        }
        .container {
            max-width: 980px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #d9e1ea;
            border-radius: 8px;
            padding: 20px;
        }
        .header {
            border: 1px solid #dbe4ef;
            border-left: 5px solid #1f4f82;
            border-radius: 7px;
            background: #f8fbff;
            padding: 12px 14px;
            margin-bottom: 14px;
        }
        .header h1 {
            font-size: 22px;
            color: #123356;
            margin-bottom: 4px;
        }
        .muted { color: #5b677a; }
        .grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px;
            margin-bottom: 12px;
        }
        .card {
            border: 1px solid #dde5ef;
            border-radius: 7px;
            padding: 10px;
            background: #ffffff;
            vertical-align: top;
        }
        .card .label {
            font-size: 10px;
            color: #667085;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-bottom: 4px;
            font-weight: 700;
        }
        .card .value {
            font-size: 14px;
            font-weight: 700;
            color: #0f2d4d;
        }
        .section-title {
            margin: 8px 0;
            font-size: 12px;
            font-weight: 700;
            color: #123356;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        th {
            background: #173a5e;
            color: #ffffff;
            padding: 8px 6px;
            border: 1px solid #274d73;
            text-align: left;
            white-space: nowrap;
        }
        td {
            padding: 7px 6px;
            border: 1px solid #e4ebf3;
        }
        tbody tr:nth-child(odd) { background: #f9fbff; }
        tbody tr:nth-child(even) { background: #f2f7fd; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .status-paid { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .status-pending { background: #fff7db; color: #8a5b00; border: 1px solid #f6e8b1; }
        .status-overdue { background: #ffe4e6; color: #9f1239; border: 1px solid #fecdd3; }
        .footer {
            margin-top: 14px;
            border-top: 1px solid #dbe3ed;
            padding-top: 8px;
            text-align: center;
            color: #64748b;
            font-size: 11px;
        }
        .no-data {
            border: 1px dashed #cad4e1;
            border-radius: 7px;
            padding: 16px;
            text-align: center;
            color: #64748b;
            background: #f8fbff;
        }
        @media print {
            body { background: #fff; padding: 0; }
            .container { border: none; border-radius: 0; max-width: 100%; padding: 10px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Customer Paid History</h1>
            <div class="muted">Collection Type: {{ $collection_type_label }}</div>
            <div class="muted">Generated on: {{ $generated_at }}</div>
        </div>

        <table class="grid">
            <tr>
                <td class="card">
                    <div class="label">Customer Name</div>
                    <div class="value">{{ optional($customer->client_name)->name ?? 'N/A' }}</div>
                </td>
                <td class="card">
                    <div class="label">Phone</div>
                    <div class="value">{{ optional($customer->client_name)->phone ?? 'N/A' }}</div>
                </td>
                <td class="card">
                    <div class="label">Loan Amount</div>
                    <div class="value">{{ number_format($customer->loan_amount ?? 0, 2) }}</div>
                </td>
                <td class="card">
                    <div class="label">Installments</div>
                    <div class="value">{{ $total_installments ?? 0 }}</div>
                </td>
            </tr>
            <tr>
                <td class="card">
                    <div class="label">Total Amount</div>
                    <div class="value">{{ number_format($total_amount ?? 0, 2) }}</div>
                </td>
                <td class="card">
                    <div class="label">Total Paid</div>
                    <div class="value">{{ number_format($total_paid_amount ?? 0, 2) }}</div>
                </td>
                <td class="card">
                    <div class="label">{{ $display_label ?? 'Remaining' }}</div>
                    <div class="value">{{ number_format($displayed_value ?? 0, 2) }}</div>
                </td>
                <td class="card">
                    <div class="label">History Records</div>
                    <div class="value">{{ $total_records ?? 0 }}</div>
                </td>
            </tr>
        </table>

        <div class="section-title">Payment History</div>

        @if(($history ?? collect())->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Installment No</th>
                        <th>Due Date</th>
                        <th>Paid Date</th>
                        <th>EMI Amount</th>
                        <th>Paid Amount</th>
                        <th>Remaining</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($history as $item)
                        <tr>
                            <td class="text-center">{{ $item->installment_no }}</td>
                            <td>{{ $item->due_date ? \Carbon\Carbon::parse($item->due_date)->format('d-M-Y') : 'N/A' }}</td>
                            <td>{{ $item->paid_date ? \Carbon\Carbon::parse($item->paid_date)->format('d-M-Y') : 'N/A' }}</td>
                            <td class="text-right">{{ number_format($item->emi_amount ?? 0, 2) }}</td>
                            <td class="text-right">{{ number_format($item->paid_amount ?? 0, 2) }}</td>
                            <td class="text-right">{{ number_format($item->remaining_payable_amount ?? 0, 2) }}</td>
                            <td class="text-center">
                                <span class="badge status-{{ strtolower($item->status ?? 'pending') }}">{{ $item->status ?? 'Pending' }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">No paid history found for this customer.</div>
        @endif

        <div class="footer">
            <div>&copy; {{ now()->year }} Pannai Loan Management System</div>
        </div>
    </div>
</body>
</html>
