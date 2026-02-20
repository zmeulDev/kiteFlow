<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Visit Confirmed</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #2563eb; color: white; padding: 20px; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 20px; border: 1px solid #e5e7eb; }
        .footer { background: #1f2937; color: #9ca3af; padding: 15px; text-align: center; font-size: 12px; border-radius: 0 0 8px 8px; }
        .btn { display: inline-block; padding: 12px 24px; background: #2563eb; color: white; text-decoration: none; border-radius: 6px; margin: 10px 0; }
        .detail { margin: 8px 0; }
        .label { font-weight: 600; color: #4b5563; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Visit Confirmed ✓</h1>
    </div>
    <div class="content">
        <p>Hi {{ $visitor->first_name }},</p>
        
        <p>Your visit to <strong>{{ $tenant->name }}</strong> has been scheduled.</p>
        
        <div class="detail">
            <span class="label">Date:</span> {{ $visit->scheduled_start->format('F j, Y') }}
        </div>
        <div class="detail">
            <span class="label">Time:</span> {{ $visit->scheduled_start->format('g:i A') }} - {{ $visit->scheduled_end->format('g:i A') }}
        </div>
        @if($visit->meetingRoom)
        <div class="detail">
            <span class="label">Location:</span> {{ $visit->meetingRoom->name }}
            @if($visit->building)
                - {{ $visit->building->name }}
            @endif
        </div>
        @endif
        @if($visit->hostUser)
        <div class="detail">
            <span class="label">Host:</span> {{ $visit->hostUser->name }}
        </div>
        @endif
        @if($visit->purpose)
        <div class="detail">
            <span class="label">Purpose:</span> {{ $visit->purpose }}
        </div>
        @endif
        
        <p><strong>Your Visit Code:</strong> {{ $visit->visit_code }}</p>
        
        <p>Please present this code at the reception desk when you arrive.</p>
        
        <p>Need to reschedule? Contact your host directly.</p>
    </div>
    <div class="footer">
        <p>{{ $tenant->name }}</p>
        @if($tenant->address)
            <p>{{ $tenant->address }}, {{ $tenant->city }}</p>
        @endif
    </div>
</body>
</html>
