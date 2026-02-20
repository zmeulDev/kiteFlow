<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Visitor Check-In</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { 
            height: 100%; 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f5f5;
            color: #1a1a1a;
        }
        
        /* Flat Design - NO shadows, NO glassmorphism */
        .kiosk-container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .kiosk-header {
            width: 100%;
            max-width: 500px;
            background: #2563eb;
            color: white;
            padding: 30px 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        
        .kiosk-logo {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .kiosk-subtitle {
            font-size: 16px;
            opacity: 0.9;
        }
        
        .kiosk-card {
            width: 100%;
            max-width: 500px;
            background: white;
            border: 1px solid #e5e5e5;
            border-top: none;
            border-radius: 0 0 8px 8px;
            padding: 30px;
        }
        
        /* Mode Tabs */
        .mode-tabs {
            display: flex;
            border: 1px solid #e5e5e5;
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 25px;
        }
        
        .mode-tab {
            flex: 1;
            padding: 14px;
            border: none;
            background: #f5f5f5;
            color: #666;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
        }
        
        .mode-tab.active {
            background: #2563eb;
            color: white;
        }
        
        .mode-tab:hover:not(.active) {
            background: #e5e5e5;
        }
        
        /* Forms */
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #444;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .form-input {
            width: 100%;
            padding: 16px;
            font-size: 18px;
            border: 2px solid #e5e5e5;
            border-radius: 6px;
            background: #fafafa;
            transition: border-color 0.2s;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #2563eb;
            background: white;
        }
        
        .form-input::placeholder {
            color: #999;
        }
        
        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 16px 32px;
            font-size: 16px;
            font-weight: 600;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.2s;
            width: 100%;
        }
        
        .btn-primary {
            background: #2563eb;
            color: white;
        }
        
        .btn-primary:hover {
            background: #1d4ed8;
        }
        
        .btn-secondary {
            background: #f5f5f5;
            color: #444;
            border: 1px solid #e5e5e5;
        }
        
        .btn-secondary:hover {
            background: #e5e5e5;
        }
        
        .btn-group {
            display: flex;
            gap: 12px;
            margin-top: 20px;
        }
        
        /* Messages */
        .error-message {
            background: #fee2e2;
            color: #b91c1c;
            padding: 14px;
            border-radius: 6px;
            font-size: 14px;
            margin-bottom: 20px;
            border: 1px solid #fecaca;
        }
        
        .success-message {
            text-align: center;
            padding: 20px;
        }
        
        .success-icon {
            width: 80px;
            height: 80px;
            background: #22c55e;
            color: white;
            font-size: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        
        /* Visitor Info */
        .visitor-info {
            text-align: center;
        }
        
        .returning-badge {
            display: inline-block;
            background: #22c55e;
            color: white;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .visitor-avatar {
            width: 80px;
            height: 80px;
            background: #e5e5e5;
            color: #666;
            font-size: 28px;
            font-weight: 600;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
        }
        
        .visitor-name {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .visitor-company {
            color: #666;
            font-size: 16px;
            margin-bottom: 25px;
        }
        
        /* Visit Details Box */
        .visit-details {
            background: #f5f5f5;
            border: 1px solid #e5e5e5;
            border-radius: 6px;
            padding: 20px;
            text-align: left;
            margin-bottom: 25px;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e5e5e5;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            color: #666;
            font-size: 14px;
        }
        
        .detail-value {
            font-weight: 600;
            font-size: 14px;
        }
        
        /* Signature Pad */
        .signature-pad {
            width: 100%;
            height: 150px;
            border: 2px solid #e5e5e5;
            border-radius: 6px;
            background: white;
            margin-bottom: 15px;
        }
        
        /* Checkboxes */
        .checkbox-group {
            margin: 20px 0;
        }
        
        .checkbox-label {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 12px;
            background: #f9f9f9;
            border: 1px solid #e5e5e5;
            border-radius: 6px;
            margin-bottom: 10px;
            cursor: pointer;
        }
        
        .checkbox-label input {
            margin-top: 3px;
        }
        
        .checkbox-text {
            font-size: 14px;
            line-height: 1.4;
        }
        
        /* QR Scanner placeholder */
        .qr-scanner {
            width: 100%;
            height: 250px;
            background: #1a1a1a;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            margin-bottom: 20px;
        }
        
        /* Success */
        .success-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .success-text {
            color: #666;
            font-size: 16px;
            margin-bottom: 25px;
            line-height: 1.5;
        }
        
        /* NDA/Terms Text */
        .legal-text {
            font-size: 12px;
            color: #666;
            line-height: 1.4;
            max-height: 80px;
            overflow-y: auto;
            background: #f9f9f9;
            padding: 10px;
            border-radius: 4px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="kiosk-container">
        <div class="kiosk-header">
            <div class="kiosk-logo">{{ $tenant?->name ?? 'Visitor Management' }}</div>
            <div class="kiosk-subtitle">Self Check-In Kiosk</div>
        </div>
        
        <div class="kiosk-card">
            @if($error)
                <div class="error-message">{{ $error }}</div>
            @endif
            
            @if($step === 'search')
                <!-- Mode Tabs -->
                <div class="mode-tabs">
                    <button 
                        type="button"
                        class="mode-tab {{ $mode === 'code' ? 'active' : '' }}"
                        wire:click="$set('mode', 'code')"
                    >Code</button>
                    <button 
                        type="button"
                        class="mode-tab {{ $mode === 'qr' ? 'active' : '' }}"
                        wire:click="$set('mode', 'qr')"
                    >QR Scan</button>
                    <button 
                        type="button"
                        class="mode-tab {{ $mode === 'manual' ? 'active' : '' }}"
                        wire:click="$set('mode', 'manual')"
                    >Email/Phone</button>
                </div>
                
                @if($mode === 'code')
                    <div class="form-group">
                        <label class="form-label">Enter Your Visit Code</label>
                        <input 
                            type="text" 
                            class="form-input"
                            wire:model="visitCode"
                            placeholder="e.g. ABC12345"
                            style="text-transform: uppercase; letter-spacing: 0.2em; text-align: center; font-size: 22px;"
                            wire:keydown.enter="searchByCode"
                        >
                    </div>
                    <button class="btn btn-primary" wire:click="searchByCode">
                        Find My Visit
                    </button>
                    
                @elseif($mode === 'qr')
                    <div class="qr-scanner" id="qr-scanner">
                        <span>📷 Point camera at QR code</span>
                    </div>
                    <p style="text-align: center; color: #666; font-size: 14px; margin-bottom: 20px;">
                        Position the QR code on your confirmation within the frame
                    </p>
                    
                @else
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input 
                            type="email" 
                            class="form-input"
                            wire:model="email"
                            placeholder="your@email.com"
                        >
                    </div>
                    <div class="form-group">
                        <label class="form-label">Or Phone Number</label>
                        <input 
                            type="tel" 
                            class="form-input"
                            wire:model="phone"
                            placeholder="+1 234 567 8900"
                        >
                    </div>
                    <button class="btn btn-primary" wire:click="searchByContact">
                        Find My Visit
                    </button>
                @endif
            
            @elseif($step === 'sign')
                <!-- Signature & NDA Step -->
                <div style="text-align: center; margin-bottom: 25px;">
                    <h3 style="font-size: 20px; margin-bottom: 8px;">Sign In</h3>
                    <p style="color: #666; font-size: 14px;">Please sign below and accept terms</p>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Your Signature</label>
                    <canvas id="signature-canvas" class="signature-pad"></canvas>
                    <button type="button" onclick="clearSignature()" style="font-size: 13px; color: #666; background: none; border: none; cursor: pointer; text-decoration: underline;">
                        Clear Signature
                    </button>
                    <input type="hidden" wire:model="signatureData" id="signature-data">
                </div>
                
                @if($tenant?->nda_text)
                <div class="checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" wire:model="agreedToNda">
                        <span class="checkbox-text">
                            I have read and agree to the <strong>NDA</strong><br>
                            <span class="legal-text">{{ $tenant->nda_text }}</span>
                        </span>
                    </label>
                </div>
                @endif
                
                @if($tenant?->terms_text)
                <div class="checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" wire:model="agreedToTerms">
                        <span class="checkbox-text">
                            I accept the <strong>Terms & Conditions</strong><br>
                            <span class="legal-text">{{ $tenant->terms_text }}</span>
                        </span>
                    </label>
                </div>
                @endif
                
                <div class="btn-group">
                    <button class="btn btn-secondary" wire:click="resetForm" style="flex: 1;">Back</button>
                    <button class="btn btn-primary" wire:click="saveSignature" style="flex: 1;">Continue</button>
                </div>
            
            @elseif($step === 'details')
                <!-- Visit Details Confirmation -->
                <div class="visitor-info">
                    @if($isReturningVisitor)
                        <span class="returning-badge">Welcome Back!</span>
                    @endif
                    
                    <div class="visitor-avatar">
                        {{ strtoupper(substr($visitor->first_name ?? '', 0, 1) . substr($visitor->last_name ?? '', 0, 1)) }}
                    </div>
                    
                    <div class="visitor-name">
                        {{ $visitor->first_name }} {{ $visitor->last_name }}
                    </div>
                    
                    @if($visitor->company)
                        <div class="visitor-company">{{ $visitor->company }}</div>
                    @endif
                    
                    @if($visit)
                        <div class="visit-details">
                            @if($visit->hostUser)
                            <div class="detail-row">
                                <span class="detail-label">Host</span>
                                <span class="detail-value">{{ $visit->hostUser->name }}</span>
                            </div>
                            @endif
                            @if($visit->meetingRoom)
                            <div class="detail-row">
                                <span class="detail-label">Location</span>
                                <span class="detail-value">{{ $visit->meetingRoom->name }}</span>
                            </div>
                            @endif
                            <div class="detail-row">
                                <span class="detail-label">Time</span>
                                <span class="detail-value">{{ $visit->scheduled_start->format('H:i') }} - {{ $visit->scheduled_end->format('H:i') }}</span>
                            </div>
                            @if($visit->purpose)
                            <div class="detail-row">
                                <span class="detail-label">Purpose</span>
                                <span class="detail-value">{{ $visit->purpose }}</span>
                            </div>
                            @endif
                        </div>
                    @endif
                    
                    <div class="btn-group">
                        <button class="btn btn-secondary" wire:click="resetForm" style="flex: 1;">Back</button>
                        <button class="btn btn-primary" wire:click="performCheckIn" style="flex: 1;">Check In</button>
                    </div>
                </div>
            
            @elseif($step === 'success')
                <!-- Success Screen -->
                <div class="success-message">
                    <div class="success-icon">✓</div>
                    <div class="success-title">Checked In!</div>
                    <div class="success-text">
                        @if($visit?->meetingRoom)
                            Please proceed to <strong>{{ $visit->meetingRoom->name }}</strong><br>
                        @endif
                        Your host has been notified.
                    </div>
                    <button class="btn btn-primary" wire:click="resetForm">Done</button>
                </div>
            @endif
        </div>
    </div>
    
    <script>
        // Signature Pad Logic
        let canvas, ctx, isDrawing = false;
        
        document.addEventListener('livewire:load', function() {
            initSignaturePad();
        });
        
        function initSignaturePad() {
            canvas = document.getElementById('signature-canvas');
            if (!canvas) return;
            
            ctx = canvas.getContext('2d');
            
            // Set canvas size
            canvas.width = canvas.offsetWidth;
            canvas.height = canvas.offsetHeight;
            
            // Drawing events
            canvas.addEventListener('mousedown', startDraw);
            canvas.addEventListener('mousemove', draw);
            canvas.addEventListener('mouseup', stopDraw);
            canvas.addEventListener('mouseout', stopDraw);
            
            // Touch events
            canvas.addEventListener('touchstart', handleTouch);
            canvas.addEventListener('touchmove', handleTouch);
            canvas.addEventListener('touchend', stopDraw);
        }
        
        function startDraw(e) {
            isDrawing = true;
            ctx.beginPath();
            ctx.moveTo(e.offsetX, e.offsetY);
        }
        
        function draw(e) {
            if (!isDrawing) return;
            ctx.lineTo(e.offsetX, e.offsetY);
            ctx.stroke();
        }
        
        function stopDraw() {
            if (isDrawing) {
                isDrawing = false;
                // Save signature data
                const dataInput = document.getElementById('signature-data');
                if (dataInput) {
                    dataInput.value = canvas.toDataURL('image/png');
                    @this.set('signatureData', canvas.toDataURL('image/png'));
                }
            }
        }
        
        function handleTouch(e) {
            e.preventDefault();
            const touch = e.touches[0];
            const rect = canvas.getBoundingClientRect();
            const x = touch.clientX - rect.left;
            const y = touch.clientY - rect.top;
            
            if (e.type === 'touchstart') {
                isDrawing = true;
                ctx.beginPath();
                ctx.moveTo(x, y);
            } else if (e.type === 'touchmove' && isDrawing) {
                ctx.lineTo(x, y);
                ctx.stroke();
            }
        }
        
        function clearSignature() {
            if (!ctx || !canvas) return;
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            const dataInput = document.getElementById('signature-data');
            if (dataInput) {
                dataInput.value = '';
                @this.set('signatureData', '');
            }
        }
    </script>
    
    @livewireScripts
</body>
</html>
