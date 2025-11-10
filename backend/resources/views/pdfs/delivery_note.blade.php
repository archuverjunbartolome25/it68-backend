<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Order</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { display: flex; justify-content: space-between; align-items: center; }
        .company { width: 50%; }
        .company h2 { margin: 0; }
        .company p { margin: 2px 0; }
        .logo { margin: 5px 0; }
        .title { font-size: 22px; color: #004080; font-weight: bold; text-align: left; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table th, table td { border: 1px solid #000; padding: 6px; text-align: left; }
        .no-border td { border: none; }
        .footer { margin-top: 30px; font-size: 11px; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company">
            <h2>Balipure Purified Drinking Water</h2>
            <h3>Bali Champion Bottlers Inc.</h3>
            <p>VAT Registered: TIN 009-068-451-000</p>
            <div class="logo">
            </div>
        </div>
        <div class="title">Purchase Order</div>
    </div>

    <table class="no-border">
        <tr>
            <td><strong>Order Date:</strong> {{ $order->order_date }}</td>
            <td><strong>Order #:</strong> {{ $order->po_number }}</td>
        </tr>
        <tr>
            <td><strong>Delivery Note #:</strong> DN-{{ $order->id }}</td>
            <td><strong>Supplier:</strong> {{ $order->supplier_name }}</td>
        </tr>
        <tr>
            <td><strong>Expected Date:</strong> {{ $order->expected_date }}</td>
            <td><strong>Status:</strong> {{ ucfirst($order->status) }}</td>
        </tr>
    </table>

    <h4>Ordered Items</h4>
    <table>
        <thead>
            <tr>
                <th>Item #</th>
                <th>Description</th>
                <th>Ordered</th>
                <th>Delivered</th>
                <th>Outstanding</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->item_name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $item->received_quantity }}</td>
                    <td>{{ $item->quantity - $item->received_quantity }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Notice must be given to us of any goods not received within 10 days taken from the date of dispatch stated on invoice.</p>
        <p>Any shortage or damage must be notified within 72 hours of receipt of goods.</p>
        <p>No goods may be returned without prior authorization from the company.</p>
        <p><strong>Thank you for your business!</strong></p>
        <p>L-21 B-13 Malabanias Road, Josefa Subdivision, Brgy. Malabanias, Angeles City, Pampanga
        <p>sales.balipure@gmail.com</p>
    </div>
</body>
</html>
