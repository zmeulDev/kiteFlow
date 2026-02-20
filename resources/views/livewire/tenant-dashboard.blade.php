<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - {{ $tenant?->name }}</title>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; color: #1a1a1a; }
        
        .layout { display: flex; min-height: 100vh; }
        .sidebar { width: 240px; background: #1a1a1a; color: #fff; padding: 1.5rem; position: fixed; height: 100vh; }
        .sidebar-logo { font-size: 1.25rem; font-weight: 700; margin-bottom: 2rem; }
        .nav-item { display: block; padding: 0.75rem 1rem; color: #999; text-decoration: none; border-radius: 6px; margin-bottom: 0.25rem; transition: background 0.2s, color 0.2s; }
        .nav-item:hover { background: #333; color: #fff; }
        .nav-item.active { background: #333; color: #fff; }
        
        .main { flex: 1; margin-left: 240px; padding: 2rem; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .header h1 { font-size: 1.75rem; font-weight: 700; }
        
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
        .stat-card { background: #fff; border: 1px solid #e5e5e5; border-radius: 8px; padding: 1.5rem; }
        .stat-label { font-size: 0.875rem; color: #666; margin-bottom: 0.5rem; }
        .stat-value { font-size: 2rem; font-weight: 700; }
        
        .card { background: #fff; border: 1px solid #e5e5e5; border-radius: 8px; padding: 1.5rem; }
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
        .card-title { font-size: 1.125rem; font-weight: 600; }
        
        .tabs { display: flex; gap: 0; margin-bottom: 1.5rem; border: 1px solid #e5e5e5; border-radius: 6px; overflow: hidden; }
        .tab { padding: 0.75rem 1.5rem; border: none; background: #f5f5f5; color: #666; font-size: 0.875rem; font-weight: 500; cursor: pointer; }
        .tab:hover { background: #e5e5e5; }
        .tab.active { background: #1a1a1a; color: #fff; }
        
        .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; font-size: 0.875rem; font-weight: 500; color: #666; margin-bottom: 0.5rem; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 0.75rem; border: 1px solid #e5e5e5; border-radius: 6px; font-size: 0.875rem; }
        .form-group input:focus, .form-group textarea:focus { outline: none; border-color: #1a1a1a; }
        .form-group.full { grid-column: span 2; }
        
        .btn { display: inline-flex; align-items: center; justify-content: center; padding: 0.75rem 1.5rem; border: none; border-radius: 6px; font-size: 0.875rem; font-weight: 600; cursor: pointer; }
        .btn-primary { background: #1a1a1a; color: #fff; }
        .btn-primary:hover { background: #333; }
        
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 0.75rem 1rem; text-align: left; border-bottom: 1px solid #e5e5e5; font-size: 0.875rem; }
        th { font-weight: 600; color: #666; background: #f9f9f9; }
        tr:hover { background: #f9f9f9; }
        
        .badge { display: inline-block; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 500; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-info { background: #dbeafe; color: #1e40af; }
        
        @media (max-width: 768px) {
            .sidebar { display: none; }
            .main { margin-left: 0; }
            .form-grid { grid-template-columns: 1fr; }
            .form-group.full { grid-column: span 1; }
        }
    </style>
</head>
<body>
    <div class="layout">
        <div class="sidebar">
            <div class="sidebar-logo">{{ $tenant?->name }}</div>
            <nav>
                <a href="#" class="nav-item active" @click.prevent="$set('tab', 'overview')">Overview</a>
                <a href="#" class="nav-item" @click.prevent="$set('tab', 'visits')">Visits</a>
                <a href="#" class="nav-item" @click.prevent="$set('tab', 'rooms')">Meeting Rooms</a>
                <a href="#" class="nav-item" @click.prevent="$set('tab', 'buildings')">Buildings</a>
                <a href="#" class="nav-item" @click.prevent="$set('tab', 'settings')">Settings</a>
            </nav>
        </div>
        
        <div class="main">
            <div class="header">
                <h1>Dashboard</h1>
                <div>{{ now()->format('D, M j, Y') }}</div>
            </div>
            
            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Today's Visits</div>
                    <div class="stat-value">{{ $stats['total_visits_today'] ?? 0 }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Currently Checked In</div>
                    <div class="stat-value">{{ $stats['checked_in'] ?? 0 }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Total Visitors</div>
                    <div class="stat-value">{{ $stats['total_visitors'] ?? 0 }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Meeting Rooms</div>
                    <div class="stat-value">{{ $stats['total_rooms'] ?? 0 }}</div>
                </div>
            </div>
            
            <!-- Tabs -->
            <div class="tabs">
                <button class="tab" :class="{ 'active': tab === 'overview' }" @click="$set('tab', 'overview')">Overview</button>
                <button class="tab" :class="{ 'active': tab === 'visits' }" @click="$set('tab', 'visits')">Visits</button>
                <button class="tab" :class="{ 'active': tab === 'rooms' }" @click="$set('tab', 'rooms')">Rooms</button>
                <button class="tab" :class="{ 'active': tab === 'settings' }" @click="$set('tab', 'settings')">Settings</button>
            </div>
            
            <!-- Overview Tab -->
            <div x-show="tab === 'overview'" class="card">
                <div class="card-header">
                    <div class="card-title">Recent Visits</div>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Visitor</th>
                            <th>Host</th>
                            <th>Room</th>
                            <th>Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(\App\Models\Visit::where('tenant_id', $tenant?->id)->with(['visitor', 'hostUser', 'meetingRoom'])->latest()->limit(5)->get() as $visit)
                        <tr>
                            <td>{{ $visit->visitor->first_name }} {{ $visit->visitor->last_name }}</td>
                            <td>{{ $visit->hostUser?->name ?? '-' }}</td>
                            <td>{{ $visit->meetingRoom?->name ?? '-' }}</td>
                            <td>{{ $visit->scheduled_start->format('H:i') }}</td>
                            <td>
                                <span class="badge badge-{{ $visit->status === 'checked_in' ? 'success' : ($visit->status === 'pre_registered' ? 'info' : 'warning') }}">
                                    {{ $visit->status }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" style="text-align: center; color: #999;">No visits yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Settings Tab -->
            <div x-show="tab === 'settings'" class="card">
                <div class="card-header">
                    <div class="card-title">Tenant Settings</div>
                </div>
                <form wire:submit.prevent="saveSettings">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Company Name</label>
                            <input type="text" wire:model="name">
                        </div>
                        <div class="form-group">
                            <label>Contact Person</label>
                            <input type="text" wire:model="contact_person">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" wire:model="email">
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="tel" wire:model="phone">
                        </div>
                        <div class="form-group full">
                            <label>Address</label>
                            <input type="text" wire:model="address">
                        </div>
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" wire:model="city">
                        </div>
                        <div class="form-group">
                            <label>Country</label>
                            <input type="text" wire:model="country">
                        </div>
                        <div class="form-group">
                            <label>GDPR Retention (months)</label>
                            <input type="number" wire:model="gdpr_retention_months" min="1" max="36">
                        </div>
                        <div class="form-group full">
                            <label>NDA Text</label>
                            <textarea wire:model="nda_text" rows="3" placeholder="Custom NDA terms..."></textarea>
                        </div>
                        <div class="form-group full">
                            <label>Terms & Conditions</label>
                            <textarea wire:model="terms_text" rows="3" placeholder="Custom terms..."></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
