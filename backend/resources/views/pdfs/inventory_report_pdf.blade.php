<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Inventory Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #777; padding: 5px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        h2 { margin-bottom: 0; display: flex; align-items: baseline; gap: 8px; }
        small { color: gray; font-size: 13px; }
    </style>
</head>
<body>
    <h2>
        Inventory Summary
        @if(!empty($selectedType) && strtolower($selectedType) !== 'all')
            <small>({{ $selectedType }})</small>
        @endif
    </h2>
    <small>Generated on: {{ $generatedAt }}</small>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Unit</th>
                <th>Unit Cost</th>
                <th>Quantity (Unit)</th>
                <th>Quantity (Pieces)</th>
                <th>Low Stock Alert</th>
                <th>Last Updated</th>
            </tr>
        </thead>
        <tbody>
            @forelse($inventoryData as $item)
                <tr>
                    <td>{{ $item->item }}</td>
                    <td>{{ $item->unit }}</td>
                    <td>₱{{ number_format($item->unit_cost ?? 0, 2) }}</td>
                    <td>{{ number_format($item->quantity ?? 0) }}</td>
                    <td>{{ number_format($item->quantity_pcs ?? $item->quantity_pieces ?? 0) }}</td>
                    <td>{{ $item->low_stock_alert ?? '—' }}</td>
                    <td>{{ $item->updated_at ? \Carbon\Carbon::parse($item->updated_at)->format('M d, Y h:i A') : '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align:center;">No inventory found.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
