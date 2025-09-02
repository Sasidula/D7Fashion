<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Report</title>
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
        .bonus-add {
            color: green;
            font-weight: bold;
        }
        .bonus-remove {
            color: red;
            font-weight: bold;
        }
        .no-records {
            text-align: center;
            font-style: italic;
            color: #777;
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
            <div style="font-size:14px; font-weight:normal;">Purchase Report</div>
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

<!-- Salary Table -->
<table>
    <thead>
    <tr>
        <th>Date</th>
        <th>Name</th>
        <th>Type</th>
        <th>Quantity</th>
        <th>Total Price</th>
    </tr>
    </thead>
    <tbody>
    @forelse ($purchases as $row)
        <tr>
            <td>{{ $row->date }}</td>
            <td>{{ $row->item_name }}</td>
            <td>{{ $row->type }}</td>
            <td class="bonus-add">{{ $row->quantity }}</td>
            <td class="bonus-remove">Rs. {{ number_format($row->total_price, 2) }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="9" class="no-records">No records found.</td>
        </tr>
    @endforelse
    </tbody>
</table>

</body>
</html>
