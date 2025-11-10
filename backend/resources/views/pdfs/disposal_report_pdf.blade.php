<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Disposal Report</title>
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
    <h2>Disposal Report</h2>
    <small>Generated on: {{ now()->format('F d, Y h:i A') }}</small>

    <table>
        <thead>
            <tr>
                <th>Disposal #</th>
                <th>Item Type</th>
                <th>Item</th>
                <th>Quantity</th>
                <th>Reason</th>
                <th>Request Date</th>
                <th>Disposal Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Disposed by: (Employee ID)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($disposals as $d)
                <tr>
                    <td>{{ $d->disposal_number }}</td>
                    <td>{{ $d->item_type }}</td>
                    <td>{{ $d->item }}</td>
                    <td>{{ $d->quantity }}</td>
                    <td>{{ $d->reason ?? '—' }}</td>
                    <td>{{ \Carbon\Carbon::parse($d->disposal_date)->format('m/d/Y') }}</td>
                    <td>{{ $d->disposed_date ? \Carbon\Carbon::parse($d->disposed_date)->format('m/d/Y') : 'Pending' }}</td>
                    <td>{{ $d->disposed_time ?? 'Pending' }}</td>
                    <td>{{ $d->status }}</td>
                    <td>{{ $d->employee_id ?? 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align:center;">No disposal records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
