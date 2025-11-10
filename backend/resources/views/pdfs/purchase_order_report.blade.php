<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Order Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #777; padding: 5px; text-align: left; vertical-align: top; }
        th { background-color: #4CAF50; color: white; }
        td.right { text-align: right; }
        h2 { margin-bottom: 5px; }
        small { color: gray; font-size: 13px; display: block; margin-bottom: 5px; }
    </style>
</head>
<body>
    <h2>
        Purchase Order Report
        @if(!empty($status) && strtolower($status) !== 'all')
            <small>({{ $status }})</small>
        @endif
    </h2>
    <small>Generated on: {{ $generatedAt }}</small>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>PO Number</th>
                <th>Supplier</th>
                <th>Order Date</th>
                <th>Expected Date</th>
                <th>Status</th>
                <th class="right">Amount</th>
                <th>Item Name</th>
                <th class="right">Ordered Qty</th>
                <th class="right">Received Qty</th>
            </tr>
        </thead>
        <tbody>
            @forelse($purchaseOrders as $poIndex => $po)
                @if(!empty($po->items) && $po->items->count())
                    @foreach($po->items as $idx => $item)
                        <tr>
                            @if($idx === 0)
                                <td rowspan="{{ $po->items->count() }}">{{ $poIndex + 1 }}</td>
                                <td rowspan="{{ $po->items->count() }}">{{ $po->po_number }}</td>
                                <td rowspan="{{ $po->items->count() }}">{{ $po->supplier_name }}</td>
                                <td rowspan="{{ $po->items->count() }}">
                                    {{ $po->order_date ? \Carbon\Carbon::parse($po->order_date)->format('M d, Y') : '-' }}
                                </td>
                                <td rowspan="{{ $po->items->count() }}">
                                    {{ $po->expected_date ? \Carbon\Carbon::parse($po->expected_date)->format('M d, Y') : '-' }}
                                </td>
                                <td rowspan="{{ $po->items->count() }}">{{ $po->status }}</td>
                                <td rowspan="{{ $po->items->count() }}" class="right">₱{{ number_format($po->amount, 2) }}</td>
                            @endif
                            <td>{{ $item->item_name }}</td>
                            <td class="right">{{ $item->quantity }}</td>
                            <td class="right">{{ $item->received_quantity ?? 0 }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td>{{ $poIndex + 1 }}</td>
                        <td>{{ $po->po_number }}</td>
                        <td>{{ $po->supplier_name }}</td>
                        <td>{{ $po->order_date ? \Carbon\Carbon::parse($po->order_date)->format('M d, Y') : '-' }}</td>
                        <td>{{ $po->expected_date ? \Carbon\Carbon::parse($po->expected_date)->format('M d, Y') : '-' }}</td>
                        <td>{{ $po->status }}</td>
                        <td class="right">₱{{ number_format($po->amount, 2) }}</td>
                        <td colspan="3" style="text-align:center;">No items</td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="10" style="text-align:center;">No purchase orders found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
