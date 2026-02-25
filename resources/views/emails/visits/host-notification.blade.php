<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>You Have a Scheduled Visit</title>
</head>
<body style="font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #f9fafb; border-radius: 12px; padding: 32px; margin-bottom: 24px;">
        <h1 style="margin: 0 0 8px 0; font-size: 24px; font-weight: 600; color: #111827;">Scheduled Visit</h1>
        <p style="margin: 0; color: #6b7280;">A visitor has been scheduled to meet with you.</p>
    </div>

    <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px; margin-bottom: 24px;">
        <h2 style="margin: 0 0 16px 0; font-size: 18px; font-weight: 600;">Visitor Information</h2>

        <div style="margin-bottom: 16px;">
            <div style="color: #6b7280; font-size: 14px; margin-bottom: 4px;">Name</div>
            <div style="font-weight: 500;">{{ $visitor->full_name }}</div>
        </div>

        <div style="margin-bottom: 16px;">
            <div style="color: #6b7280; font-size: 14px; margin-bottom: 4px;">Email</div>
            <div style="font-weight: 500;">{{ $visitor->email }}</div>
        </div>

        @if($visitor->phone)
        <div style="margin-bottom: 16px;">
            <div style="color: #6b7280; font-size: 14px; margin-bottom: 4px;">Phone</div>
            <div style="font-weight: 500;">{{ $visitor->phone }}</div>
        </div>
        @endif

        @if($visitor->company)
        <div style="margin-bottom: 16px;">
            <div style="color: #6b7280; font-size: 14px; margin-bottom: 4px;">Company</div>
            <div style="font-weight: 500;">{{ $visitor->company->name }}</div>
        </div>
        @endif
    </div>

    <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px; margin-bottom: 24px;">
        <h2 style="margin: 0 0 16px 0; font-size: 18px; font-weight: 600;">Visit Details</h2>

        <div style="margin-bottom: 16px;">
            <div style="color: #6b7280; font-size: 14px; margin-bottom: 4px;">Scheduled For</div>
            <div style="font-weight: 500;">{{ $scheduledAt?->format('l, F j, Y \a\t g:i A') ?? 'To be confirmed' }}</div>
        </div>

        @if($building)
        <div style="margin-bottom: 16px;">
            <div style="color: #6b7280; font-size: 14px; margin-bottom: 4px;">Location</div>
            <div style="font-weight: 500;">{{ $entrance?->name ?? 'Main Entrance' }}</div>
            <div style="color: #6b7280;">{{ $building->name }}</div>
        </div>
        @endif

        @if($visit->purpose)
        <div style="margin-bottom: 16px;">
            <div style="color: #6b7280; font-size: 14px; margin-bottom: 4px;">Purpose</div>
            <div style="font-weight: 500;">{{ $visit->purpose }}</div>
        </div>
        @endif
    </div>

    <div style="background: #fef3c7; border: 1px solid #f59e0b; border-radius: 12px; padding: 16px; margin-bottom: 24px;">
        <div style="display: flex; align-items: flex-start; gap: 12px;">
            <svg style="width: 20px; height: 20px; flex-shrink: 0; color: #d97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div style="font-size: 14px; color: #92400e;">
                Please ensure you are available to greet your visitor at the scheduled time. You will be notified when they check in.
            </div>
        </div>
    </div>

    <div style="color: #6b7280; font-size: 14px; text-align: center;">
        <p style="margin: 0;">If you need to reschedule or cancel, please contact reception.</p>
    </div>
</body>
</html>