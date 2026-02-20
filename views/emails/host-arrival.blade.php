<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Visitor Arrived</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #059669; color: white; padding: 20px; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 20px; border: 1px solid #e5e7eb; }
        .footer { background: #1f2937; color: #9ca3af; padding: 15px; text-align: center; font-size: 12px; border-radius: 0 0 8px 8px; }
        .visitor-card { background: white; padding: 15px; border-radius: 8px; border: 1px solid #e5e7eb; margin: 15px 0; }
        .detail { margin: 8px 0; }
        .label { font-weight: 600; color: #4b5563; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Visitor Arrived 🚪</h1>
    </div>
    <div class="content">
        <p>Hi {{ $host->name }},</p>
        
        <p>Your visitor has arrived and checked in.</p>
        
        <div class="visitor-card">
            <div class="detail">
                <span class="label">Name:</span> {{ $visitor->first_name }} {{ $visitor->last_name }}
            </div>
            @if($visitor->company)
            <div class="detail">
                <span class="label">Company:</span> {{ $visitor->company }}
            </div>
            @endif
            @if($visitor->phone)
            <div class="detail">
                <span class="label">Phone:</span> {{ $visitor->phone }}
            </div>
            @endif
            @if($visit->meetingRoom)
            <div class="detail">
                <span class="label">Location:</span> {{ $visit->meetingRoom->name }}
            </div>
            @endif
            @if($visit->purpose)
            <div class="detail">
                <span class="label">Purpose:</span> {{ $visit->purpose }}
            </div>
            @endif
            <div class="detail">
                <span class="label">Checked in at:</span> {{ $visit->checked_in_at->format('g:i A') }}
            </div>
        </div>
        
        <p>Please go to the reception to greet your visitor.</p>
    </div>
    <div class="footer">
        <p>{{ $tenant->name }} - Visitor Management</p>
    </div>
</body>
</html>
