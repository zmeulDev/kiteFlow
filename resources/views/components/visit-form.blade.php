<div class="card">
    <div class="card-header">
        <h3>Schedule New Visit</h3>
    </div>
    <form id="visitForm" class="visit-form">
        <div class="form-section">
            <h4>Visitor Information</h4>
            <div class="form-row">
                <div class="form-group">
                    <label>First Name *</label>
                    <input type="text" name="first_name" required>
                </div>
                <div class="form-group">
                    <label>Last Name *</label>
                    <input type="text" name="last_name" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email">
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="tel" name="phone">
                </div>
            </div>
            <div class="form-group">
                <label>Company</label>
                <input type="text" name="company">
            </div>
        </div>

        <div class="form-section">
            <h4>Visit Details</h4>
            <div class="form-row">
                <div class="form-group">
                    <label>Host</label>
                    <select name="host_user_id" id="hostSelect">
                        <option value="">Select host...</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Building</label>
                    <select name="building_id" id="buildingSelect" onchange="loadRooms()">
                        <option value="">Select building...</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Meeting Room</label>
                    <select name="meeting_room_id" id="roomSelect">
                        <option value="">Select room...</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Purpose</label>
                    <input type="text" name="purpose" placeholder="Meeting, Interview, etc.">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Date *</label>
                    <input type="date" name="scheduled_date" required>
                </div>
                <div class="form-group">
                    <label>Start Time *</label>
                    <input type="time" name="scheduled_start" required>
                </div>
                <div class="form-group">
                    <label>End Time *</label>
                    <input type="time" name="scheduled_end" required>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary">Schedule Visit</button>
        </div>
    </form>
</div>

<style>
    .visit-form { padding: 1.5rem; }
    .form-section { margin-bottom: 2rem; }
    .form-section h4 { font-size: 0.875rem; font-weight: 600; color: #666; margin-bottom: 1rem; text-transform: uppercase; letter-spacing: 0.5px; }
    .form-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 1rem; }
    .form-group { margin-bottom: 1rem; }
    .form-group label { display: block; font-size: 0.875rem; font-weight: 500; color: #666; margin-bottom: 0.5rem; }
    .form-group input, .form-group select { width: 100%; padding: 0.75rem; border: 1px solid #e5e5e5; border-radius: 6px; font-size: 0.875rem; }
    .form-group input:focus, .form-group select:focus { outline: none; border-color: #1a1a1a; }
    .form-actions { display: flex; justify-content: flex-end; gap: 1rem; }
    .btn-primary { padding: 0.75rem 1.5rem; background: #1a1a1a; color: #fff; border: none; border-radius: 6px; font-size: 0.875rem; font-weight: 600; cursor: pointer; }
    .btn-primary:hover { background: #333; }
    .btn-secondary { padding: 0.75rem 1.5rem; background: #fff; color: #1a1a1a; border: 1px solid #e5e5e5; border-radius: 6px; font-size: 0.875rem; font-weight: 600; cursor: pointer; }
    .success-message { background: #dcfce7; border: 1px solid #86efac; color: #166534; padding: 1rem; border-radius: 6px; margin-bottom: 1rem; }
</style>

<script>
async function loadFormOptions() {
    const token = localStorage.getItem('token');
    
    // Load hosts
    const usersRes = await fetch('/api/v1/users', { headers: { 'Authorization': 'Bearer ' + token } });
    const users = await usersRes.json();
    const hostSelect = document.getElementById('hostSelect');
    (users.data || users).forEach(u => {
        hostSelect.innerHTML += `<option value="${u.id}">${u.name}</option>`;
    });
    
    // Load buildings
    const bldgRes = await fetch('/api/v1/buildings', { headers: { 'Authorization': 'Bearer ' + token } });
    const buildings = await bldgRes.json();
    const bldgSelect = document.getElementById('buildingSelect');
    (buildings.data || buildings).forEach(b => {
        bldgSelect.innerHTML += `<option value="${b.id}">${b.name}</option>`;
    });
    
    // Set default date
    document.querySelector('input[name="scheduled_date"]').value = new Date().toISOString().split('T')[0];
}

async function loadRooms() {
    const buildingId = document.getElementById('buildingSelect').value;
    const token = localStorage.getItem('token');
    const roomSelect = document.getElementById('roomSelect');
    
    roomSelect.innerHTML = '<option value="">Select room...</option>';
    
    if (!buildingId) return;
    
    const res = await fetch(`/api/v1/meeting-rooms?building_id=${buildingId}`, { 
        headers: { 'Authorization': 'Bearer ' + token } 
    });
    const rooms = await res.json();
    (rooms.data || rooms).forEach(r => {
        roomSelect.innerHTML += `<option value="${r.id}">${r.name} (${r.capacity} ppl)</option>`;
    });
}

document.getElementById('visitForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    const token = localStorage.getItem('token');
    
    const data = {
        visitor: {
            first_name: formData.get('first_name'),
            last_name: formData.get('last_name'),
            email: formData.get('email'),
            phone: formData.get('phone'),
            company: formData.get('company'),
        },
        host_user_id: formData.get('host_user_id') || null,
        building_id: formData.get('building_id') || null,
        meeting_room_id: formData.get('meeting_room_id') || null,
        purpose: formData.get('purpose'),
        scheduled_start: `${formData.get('scheduled_date')}T${formData.get('scheduled_start')}`,
        scheduled_end: `${formData.get('scheduled_date')}T${formData.get('scheduled_end')}`,
    };
    
    try {
        const res = await fetch('/api/v1/visits', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + token,
            },
            body: JSON.stringify(data)
        });
        
        const result = await res.json();
        
        if (res.ok) {
            alert('Visit scheduled successfully!');
            form.reset();
            document.querySelector('input[name="scheduled_date"]').value = new Date().toISOString().split('T')[0];
            loadDashboard();
        } else {
            alert('Error: ' + (result.message || 'Failed to schedule visit'));
        }
    } catch (err) {
        alert('Error scheduling visit');
    }
});

loadFormOptions();
</script>
