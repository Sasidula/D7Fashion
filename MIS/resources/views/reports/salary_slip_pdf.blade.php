@php use Carbon\Carbon; @endphp
    <!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Salary Slip</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 14px;
            color: #333;
        }

        .slip-container {
            width: 100%;
            max-width: 800px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
        }

        .header {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .net-pay {
            font-size: 18px;
            font-weight: bold;
            margin-top: 15px;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
    </style>
</head>
<body>

<!-- Report Header -->


@foreach($salaryReport as $row)
    <div class="slip-container">
        <div class="header">
            <div>
                <div class="logo-title">
                    {{--        <img src="{{ public_path('images/logo.png') }}" alt="Logo">--}}
                    <div class="title">
                        <span style="color:#fd9c0a;">D7</span> <span style="color:#000;">Fashion</span>
                    </div>
                </div>
            </div>
            <div>
                <h2>{{ $row['name'] }}</h2>
                <small>Salary Slip - {{ $row['year'] }}-{{ $row['month'] }}</small>
            </div>
            <div>
                <small>Generated On: {{ now()->format('d M Y') }}</small>
            </div>
        </div>
        <div class="section">
            <div>Worked Hours:</div>
            <div>{{ $row['worked_hours'] }}</div>
        </div>
        @foreach($attendances as $userId => $userAttendances)
            @foreach($userAttendances as $attendance)
                <div class="section">
                    <span class="font-medium text-gray-700">Date:</span>
                    {{ Carbon::parse($attendance['date'])->format('Y-m-d') }}
                    <div>
                        <span class="font-medium text-gray-700">Check In:</span> {{ $attendance['check_in'] ?? '-' }}
                        <span class="font-medium text-gray-700">Check Out:</span> {{ $attendance['check_out'] ?? '-' }}
                        <span
                            class="font-medium text-gray-700">Hours Worked:</span> {{ $attendance['hours_worked'] ?? '-' }}
                    </div>
                </div>
            @endforeach
        @endforeach

        <div class="section">
            <div>Salary Type:</div>
            <div>{{ $row['salary_type'] }}</div>
        </div>
        <div class="section">
            <div>Rate:</div>
            <div>Rs. {{ number_format($row['rate'], 2) }}</div>
        </div>
        <div class="section">
            <div>Base Salary:</div>
            <div>Rs. {{ number_format($row['base_salary'], 2) }}</div>
        </div>
        <div class="section">
            <div>Bonus Adds:</div>
            <div style="color:green;">+ Rs. {{ number_format($row['bonus_adds'], 2) }}</div>
        </div>
        <div class="section">
            <div>Bonus Removes:</div>
            <div style="color:red;">- Rs. {{ number_format($row['bonus_removes'], 2) }}</div>
        </div>
        <div class="net-pay">
            Net Pay: Rs. {{ number_format($row['calculatedSalary'], 2) }}
        </div>
    </div>
    <br>
@endforeach
</body>
</html>
