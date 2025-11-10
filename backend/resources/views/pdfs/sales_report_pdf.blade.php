<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Order Summary</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #777; padding: 5px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        h2 { margin-bottom: 2px; }
        small { color: gray; }
        .subtitle { font-size: 10px; color: #555; display: block; margin-bottom: 5px; }
    </style>
</head>
<body>
    <h2>Sales Order Summary</h2>

    {{-- Show applied filters --}}
    @php
        $filterLabel = 'All Sales';
        if (!empty($reportType) && !empty($filterValue)) {
            if ($reportType === 'Daily') $filterLabel = 'Sales Report for ' . \Carbon\Carbon::parse($filterValue)->format('F d, Y');
            elseif ($reportType === 'Weekly') $filterLabel = 'Sales Report for Week ' . $filterValue;
            elseif ($reportType === 'Monthly') $filterLabel = 'Sales Report for ' . \Carbon\Carbon::createFromFormat('Y-m', $filterValue)->format('F Y');
            elseif ($reportType === 'Yearly') $filterLabel = 'Sales Report for ' . $filterValue;
        }

        if (!empty($statusFilter) && $statusFilter !== 'All') {
            $filterLabel .= ' | Status: ' . $statusFilter;
        }
    @endphp

    <p style="font-size: 12px; margin-top:0;">{{ $filterLabel }}</p>
    <p style="font-size: 11px; color: gray; margin-top: -6px;">Generated on: {{ $generatedAt }}</p>

    <table>
        <thead>
            <tr>
                <th>Sales Order #</th>
                <th>Customer</th>
                <th>Location</th>
                <th>Date Ordered</th>
                <th>Delivery Date</th>
                <th>Date Delivered</th>
                <th>Status</th>
                <th>Products</th>
                <th>Quantities</th>
                <th>Total Amount (₱)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($salesOrders as $order)
                @php
                    $quantities = json_decode($order->quantities, true) ?? [];
                    $quantitySummary = collect($quantities)
                        ->filter(fn($qty) => $qty > 0)
                        ->map(fn($qty, $size) => "$size: $qty")
                        ->implode(', ');

                    $orderNumber = 'SO-' . str_replace('-', '', $order->date) . '-' . str_pad($order->id, 4, '0', STR_PAD_LEFT);
                    $orderStatus = $order->status ?? ($order->date_delivered ? 'Delivered' : 'Pending');
                @endphp
                <tr>
                    <td>{{ $orderNumber }}</td>
                    <td>{{ $order->customer_name ?? 'N/A' }}</td>
                    <td>{{ $order->location ?? 'N/A' }}</td>
                    <td>{{ \Carbon\Carbon::parse($order->date)->format('M d, Y') }}</td>
                    <td>{{ $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->format('M d, Y') : '—' }}</td>
                    <td>{{ $order->date_delivered ? \Carbon\Carbon::parse($order->date_delivered)->format('M d, Y') : 'Pending' }}</td>
                    <td>{{ $orderStatus }}</td>
                    <td>{{ $order->products ?? '—' }}</td>
                    <td>{{ $quantitySummary ?: '—' }}</td>
                    <td>₱{{ number_format($order->total_sales, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align:center;">No sales found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
