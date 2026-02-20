<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thank You for Visiting</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #2563eb; color: white; padding: 20px; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 20px; border: 1px solid #e5e7eb; }
        .footer { background: #1f2937; color: #9ca3af; padding: 15px; text-align: center; font-size: 12px; border-radius: 0 0 8px 8px; }
        .gdpr-box { background: #fef3c7; border: 1px solid #f59e0b; padding: 15px; border-radius: 6px; margin: 15px 0; }
        .detail { margin: 8px 0; }
        .label { font-weight: 600; color: #4b5563; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Thank You for Visiting {{ $tenant->name }} ✓</h1>
    </div>
    <div class="content">
        <p>Hi {{ $visitor->first_name }},</p>
        
        <p>Thank you for your visit today. Here are your visit details:</p>
        
        <div class="detail">
            <span class="label">Date:</span> {{ $visit->checked_in_at->format('F j, Y') }}
        </div>
        <div class="detail">
            <span class="label">Check-in Time:</span> {{ $visit->checked_in_at->format('g:i A') }}
        </div>
        @if($visit->checked_out_at)
        <div class="detail">
            <span class="label">Check-out Time:</span> {{ $visit->checked_out_at->format('g:i A') }}
        </div>
        @endif
        
        <!-- GDPR Notice -->
        <div class="gdpr-box">
            <strong>📋 Data Privacy Notice</strong>
            <p style="margin: 10px 0 0 0; font-size: 14px;">
                Your personal data collected during this visit will be retained for security purposes 
                in accordance with our data protection policy. 
                @if($tenant->gdpr_retention_months)
                    Data will be automatically purged after {{ $tenant->gdpr_retention_months }} months.
                @endif
                <br><br>
                If you have any questions about how we handle your data, please contact {{ $tenant->name }}'s data protection officer.
            </p>
        </div>
        
        @if($tenant->nda_text || $tenant->terms_text)
        <div style="margin-top: 15px;">
            <strong>📄 Legal Documents</strong>
            <p style="font-size: 14px;">
                @if($tenant->nda_text)
                    <br>NDA: {{ Str::limit($tenant->nda_text, 200) }}
                @endif
                @if($tenant->terms_text)
                    <br>Terms: {{ Str::limit($tenant->terms_text, 200) }}
                @endif
            </p>
        </div>
        @endif
    </div>
    <div class="footer">
        <p>{{ $tenant->name }}</p>
        @if($tenant->address)
            <p>{{ $tenant->address }}, {{ $tenant->city }}</p>
        @endif
    </div>
</body>
</html>
