<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Employee Attendance Report</title>
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
        .group-header {
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
            <div style="font-size:14px; font-weight:normal;">Employee Attendance Report</div>
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

<!-- Attendance Table -->
<table>
    <thead>
    <tr>
        <th>Date</th>
        <th>Check In</th>
        <th>Check Out</th>
        <th>Hours Worked</th>
    </tr>
    </thead>
    <tbody>
    @foreach($attendance as $userId => $attendances)
        @php
            $totalHours = 0;
        @endphp
        <!-- User Group Header -->
        <tr class="group-header">
            <td colspan="4">
                User: {{ $attendances->first()->user->name ?? 'Unknown' }} —
                Role: {{ $attendances->first()->user->role ?? '-' }}
                (ID: {{ $userId }})
            </td>
        </tr>

        <!-- Attendance Records -->
        @foreach($attendances as $record)
            <tr>
                <td>{{ $record->date->format('Y-m-d') }}</td>
                <td>{{ $record->check_in ?? '-' }}</td>
                <td>{{ $record->check_out ?? '-' }}</td>
                <td>{{ $record->hours_worked ?? '-' }} Hours</td>
                @php
                    $totalHours += $record->hours_worked;
                @endphp
            </tr>
        @endforeach
        <tr class="bg-gray-200 font-bold">
            <td colspan="4">
                Total Hours Worked: {{ $totalHours }} Hours
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

</body>
</html>
