<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ ucfirst($report->type) }} Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            margin-bottom: 20px;
            border-bottom: 2px solid #dee2e6;
        }
        h1 {
            color: #007bff;
            margin: 0 0 10px 0;
        }
        h2 {
            color: #6c757d;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 10px;
            margin-top: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #dee2e6;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #e9ecef;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .meta-info {
            font-size: 0.9em;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ ucfirst($report->type) }} Report</h1>
        <p class="meta-info">Generated on: {{ now()->format('Y-m-d H:i:s') }}</p>
        <p class="meta-info">Period: {{ optional($report->start_date)->format('Y-m-d') }} to {{ optional($report->end_date)->format('Y-m-d') }}</p>
        @if($report->branch)
            <p class="meta-info">Branch: {{ $report->branch->name }}</p>
        @endif
    </div>

    <h2>Summary</h2>
    @if(!empty($summary))
        <table>
            <thead>
                <tr>
                    <th>Metric</th>
                    <th>Value</th>
                </tr>
            </thead>
            <tbody>
                @foreach($summary as $key => $value)
                    <tr>
                        <td>{{ ucfirst(str_replace('_', ' ', $key)) }}</td>
                        <td>{{ is_array($value) ? json_encode($value) : $value }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No summary data available.</p>
    @endif

    <h2>Detailed Report</h2>
    @if(!empty($reportData))
        <table>
            <thead>
                <tr>
                    @foreach(array_keys($reportData) as $header)
                        <th>{{ ucfirst(str_replace('_', ' ', $header)) }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <tr>
                    @foreach($reportData as $value)
                        <td>{{ is_array($value) ? json_encode($value) : $value }}</td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    @else
        <p>No detailed report data available.</p>
    @endif
</body>
</html>
