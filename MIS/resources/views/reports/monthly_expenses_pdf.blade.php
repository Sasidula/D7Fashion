<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Monthly Expenses Report</title>
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
    </style>
</head>
<body>

<!-- Report Header -->
<div class="header">
    <div class="logo-title">
{{--        <img src="{{ public_path('images/logo.png') }}" alt="Logo">--}}
        <div class="title">
            <span style="color:#fd9c0a;">D7</span> <span style="color:#000;">Fashion</span>
            <div style="font-size:14px; font-weight:normal;">Monthly Expenses Report</div>
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

<!-- Table -->
<table>
    <thead>
    <tr>
        <th>Title</th>
        <th>Type</th>
        <th>Amount</th>
        <th>Description</th>
        <th>Created At</th>
    </tr>
    </thead>
    <tbody>
    @foreach($expenses as $expense)
        <tr>
            <td>{{ $expense->expense->title }}</td>
            <td class="{{ $expense->type === 'income' ? 'text-green' : 'text-red' }}">
                {{ ucfirst($expense->type) }}
            </td>
            <td>Rs.{{ number_format($expense->amount, 2) }}</td>
            <td>{{ $expense->description }}</td>
            <td>{{ $expense->created_at->format('Y-m-d H:i') }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

</body>
</html>
