<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Return To Vendor Report</title>
    <style>
        body { 
            font-family: DejaVu Sans, sans-serif; 
            font-size: 11px; 
            color: #333; 
            margin: 20px; 
        }

        h2 { 
            margin-bottom: 0; 
            display: flex; 
            align-items: baseline; 
            gap: 8px; 
        }

        small { 
            color: gray; 
            font-size: 13px; 
        }

        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px; 
        }

        th, td { 
            border: 1px solid #777; 
            padding: 5px; 
            text-align: left; 
        }

        th { 
            background-color: #4CAF50; 
            color: white; 
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <h2>Return To Vendor Report</h2>
@if(!empty($selectedDate))
    <p style="font-size: 12px; margin-top: 0;">
        Filtered: 
        @switch($reportType)
            @case('Daily')
                {{ \Carbon\Carbon::parse($selectedDate)->format('M d, Y') }}
                @break
            @case('Weekly')
                Week: {{ $selectedDate }}
                @break
            @case('Monthly')
                {{ \Carbon\Carbon::createFromFormat('Y-m', $selectedDate)->format('F Y') }}
                @break
            @case('Yearly')
                {{ $selectedDate }}
                @break
        @endswitch
    </p>
@endif
    <small>Generated on: {{ now()->format('F d, Y h:i A') }}</small>

    <table>
        <thead>
            <tr>
                <th>RTV #</th>
                <th>Customer</th>
                <th>Date Ordered</th>
                <th>Date Returned</th>
                <th>Status</th>
                <th>Qty 350ml</th>
                <th>Qty 500ml</th>
                <th>Qty 1L</th>
                <th>Qty 6L</th>
            </tr>
        </thead>
        <tbody>
            @forelse($returns as $r)
                <tr>
                    <td>{{ $r->rtv_number ?? 'N/A' }}</td>
                    <td>{{ $r->customer_name ?? 'Unknown' }}</td>
                    <td>{{ \Carbon\Carbon::parse($r->date_ordered)->format('m/d/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($r->date_returned)->format('m/d/Y') }}</td>
                    <td>{{ $r->status ?? 'Pending' }}</td>
                    <td>{{ $r->qty_350ml ?? 0 }}</td>
                    <td>{{ $r->qty_500ml ?? 0 }}</td>
                    <td>{{ $r->qty_1l ?? 0 }}</td>
                    <td>{{ $r->qty_6l ?? 0 }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align:center;">No return records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
