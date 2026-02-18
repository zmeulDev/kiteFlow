<!-- resources/views/exports/visitor-log-pdf.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Visitor Log Export</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { bg-color: #f4f4f4; font-weight: bold; }
        .header { text-align: center; margin-bottom: 30px; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <h1>KiteFlow Visitor Log</h1>
        <p>Generated on: {{ now()->format('Y-m-d H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Visitor Name</th>
                <th>Email</th>
                <th>Host</th>
                <th>Purpose</th>
                <th>Check In</th>
                <th>Check Out</th>
            </tr>
        </thead>
        <tbody>
            @foreach($visits as $visit)
                <tr>
                    <td>{{ $visit->visitor->full_name }}</td>
                    <td>{{ $visit->visitor->email }}</td>
                    <td>{{ $visit->host->name }}</td>
                    <td>{{ $visit->purpose }}</td>
                    <td>{{ $visit->checked_in_at?->format('Y-m-d H:i') ?? 'N/A' }}</td>
                    <td>{{ $visit->checked_out_at?->format('Y-m-d H:i') ?? 'In Building' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        &copy; {{ date('Y') }} KiteFlow. All rights reserved.
    </div>
</body>
</html>
