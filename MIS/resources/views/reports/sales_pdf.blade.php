<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid #ccc;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .logo-title {
            display: flex;
            align-items: center;
        }
        .logo-title img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 10px;
        }
        .title {
            font-size: 20px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #999;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .text-green { color: green; }
        .text-red { color: red; }
        .sale-header {
            background-color: #ddd;
            font-weight: bold;
        }
    </style>
</head>
<body>

<!-- Report Header -->
<div class="header">
    <div class="logo-title">
{{--        <img src="{{ public_path('images/logo.png') }}" alt="Logo">--}}
        <div class="title">
            <span style="color:#fd9c0a;">D7</span> <span style="color:#000;">Fashion</span>
            <div style="font-size:14px; font-weight:normal;">Sales Report</div>
        </div>
    </div>
    <div style="text-align:right; font-size: 12px;">
        <strong>Date:</strong> {{ now()->format('Y-m-d') }}<br>
        <strong>Period:</strong>
        @if($monthy && $yeary)
            {{ \Carbon\Carbon::create()->month($monthy)->format('F') }} {{ $yeary }}
        @elseif($yeary)
            Year {{ $yeary }}
        @else
            All Records
        @endif
    </div>
</div>

<!-- Sales Table -->
<table>
    <thead>
    <tr>
        <th>Name</th>
        <th>Type</th>
        <th>Price</th>
    </tr>
    </thead>
    <tbody>
    @foreach($sales as $saleId => $items)
        <!-- Sale Group Header -->
        <tr class="sale-header">
            <td colspan="3">
                Sale ID: {{ $saleId }} |
                Total Price: Rs. {{ number_format($items->first()->sale->price, 2) }} |
                Created At: {{ $items->first()->sale->created_at->format('Y-m-d H:i') }}
            </td>
        </tr>

        <!-- Sale Items -->
        @foreach($items as $item)
            <tr>
                <td>
                    {{ $item->product_type === 'internal'
                        ? $item->internalProductItem->internalProduct->name
                        : $item->externalProductItem->external_product->name }}
                </td>
                <td class="{{ $item->product_type === 'internal' ? 'text-green' : 'text-red' }}">
                    {{ ucfirst($item->product_type) }} product
                </td>
                <td>
                    Rs. {{ number_format(
                                $item->product_type === 'internal'
                                    ? $item->internalProductItem->internalProduct->price
                                    : $item->externalProductItem->external_product->sold_price, 2
                            ) }}
                </td>
            </tr>
        @endforeach
    @endforeach
    </tbody>
</table>

</body>
</html>
