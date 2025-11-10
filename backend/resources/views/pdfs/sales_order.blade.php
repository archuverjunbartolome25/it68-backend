<!DOCTYPE html>
<html>
<head>
  <title>Customer Order Form</title>
  <style>
    body {
      font-family: 'DejaVu Sans', sans-serif;
      margin: 0;
      padding: 0;
    }

    .form-container {
      width: 100%;
      border: 2px solid #000;
      padding: 10px;
      box-sizing: border-box;
    }

    /* Header */
    .header {
      background-color: #A0E7E5;
      text-align: center;
      padding: 10px;
      border: 2px solid #000;
      margin-bottom: 10px;
    }

    .header h1 {
      margin: 0;
      font-size: 24px;
      font-weight: bold;
    }

    .header p {
      margin: 4px 0;
      font-size: 14px;
    }

    /* Tables */
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 10px;
      font-size: 14px;
    }

    td, th {
      padding: 5px;
      border: 1px solid #000;
      vertical-align: top;
    }

    .info-title {
      font-weight: bold;
      background-color: #A0E7E5;
    }

    .items-table th {
      background-color: #A0E7E5;
      text-align: center;
      font-weight: bold;
    }

    .total-row td {
      font-weight: bold;
    }

    .text-right {
      text-align: right;
    }

    .terms {
      margin-top: 10px;
      border-top: 2px solid #000;
      padding-top: 5px;
      font-size: 12px;
    }

    .signature {
      margin-top: 20px;
      text-align: right;
    }

    .signature-box {
      display: inline-block;
      padding: 5px 15px;
      background-color: #A0E7E5;
      border: 2px solid #000;
      text-align: center;
      font-weight: bold;
    }
  </style>
</head>

<body>

  <div class="form-container">
    <!-- HEADER -->
    <div class="header">
      <h1>Balipure Purified Drinking Water</h1>
      <p>BALI CHAMPION BOTTLERS, INC.</p>
      <p>L-21 B-13 Malabanias Road, Josefa Subdivision, Brgy. Malabanias, Angeles City, Pampanga</p>
      <p>Email Address: sales.balipure@gmail.com</p>
      <p>VAT Registered TIN 009-068-451-000</p>
    </div>

    <!-- INFO GRID -->
    <table class="info-grid">
      <thead>
        <tr>
          <td class="info-title" colspan="2">Order Information</td>
          <td class="info-title" colspan="2">Customer Information</td>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Order No:</td>
          <td>{{ $orderNumber }}</td>
          <td>Customer Name:</td>
          <td>{{ $customer?->name ?? 'N/A' }}</td>
        </tr>
        <tr>
          <td>Order Date:</td>
          <td>{{ $order->date }}</td>
          <td>Billing Address:</td>
          <td>{{ $customer?->billing_address ?? 'N/A' }}</td>
        </tr>
        <tr>
          <td>Order Type:</td>
          <td>{{ $order->order_type }}</td>
          <td>Shipping Address:</td>
          <td>{{ $customer?->shipping_address ?? 'N/A' }}</td>
        </tr>
        <tr>
          <td></td>
          <td></td>
          <td>TIN:</td>
          <td>{{ $customer?->tin ?? 'N/A' }}</td>
        </tr>
      </tbody>
    </table>

    <!-- ITEMS TABLE -->
    <table class="items-table">
      <thead>
        <tr>
          <th>Item Description</th>
          <th>Quantity</th>
          <th>Unit Price</th>
          <th>Total Price</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>350ml</td>
          <td>{{ $order->qty_350ml ?? 0 }}</td>
          <td>₱130.00</td>
          <td>₱{{ number_format(($order->qty_350ml ?? 0) * 130, 2) }}</td>
        </tr>
        <tr>
          <td>500ml</td>
          <td>{{ $order->qty_500ml ?? 0 }}</td>
          <td>₱155.00</td>
          <td>₱{{ number_format(($order->qty_500ml ?? 0) * 155, 2) }}</td>
        </tr>
        <tr>
          <td>1L</td>
          <td>{{ $order->qty_1L ?? 0 }}</td>
          <td>₱130.00</td>
          <td>₱{{ number_format(($order->qty_1L ?? 0) * 130, 2) }}</td>
        </tr>
        <tr>
          <td>6L</td>
          <td>{{ $order->qty_6L ?? 0 }}</td>
          <td>₱60.00</td>
          <td>₱{{ number_format(($order->qty_6L ?? 0) * 60, 2) }}</td>
        </tr>
      </tbody>
    </table>

    <!-- TOTALS TABLE -->
    <table>
      <tr>
        <td style="width: 50%;">Payment Details:</td>
        <td style="width: 25%;">Subtotal:</td>
        <td style="width: 25%;" class="text-right">₱{{ number_format($order->amount, 2) }}</td>
      </tr>
      <tr>
        <td>Bank Details: {{ $customer?->bank_details ?? 'N/A' }}</td>
        <td>Discount:</td>
        <td class="text-right">
          ₱{{ number_format(($order->amount * ($customer?->discounts / 100)), 2) }}
        </td>
      </tr>
      <tr>
        <td></td>
        <td>Tax:</td>
        <td class="text-right">₱0.00</td>
      </tr>
      <tr class="total-row">
        <td></td>
        <td>Grand Total:</td>
        <td class="text-right">
          ₱{{ number_format($order->amount - ($order->amount * ($customer?->discounts / 100)), 2) }}
        </td>
      </tr>
    </table>

    <!-- TERMS -->
    <div class="terms">
<strong>Terms and Conditions:</strong><br>
<small>
1. All sales are subject to availability of stock.<br>
2. Payment must be made within seven (7) days from the date of delivery unless otherwise agreed.<br>
3. Goods once sold are non-returnable except in cases of defect or damage reported within 48 hours of receipt.<br>
4. The company reserves the right to correct any errors in pricing or description.<br>
5. Delivery schedules are estimates and subject to change due to unforeseen circumstances.<br>
6. By signing this order, the customer agrees to the terms stated above.
</small>
    </div>

    <!-- SIGNATURE -->
    <div class="signature">
      <div class="signature-box">Signature with Date</div>
    </div>
  </div>

</body>
</html>
