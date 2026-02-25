<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Visit Invitation</title>
</head>
<body style="font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #f9fafb; border-radius: 12px; padding: 32px; margin-bottom: 24px;">
        <h1 style="margin: 0 0 8px 0; font-size: 24px; font-weight: 600; color: #111827;">Visit Invitation</h1>
        <p style="margin: 0; color: #6b7280;">You have been invited for a scheduled visit.</p>
    </div>

    <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px; margin-bottom: 24px;">
        <h2 style="margin: 0 0 16px 0; font-size: 18px; font-weight: 600;">Visit Details</h2>

        <div style="margin-bottom: 16px;">
            <div style="color: #6b7280; font-size: 14px; margin-bottom: 4px;">Date & Time</div>
            <div style="font-weight: 500;">{{ $scheduledAt?->format('l, F j, Y \a\t g:i A') ?? 'To be confirmed' }}</div>
        </div>

        @if($building)
        <div style="margin-bottom: 16px;">
            <div style="color: #6b7280; font-size: 14px; margin-bottom: 4px;">Location</div>
            <div style="font-weight: 500;">{{ $entrance?->name ?? 'Main Entrance' }}</div>
            <div style="color: #6b7280;">{{ $building->name }}</div>
        </div>
        @endif

        @if($host)
        <div style="margin-bottom: 16px;">
            <div style="color: #6b7280; font-size: 14px; margin-bottom: 4px;">Your Host</div>
            <div style="font-weight: 500;">{{ $host->name }}</div>
            <div style="color: #6b7280;">{{ $host->email }}</div>
        </div>
        @endif

        @if($visit->purpose)
        <div style="margin-bottom: 16px;">
            <div style="color: #6b7280; font-size: 14px; margin-bottom: 4px;">Purpose</div>
            <div style="font-weight: 500;">{{ $visit->purpose }}</div>
        </div>
        @endif
    </div>

    <div style="background: #eff6ff; border: 2px solid #3b82f6; border-radius: 12px; padding: 24px; text-align: center; margin-bottom: 24px;">
        <div style="color: #6b7280; font-size: 14px; margin-bottom: 8px;">Your Check-In Code</div>
        <div style="font-size: 36px; font-weight: 700; letter-spacing: 8px; color: #1d4ed8;">{{ $checkInCode }}</div>
        <p style="margin: 12px 0 0 0; color: #6b7280; font-size: 14px;">Present this code at reception to check in</p>
    </div>

    <div style="color: #6b7280; font-size: 14px; text-align: center;">
        <p style="margin: 0;">If you have any questions, please contact your host directly.</p>
    </div>
</body>
</html>