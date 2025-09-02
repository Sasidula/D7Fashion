<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Net Profit Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .container { width: 100%; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; border-bottom: 1px solid #ccc; padding-bottom: 8px; margin-bottom: 15px; }
        .section { margin-bottom: 15px; }
        .title { font-size: 16px; font-weight: bold; margin-bottom: 4px; }
        .subtitle { font-size: 12px; color: #555; }
        .grid { display: flex; justify-content: space-between; }
        .col { width: 32%; }
        .col p { margin: 4px 0; }
        .highlight { font-weight: bold; color: green; margin-top: 6px; }
        .danger { color: red; font-weight: bold; }
        .footer { border-top: 1px solid #ccc; padding-top: 10px; margin-top: 10px; }
    </style>
</head>
<body>
<div class="container">
    <h2 style="text-align:center;">Net Profit Report</h2>

    <div class="header">
        <div >
            <div class="logo-title">
                {{--        <img src="{{ public_path('images/logo.png') }}" alt="Logo">--}}
                <div class="title">
                    <span style="color:#fd9c0a;">D7</span> <span style="color:#000;">Fashion</span>
                </div>
            </div>
        </div>
        <div>
            <div class="title">Business Performance</div>
            <div class="subtitle">
                Report for {{ \Carbon\Carbon::create(null, $netProfit['month'])->format('F') }} {{ $netProfit['year'] }}
            </div>
        </div>
        <div class="subtitle">
            Generated On: {{ now()->format('d M Y') }}
        </div>
    </div>

    <div class="grid">
        <!-- Gross Income -->
        <div class="col">
            <p class="danger">Gross Income:</p>
            <p style="margin-left: 10px;">Total Sales: Rs. {{ number_format($netProfit['sales'], 2) }}</p>
            <p style="margin-left: 10px;">Other Incomes: Rs. {{ number_format($netProfit['incomes'], 2) }}</p>
            <p style="margin-left: 20px;"><span>Total Petty Cash:</span> Rs. {{ number_format($netProfit['petty_incomes'], 2) }}</p>
            <p style="margin-left: 20px;"><span>Total Incomes:</span> Rs. {{ number_format($netProfit['total_incomes'], 2) }}</p>
            @foreach($netProfit['income_record'] as $record)
                <p  style="margin-left: 30px;">{{ $record['title'] }}: Rs. {{ number_format($record['total'], 2) }}</p>
            @endforeach
            <p class="highlight">Total: Rs. {{ number_format($netProfit['sales'] + $netProfit['incomes'], 2) }}</p>
        </div>

        <!-- Production Costs -->
        <div class="col">
            <p class="danger">Production Costs:</p>
            <p>Material Costs: Rs. {{ number_format($netProfit['material_costs'], 2) }}</p>
            <p>External Costs: Rs. {{ number_format($netProfit['external_costs'], 2) }}</p>
            <p class="highlight">Total: Rs. {{ number_format($netProfit['material_costs'] + $netProfit['external_costs'], 2) }}</p>
        </div>

        <!-- Operating Costs -->
        <div class="col">
            <p class="danger">Operating Costs:</p>
            <p style="margin-left: 10px;">Salaries: Rs. {{ number_format($netProfit['salaries'], 2) }}</p>
            <p style="margin-left: 10px;">Expenses: Rs. {{ number_format($netProfit['expenses'], 2) }}</p>
            <p style="margin-left: 20px;"><span>Total Petty Cash:</span> Rs. {{ number_format($netProfit['petty_expenses'], 2) }}</p>
            <p style="margin-left: 20px;"><span>Total Expenses:</span> Rs. {{ number_format($netProfit['total_expenses'], 2) }}</p>
            @foreach($netProfit['expense_record'] as $record)
                <p style="margin-left: 30px;">{{ $record['title'] }}: Rs. {{ number_format($record['total'], 2) }}</p>
            @endforeach
            <p class="highlight">Total: Rs. {{ number_format($netProfit['salaries'] + $netProfit['expenses'], 2) }}</p>
        </div>
    </div>

    <div class="footer">
        <p class="title {{ $netProfit['profit'] >= 0 ? 'highlight' : 'danger' }}">
            Net Profit: Rs. {{ number_format($netProfit['profit'], 2) }}
        </p>
    </div>
</div>
</body>
</html>
