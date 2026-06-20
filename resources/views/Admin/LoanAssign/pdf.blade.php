<!DOCTYPE html>
<html>
<head>
    <title>Loan Assign List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #333;
        }
        .info {
            margin-bottom: 15px;
        }
        .info p {
            margin: 2px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table th {
            background-color: #f2f2f2;
            text-align: left;
            padding: 8px;
            border: 1px solid #ddd;
            font-weight: bold;
        }
        table td {
            padding: 6px;
            border: 1px solid #ddd;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Loan Assign List</h1>
        <p>Generated on: {{ date('Y-m-d H:i:s') }}</p>
        @if(request()->has('search') && !empty(request()->search))
            <p>Search Term: {{ request()->search }}</p>
        @endif
    </div>

    <div class="info">
        <p>Total Records: {{ $data->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Customer Name</th>
                <th>Phone</th>
                <th>Loan Name</th>
                <th>Branch</th>
                <th>Route</th>
                <th>Amount</th>
                <th>Interest</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->client_name->name ?? 'N/A' }}</td>
                    <td>{{ $item->phone ?? 'N/A' }}</td>
                    <td>{{ $item->loan->loan_name ?? 'N/A' }}</td>
                    <td>{{ $item->branches->branch_name ?? 'N/A' }}</td>
                    <td>{{ $item->routes->route_name ?? 'N/A' }}</td>
                    <td class="text-right">{{ number_format($item->loan_amount ?? 0, 2) }}</td>
                    <td class="text-right">{{ $item->int->interest_id ?? 'N/A' }}%</td>
                    <td>
                        {{ $item->loanassign_date
                            ? \Carbon\Carbon::parse($item->loanassign_date)->format('d-m-Y')
                            : 'N/A'
                        }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center">No records found</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            @if($data->isNotEmpty())
                <tr>
                    <td colspan="6" class="text-right"><strong>Total:</strong></td>
                    <td class="text-right"><strong>{{ number_format($data->sum('loan_amount'), 2) }}</strong></td>
                    <td colspan="2"></td>
                </tr>
            @endif
        </tfoot>
    </table>

    <div class="footer">
        <p>Page 1 of 1</p>
    </div>
</body>
</html>
