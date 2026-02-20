<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Visit - KiteFlow</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; color: #1a1a1a; padding: 20px; }
        
        .container { max-width: 600px; margin: 0 auto; }
        
        .card { background: white; border: 1px solid #e5e5e5; border-radius: 8px; padding: 30px; }
        
        .header { margin-bottom: 30px; }
        .header h1 { font-size: 24px; font-weight: 600; }
        .header p { color: #666; margin-top: 5px; }
        
        .step-indicator { display: flex; margin-bottom: 30px; }
        .step { flex: 1; text-align: center; padding: 10px; color: #999; font-size: 13px; position: relative; }
        .step.active { color: #2563eb; font-weight: 600; }
        .step::after { content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 3px; background: #e5e5e5; }
        .step.active::after { background: #2563eb; }
        
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: #444; margin-bottom: 8px; text-transform: uppercase; }
        .form-input, .form-select { width: 100%; padding: 12px; font-size: 16px; border: 2px solid #e5e5e5; border-radius: 6px; background: #fafafa; }
        .form-input:focus, .form-select:focus { outline: none; border-color: #2563eb; background: white; }
        
        .row { display: flex; gap: 15px; }
        .col { flex: 1; }
        
        .btn { display: inline-flex; align-items: center; justify-content: center; padding: 14px 28px; font-size: 15px; font-weight: 600; border: none; border-radius: 6px; cursor: pointer; transition: background 0.2s; }
        .btn-primary { background: #2563eb; color: white; }
        .btn-primary:hover { background: #1d4ed8; }
        .btn-secondary { background: #f5f5f5; color: #444; border: 1px solid #e5e5e5; }
        .btn-group { display: flex; gap: 12px; margin-top: 25px; }
        
        .error-message { background: #fee2e2; color: #b91c1c; padding: 14px; border-radius: 6px; font-size: 14px; margin-bottom: 20px; border: 1px solid #fecaca; }
        
        .summary { background: #f5f5f5; border: 1px solid #e5e5e5; border-radius: 6px; padding: 20px; margin-bottom: 20px; }
        .summary-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e5e5e5; }
        .summary-row:last-child { border-bottom: none; }
        .summary-label { color: #666; font-size: 14px; }
        .summary-value { font-weight: 600; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <h1>{{ $mode === 'edit' ? 'Edit Visit' : 'Schedule a Visit' }}</h1>
                <p>Book a meeting room and schedule your visitor</p>
            </div>
            
            @if($error)
                <div class="error-message">{{ $error }}</div>
            @endif
            
            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step {{ $step === 'visitor' ? 'active' : '' }}">1. Visitor</div>
                <div class="step {{ $step === 'details' ? 'active' : '' }}">2. Details</div>
                <div class="step {{ $step === 'review' ? 'active' : '' }}">3. Review</div>
            </div>
            
            <!-- Step 1: Visitor Info -->
            @if($step === 'visitor')
                <div class="form-group">
                    <label class="form-label">First Name *</label>
                    <input type="text" class="form-input" wire:model="first_name" placeholder="John">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Last Name *</label>
                    <input type="text" class="form-input" wire:model="last_name" placeholder="Doe">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-input" wire:model="email" placeholder="john@company.com">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input type="tel" class="form-input" wire:model="phone" placeholder="+1 234 567 8900">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Company</label>
                    <input type="text" class="form-input" wire:model="company" placeholder="Acme Inc">
                </div>
                
                <button class="btn btn-primary" wire:click="nextStep">Next: Visit Details →</button>
            @endif
            
            <!-- Step 2: Visit Details -->
            @if($step === 'details')
                <div class="form-group">
                    <label class="form-label">Host</label>
                    <select class="form-select" wire:model="host_user_id">
                        <option value="">Select host (optional)</option>
                        @foreach($hosts as $host)
                            <option value="{{ $host['id'] }}">{{ $host['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                
                @if(count($subTenants) > 0)
                <div class="form-group">
                    <label class="form-label">Department</label>
                    <select class="form-select" wire:model="sub_tenant_id">
                        <option value="">Select department (optional)</option>
                        @foreach($subTenants as $sub)
                            <option value="{{ $sub['id'] }}">{{ $sub['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                
                <div class="form-group">
                    <label class="form-label">Date</label>
                    <input type="date" class="form-input" wire:model="scheduled_date">
                </div>
                
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label class="form-label">Start Time</label>
                            <input type="time" class="form-input" wire:model="scheduled_start_time">
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label class="form-label">End Time</label>
                            <input type="time" class="form-input" wire:model="scheduled_end_time">
                        </div>
                    </div>
                </div>
                
                @if(count($buildings) > 0)
                <div class="form-group">
                    <label class="form-label">Building</label>
                    <select class="form-select" wire:model="building_id">
                        <option value="">Select building (optional)</option>
                        @foreach($buildings as $building)
                            <option value="{{ $building['id'] }}">{{ $building['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                
                @if(count($meetingRooms) > 0)
                <div class="form-group">
                    <label class="form-label">Meeting Room</label>
                    <select class="form-select" wire:model="meeting_room_id">
                        <option value="">Select room (optional)</option>
                        @foreach($meetingRooms as $room)
                            <option value="{{ $room['id'] }}">{{ $room['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                
                <div class="form-group">
                    <label class="form-label">Purpose</label>
                    <input type="text" class="form-input" wire:model="purpose" placeholder="Business meeting, interview, etc.">
                </div>
                
                <div class="btn-group">
                    <button class="btn btn-secondary" wire:click="prevStep">← Back</button>
                    <button class="btn btn-primary" wire:click="nextStep">Next: Review →</button>
                </div>
            @endif
            
            <!-- Step 3: Review -->
            @if($step === 'review')
                <div class="summary">
                    <div class="summary-row">
                        <span class="summary-label">Visitor</span>
                        <span class="summary-value">{{ $first_name }} {{ $last_name }}</span>
                    </div>
                    @if($email)
                    <div class="summary-row">
                        <span class="summary-label">Email</span>
                        <span class="summary-value">{{ $email }}</span>
                    </div>
                    @endif
                    @if($company)
                    <div class="summary-row">
                        <span class="summary-label">Company</span>
                        <span class="summary-value">{{ $company }}</span>
                    </div>
                    @endif
                    <div class="summary-row">
                        <span class="summary-label">Date & Time</span>
                        <span class="summary-value">{{ $scheduled_date }} {{ $scheduled_start_time }} - {{ $scheduled_end_time }}</span>
                    </div>
                    @if($purpose)
                    <div class="summary-row">
                        <span class="summary-label">Purpose</span>
                        <span class="summary-value">{{ $purpose }}</span>
                    </div>
                    @endif
                </div>
                
                <div class="btn-group">
                    <button class="btn btn-secondary" wire:click="prevStep">← Back</button>
                    <button class="btn btn-primary" wire:click="save">Confirm & Send Invite</button>
                </div>
            @endif
        </div>
    </div>
    
    @livewireScripts
</body>
</html>
