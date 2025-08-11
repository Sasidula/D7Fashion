<form method="GET" action="{{ url('/monthly-expenses/report') }}">
    <select name="month">
        <option value="">Select Month</option>
        @foreach(range(1, 12) as $m)
            <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
        @endforeach
    </select>

    <select name="year">
        <option value="">Select Year</option>
        @for($y = now()->year; $y >= 2000; $y--)
            <option value="{{ $y }}">{{ $y }}</option>
        @endfor
    </select>

    <button type="submit">Filter</button>
</form>

<a href="{{ url('/monthly-expenses/export?' . http_build_query(request()->query())) }}">Export PDF</a>

<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Amount</th>
        <th>Type</th>
        <th>Description</th>
        <th>Date</th>
    </tr>
    </thead>
    <tbody>
    @foreach($records as $record)
        <tr>
            <td>{{ $record->id }}</td>
            <td>{{ $record->amount }}</td>
            <td>{{ $record->type }}</td>
            <td>{{ $record->description }}</td>
            <td>{{ $record->created_at->format('Y-m-d') }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
