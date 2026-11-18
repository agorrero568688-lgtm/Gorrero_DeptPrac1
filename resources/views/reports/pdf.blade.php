<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Plant Nursery Report</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #999;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #eee;
        }
        .text-right {
            text-align: right;
        }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="if (!window.dompdf) { window.print(); }">
    <div class="no-print">
        <button onclick="window.print()">Print</button>
        <a href="{{ route('reports.index') }}">Back to Reports</a>
    </div>

    <h2>Plant Nursery Report</h2>

    <p>
        Report: {{ strtoupper(str_replace('_', ' ', $filters['report_type'])) }} |
        Category: {{ $filters['category'] ?: 'All Categories' }} |
        Stock: {{ $filters['stock_status'] ?: 'All Statuses' }} |
        Generated: {{ $filters['generated_at'] }}
    </p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Category</th>
                <th class="text-right">Price (PHP)</th>
                <th>Stock</th>
                <th>Description</th>
                <th>Added By</th>
            </tr>
        </thead>
        <tbody>
            @forelse($plants as $plant)
            <tr>
                <td>{{ $plant->id }}</td>
                <td>{{ $plant->plant_name }}</td>
                <td>{{ $plant->category }}</td>
                <td class="text-right">₱{{ number_format((float) $plant->unit_price, 2) }}</td>
                <td>{{ $plant->formatted_status }}</td>
                <td>{{ $plant->description }}</td>
                <td>{{ $plant->user->name ?? 'Admin' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center;">No records found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
