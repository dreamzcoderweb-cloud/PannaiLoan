<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Loan List Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 10px;
        }
        .date {
            text-align: right;
            margin-bottom: 20px;
            color: #666;
        }
    </style>
</head>
<body>
     <div class="header">
        <h1>Loan List Report</h1>
        <p>Generated on: {{ date('F d, Y') }}</p>
    </div>

   {{-- <div class="date">
        Report Date: {{ date('Y-m-d H:i:s') }}
    </div> --}}

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Loan Name</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $loan)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $loan->loan_name ?? 'N/A' }}</td>
                <td>{{ $loan->created_at->format('d-m-Y') }}</td>
            </tr>
            @endforeach

            @if($data->isEmpty())
            <tr>
                <td colspan="4" style="text-align: center;">No loans found</td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        <p>Page 1 of 1</p>
        <p>Total Loans: {{ $data->count() }}</p>
    </div>
</body>
</html>
