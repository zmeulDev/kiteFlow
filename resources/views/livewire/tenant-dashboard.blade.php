<div>
    <style>
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
    <div class="layout" x-data="{ tab: $wire.entangle('tab') }">
        <div class="sidebar">
            <div class="sidebar-logo">{{ $tenant?->name }}</div>
            <nav>
                <a href="#" class="nav-item active" @click.prevent="tab = 'overview'">Overview</a>
                <a href="#" class="nav-item" @click.prevent="tab = 'visits'">Visits</a>
                <a href="#" class="nav-item" @click.prevent="tab = 'rooms'">Meeting Rooms</a>
                <a href="#" class="nav-item" @click.prevent="tab = 'buildings'">Buildings</a>
                <a href="#" class="nav-item" @click.prevent="tab = 'subtenants'">Sub-Tenants</a>
                <a href="#" class="nav-item" @click.prevent="tab = 'settings'">Settings</a>
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
                <button class="tab" :class="{ 'active': tab === 'overview' }" @click="tab = 'overview'">Overview</button>
                <button class="tab" :class="{ 'active': tab === 'visits' }" @click="tab = 'visits'">Visits</button>
                <button class="tab" :class="{ 'active': tab === 'rooms' }" @click="tab = 'rooms'">Rooms</button>
                <button class="tab" :class="{ 'active': tab === 'buildings' }" @click="tab = 'buildings'">Buildings</button>
                <button class="tab" :class="{ 'active': tab === 'subtenants' }" @click="tab = 'subtenants'">Sub-Tenants</button>
                <button class="tab" :class="{ 'active': tab === 'settings' }" @click="tab = 'settings'">Settings</button>
            </div>
            
            <!-- Overview Tab -->
            <div x-show="tab === 'overview'">
                <div class="card" style="margin-bottom: 2rem;">
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

                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Peak Visitor Hours (Last 30 Days)</div>
                    </div>
                    @if(empty($stats['peak_hours']))
                        <div style="text-align: center; color: #999; padding: 2rem 0;">No visit data available yet</div>
                    @else
                        <div style="display: flex; align-items: flex-end; height: 200px; gap: 4px; padding-top: 20px; border-bottom: 1px solid #e5e5e5;">
                            @php
                                $maxCount = max((array)$stats['peak_hours'] ?: [1]);
                                $maxCount = $maxCount > 0 ? $maxCount : 1; // Prevent division by zero
                            @endphp
                            @foreach($stats['peak_hours'] as $hour => $count)
                                <div style="flex: 1; display: flex; flex-direction: column; justify-content: flex-end; align-items: center; height: 100%;">
                                    <div style="width: 100%; text-align: center; font-size: 10px; color: #666; margin-bottom: 4px; display: {{ $count > 0 ? 'block' : 'none' }};">{{ $count }}</div>
                                    <div style="width: 80%; background: #1a1a1a; border-radius: 4px 4px 0 0; min-height: 1px; height: {{ ($count / $maxCount) * 100 }}%"></div>
                                </div>
                            @endforeach
                        </div>
                        <div style="display: flex; gap: 4px; margin-top: 8px;">
                            @foreach($stats['peak_hours'] as $hour => $count)
                                <div style="flex: 1; text-align: center; font-size: 11px; color: #666;">
                                    {{ explode(':', $hour)[0] }}h
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Rooms Tab -->
            <div x-show="tab === 'rooms'">
                <div class="card" style="margin-bottom: 2rem;">
                    <div class="card-header">
                        <div class="card-title">{{ $isEditingRoom ? 'Edit Room' : 'Add New Room' }}</div>
                        @if($isEditingRoom)
                            <button type="button" class="btn btn-secondary" wire:click="resetRoomForm">Cancel</button>
                        @endif
                    </div>
                    <form wire:submit.prevent="saveRoom">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Building</label>
                                <select wire:model="room_building_id">
                                    <option value="">Select Building</option>
                                    @foreach($buildings as $building)
                                        <option value="{{ $building->id }}">{{ $building->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" wire:model="room_name" required>
                            </div>
                            <div class="form-group">
                                <label>Capacity</label>
                                <input type="number" wire:model="room_capacity" min="1" required>
                            </div>
                            <div class="form-group">
                                <label>Floor (Optional)</label>
                                <input type="text" wire:model="room_floor">
                            </div>
                            <div class="form-group">
                                <label style="display: flex; align-items: center; gap: 8px;">
                                    <input type="checkbox" wire:model="room_is_active" style="width: auto;">
                                    <span>Active (Available for booking)</span>
                                </label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">{{ $isEditingRoom ? 'Update Room' : 'Add Room' }}</button>
                    </form>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Manage Rooms</div>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Building</th>
                                <th>Capacity</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rooms as $room)
                            <tr>
                                <td>{{ $room->name }}</td>
                                <td>{{ $room->building?->name ?? 'N/A' }}</td>
                                <td>{{ $room->capacity }}</td>
                                <td>
                                    <span class="badge badge-{{ $room->is_active ? 'success' : 'warning' }}">
                                        {{ $room->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-secondary" style="padding: 0.25rem 0.75rem; font-size: 0.75rem;" wire:click="editRoom({{ $room->id }})">Edit</button>
                                    <button class="btn btn-secondary" style="padding: 0.25rem 0.75rem; font-size: 0.75rem; color: #dc2626;" wire:click="deleteRoom({{ $room->id }})" onsubmit="return confirm('Are you sure?')">Delete</button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" style="text-align: center; color: #999;">No meeting rooms found</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Buildings Tab -->
            <div x-show="tab === 'buildings'">
                <div class="card" style="margin-bottom: 2rem;">
                    <div class="card-header">
                        <div class="card-title">{{ $isEditingBuilding ? 'Edit Building' : 'Add New Building' }}</div>
                        @if($isEditingBuilding)
                            <button type="button" class="btn btn-secondary" wire:click="resetBuildingForm">Cancel</button>
                        @endif
                    </div>
                    <form wire:submit.prevent="saveBuilding">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Building Name</label>
                                <input type="text" wire:model="building_name" required>
                            </div>
                            <div class="form-group full">
                                <label>Address Details (Optional)</label>
                                <textarea wire:model="building_address" rows="2"></textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">{{ $isEditingBuilding ? 'Update Building' : 'Add Building' }}</button>
                    </form>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Manage Buildings</div>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Address</th>
                                <th>Meeting Rooms</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($buildings as $building)
                            <tr>
                                <td>{{ $building->name }}</td>
                                <td>{{ Str::limit($building->address, 50) ?? '-' }}</td>
                                <td>{{ $building->meetingRooms()->count() }}</td>
                                <td>
                                    <button class="btn btn-secondary" style="padding: 0.25rem 0.75rem; font-size: 0.75rem;" wire:click="editBuilding({{ $building->id }})">Edit</button>
                                    <button class="btn btn-secondary" style="padding: 0.25rem 0.75rem; font-size: 0.75rem; color: #dc2626;" wire:click="deleteBuilding({{ $building->id }})" onsubmit="return confirm('Are you sure?')">Delete</button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" style="text-align: center; color: #999;">No buildings found</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- SubTenants Tab -->
            <div x-show="tab === 'subtenants'">
                <div class="card" style="margin-bottom: 2rem;">
                    <div class="card-header">
                        <div class="card-title">{{ $isEditingSubTenant ? 'Edit Sub-Tenant / Dept' : 'Add New Sub-Tenant / Dept' }}</div>
                        @if($isEditingSubTenant)
                            <button type="button" class="btn btn-secondary" wire:click="resetSubTenantForm">Cancel</button>
                        @endif
                    </div>
                    <form wire:submit.prevent="saveSubTenant">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" wire:model="subtenant_name" required>
                            </div>
                            <div class="form-group">
                                <label>Slug (URL identifier)</label>
                                <input type="text" wire:model="subtenant_slug" required>
                            </div>
                            <div class="form-group">
                                <label>Contact Person</label>
                                <input type="text" wire:model="subtenant_contact_person">
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" wire:model="subtenant_email">
                            </div>
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="tel" wire:model="subtenant_phone">
                            </div>
                            <div class="form-group">
                                <label style="display: flex; align-items: center; gap: 8px;">
                                    <input type="checkbox" wire:model="subtenant_is_active" style="width: auto;">
                                    <span>Active</span>
                                </label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">{{ $isEditingSubTenant ? 'Update Sub-Tenant' : 'Add Sub-Tenant' }}</button>
                    </form>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Manage Sub-Tenants</div>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Slug</th>
                                <th>Contact</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subtenants as $sub)
                            <tr>
                                <td>{{ $sub->name }}</td>
                                <td>{{ $sub->slug }}</td>
                                <td>{{ $sub->email }}</td>
                                <td>
                                    <span class="badge badge-{{ $sub->is_active ? 'success' : 'warning' }}">
                                        {{ $sub->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-secondary" style="padding: 0.25rem 0.75rem; font-size: 0.75rem;" wire:click="editSubTenant({{ $sub->id }})">Edit</button>
                                    <button class="btn btn-secondary" style="padding: 0.25rem 0.75rem; font-size: 0.75rem; color: #dc2626;" wire:click="deleteSubTenant({{ $sub->id }})" onsubmit="return confirm('Are you sure?')">Delete</button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" style="text-align: center; color: #999;">No sub-tenants found</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Settings Tab -->
            <div x-show="tab === 'settings'" class="card">
                <div class="card-header">
                    <div class="card-title">Tenant Settings</div>
                </div>
                @if(session()->has('message'))
                    <div style="background: #dcfce7; color: #166534; padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem;">
                        {{ session('message') }}
                    </div>
                @endif
                <form wire:submit.prevent="saveSettings">
                    <div class="form-grid">
                        <div class="form-group full">
                            <label>Company Logo</label>
                            @if ($tenant?->logo_path)
                                <div style="margin-bottom: 10px;">
                                    <img src="{{ Storage::url($tenant->logo_path) }}" alt="Logo" style="max-height: 50px;">
                                </div>
                            @endif
                            <input type="file" wire:model="logo" accept="image/*">
                            @error('logo') <span style="color: red; font-size: 0.75rem;">{{ $message }}</span> @enderror
                        </div>
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
</div>
