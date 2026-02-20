<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - KiteFlow</title>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; }
        .layout { display: flex; min-height: 100vh; }
        .sidebar { width: 240px; background: #1a1a1a; color: #fff; padding: 1.5rem; position: fixed; height: 100vh; display: flex; flex-direction: column; }
        .sidebar-logo { font-size: 1.25rem; font-weight: 700; margin-bottom: 2rem; }
        .sidebar-nav { flex: 1; }
        .nav-item { display: block; padding: 0.75rem 1rem; color: #999; text-decoration: none; border-radius: 6px; margin-bottom: 0.25rem; cursor: pointer; }
        .nav-item:hover, .nav-item.active { background: #333; color: #fff; }
        .sidebar-footer { margin-top: auto; }
        .logout-btn { background: none; border: none; color: #999; cursor: pointer; padding: 0.75rem 1rem; width: 100%; text-align: left; border-radius: 6px; }
        .logout-btn:hover { background: #333; color: #fff; }
        
        .main-content { flex: 1; margin-left: 240px; padding: 2rem; }
        .header { background: #fff; border-bottom: 1px solid #e5e5e5; padding: 1.5rem 2rem; display: flex; justify-content: space-between; align-items: center; margin: -2rem -2rem 2rem -2rem; }
        .header h1 { font-size: 1.5rem; font-weight: 600; }
        .user-info { color: #666; }
        
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
        .stat-card { background: #fff; border: 1px solid #e5e5e5; border-radius: 8px; padding: 1.5rem; }
        .stat-label { font-size: 0.875rem; color: #666; margin-bottom: 0.5rem; }
        .stat-value { font-size: 2rem; font-weight: 700; color: #1a1a1a; }
        
        .card { background: #fff; border: 1px solid #e5e5e5; border-radius: 8px; overflow: hidden; margin-bottom: 2rem; }
        .card-header { padding: 1.5rem; border-bottom: 1px solid #e5e5e5; display: flex; justify-content: space-between; align-items: center; }
        .card-header h3 { font-size: 1.125rem; font-weight: 600; }
        .card-body { padding: 1.5rem; }
        
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 1rem 1.5rem; text-align: left; border-bottom: 1px solid #e5e5e5; }
        th { font-weight: 600; color: #666; font-size: 0.875rem; background: #f9f9f9; }
        tr:last-child td { border-bottom: none; }
        .badge { display: inline-block; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 500; }
        .badge-pre_registered { background: #dbeafe; color: #1e40af; }
        .badge-checked_in { background: #dcfce7; color: #166534; }
        .badge-checked_out { background: #f3f4f6; color: #6b7280; }
        .badge-cancelled { background: #fef3c7; color: #92400e; }
        
        .btn { padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.875rem; font-weight: 500; cursor: pointer; }
        .btn-primary { background: #1a1a1a; color: #fff; border: none; }
        .btn-secondary { background: #fff; color: #1a1a1a; border: 1px solid #e5e5e5; }
        .btn-sm { padding: 0.25rem 0.5rem; font-size: 0.75rem; }
        
        .form-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1rem; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; font-size: 0.875rem; font-weight: 500; color: #666; margin-bottom: 0.5rem; }
        .form-group input, .form-group select { width: 100%; padding: 0.75rem; border: 1px solid #e5e5e5; border-radius: 6px; font-size: 0.875rem; }
        
        .section { display: none; }
        .section.active { display: block; }
        
        .action-bar { display: flex; gap: 0.5rem; margin-bottom: 1rem; }
        .search-input { padding: 0.5rem 1rem; border: 1px solid #e5e5e5; border-radius: 6px; font-size: 0.875rem; width: 300px; }
        
        .empty-state { text-align: center; padding: 3rem; color: #999; }
    </style>
</head>
<body>
    <div class="layout">
        <aside class="sidebar">
            <div class="sidebar-logo">KiteFlow</div>
            <nav class="sidebar-nav">
                <a class="nav-item active" data-section="dashboard">Dashboard</a>
                <a class="nav-item" data-section="visits">Visits</a>
                <a class="nav-item" data-section="visitors">Visitors</a>
                <a class="nav-item" data-section="rooms">Rooms</a>
                <a class="nav-item" data-section="users">Users</a>
                <a class="nav-item" data-section="settings">Settings</a>
            </nav>
            <div class="sidebar-footer">
                <button class="logout-btn" onclick="logout()">Logout</button>
            </div>
        </aside>
        
        <main class="main-content">
            <header class="header">
                <h1 id="pageTitle">Dashboard</h1>
                <div class="user-info" id="userName">Loading...</div>
            </header>
            
            <!-- Dashboard Section -->
            <div id="dashboard" class="section active">
                <div class="stats-grid" id="statsGrid"></div>
                <div class="card">
                    <div class="card-header">
                        <h3>Today's Schedule</h3>
                    </div>
                    <div class="card-body">
                        <table>
                            <thead>
                                <tr><th>Time</th><th>Visitor</th><th>Host</th><th>Room</th><th>Status</th><th>Actions</th></tr>
                            </thead>
                            <tbody id="todayVisits"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Visits Section -->
            <div id="visits" class="section">
                <div class="card">
                    <div class="card-header">
                        <h3>All Visits</h3>
                        <button class="btn btn-primary btn-sm" onclick="showVisitForm()">+ New Visit</button>
                    </div>
                    <div class="card-body">
                        <table>
                            <thead>
                                <tr><th>Code</th><th>Visitor</th><th>Date</th><th>Host</th><th>Room</th><th>Status</th><th>Actions</th></tr>
                            </thead>
                            <tbody id="allVisits"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Visitors Section -->
            <div id="visitors" class="section">
                <div class="card">
                    <div class="card-header">
                        <h3>Visitors</h3>
                    </div>
                    <div class="card-body">
                        <table>
                            <thead>
                                <tr><th>Name</th><th>Email</th><th>Phone</th><th>Company</th><th>Last Visit</th></tr>
                            </thead>
                            <tbody id="allVisitors"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Rooms Section -->
            <div id="rooms" class="section">
                <div class="card">
                    <div class="card-header">
                        <h3>Meeting Rooms</h3>
                        <button class="btn btn-primary btn-sm" onclick="showRoomForm()">+ Add Room</button>
                    </div>
                    <div class="card-body">
                        <table>
                            <thead>
                                <tr><th>Name</th><th>Building</th><th>Capacity</th><th>Floor</th><th>Status</th></tr>
                            </thead>
                            <tbody id="allRooms"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Users Section -->
            <div id="users" class="section">
                <div class="card">
                    <div class="card-header">
                        <h3>Team Members</h3>
                        <button class="btn btn-primary btn-sm" onclick="showUserForm()">+ Add User</button>
                    </div>
                    <div class="card-body">
                        <table>
                            <thead>
                                <tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th></tr>
                            </thead>
                            <tbody id="allUsers"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Settings Section -->
            <div id="settings" class="section">
                <div class="card">
                    <div class="card-header">
                        <h3>Settings</h3>
                    </div>
                    <div class="card-body">
                        <form id="settingsForm">
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Company Name</label>
                                    <input type="text" name="company_name" id="companyName">
                                </div>
                                <div class="form-group">
                                    <label>Contact Person</label>
                                    <input type="text" name="contact_person" id="contactPerson">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" id="settingsEmail">
                                </div>
                                <div class="form-group">
                                    <label>Phone</label>
                                    <input type="tel" name="phone" id="settingsPhone">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>GDPR Retention (months)</label>
                                    <input type="number" name="gdpr_retention" id="gdprRetention" min="1" max="36">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Save Settings</button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        const API = '/api/v1';
        let token = localStorage.getItem('token');
        
        if (!token) window.location.href = '/login';
        axios.defaults.headers.common['Authorization'] = 'Bearer ' + token;
        
        // Navigation
        document.querySelectorAll('.nav-item').forEach(el => {
            el.addEventListener('click', () => {
                document.querySelectorAll('.nav-item').forEach(e => e.classList.remove('active'));
                el.classList.add('active');
                document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
                document.getElementById(el.dataset.section).classList.add('active');
                document.getElementById('pageTitle').textContent = el.textContent;
                loadSection(el.dataset.section);
            });
        });
        
        async function loadSection(section) {
            switch(section) {
                case 'dashboard': loadDashboard(); break;
                case 'visits': loadVisits(); break;
                case 'visitors': loadVisitors(); break;
                case 'rooms': loadRooms(); break;
                case 'users': loadUsers(); break;
                case 'settings': loadSettings(); break;
            }
        }
        
        async function loadDashboard() {
            const [stats, visits] = await Promise.all([
                axios.get(API + '/analytics/quick'),
                axios.get(API + '/visits?per_page=10')
            ]);
            
            const s = stats.data;
            document.getElementById('statsGrid').innerHTML = `
                <div class="stat-card"><div class="stat-label">Today's Visits</div><div class="stat-value">${s.today_visits}</div></div>
                <div class="stat-card"><div class="stat-label">Checked In</div><div class="stat-value">${s.checked_in}</div></div>
                <div class="stat-card"><div class="stat-label">Scheduled</div><div class="stat-value">${s.scheduled}</div></div>
                <div class="stat-card"><div class="stat-label">This Week</div><div class="stat-value">${s.this_week}</div></div>
            `;
            
            const today = new Date().toISOString().split('T')[0];
            const todayVisits = (visits.data.data || visits.data).filter(v => v.scheduled_start && v.scheduled_start.startsWith(today));
            
            if (todayVisits.length === 0) {
                document.getElementById('todayVisits').innerHTML = '<tr><td colspan="6" class="empty-state">No visits today</td></tr>';
            } else {
                document.getElementById('todayVisits').innerHTML = todayVisits.map(v => `
                    <tr>
                        <td>${v.scheduled_start ? v.scheduled_start.split('T')[1].substring(0,5) : '-'}</td>
                        <td>${v.visitor?.first_name || ''} ${v.visitor?.last_name || ''}</td>
                        <td>${v.host_user?.name || '-'}</td>
                        <td>${v.meeting_room?.name || '-'}</td>
                        <td><span class="badge badge-${v.status}">${v.status}</span></td>
                        <td>
                            ${v.status === 'pre_registered' ? `<button class="btn btn-secondary btn-sm" onclick="checkIn(${v.id})">Check In</button>` : ''}
                            ${v.status === 'checked_in' ? `<button class="btn btn-secondary btn-sm" onclick="checkOut(${v.id})">Check Out</button>` : ''}
                        </td>
                    </tr>
                `).join('');
            }
        }
        
        async function loadVisits() {
            const res = await axios.get(API + '/visits?per_page=50');
            const visits = res.data.data || res.data;
            
            if (visits.length === 0) {
                document.getElementById('allVisits').innerHTML = '<tr><td colspan="7" class="empty-state">No visits found</td></tr>';
            } else {
                document.getElementById('allVisits').innerHTML = visits.map(v => `
                    <tr>
                        <td><code>${v.visit_code}</code></td>
                        <td>${v.visitor?.first_name || ''} ${v.visitor?.last_name || ''}</td>
                        <td>${v.scheduled_start ? v.scheduled_start.split('T')[0] : '-'}</td>
                        <td>${v.host_user?.name || '-'}</td>
                        <td>${v.meeting_room?.name || '-'}</td>
                        <td><span class="badge badge-${v.status}">${v.status}</span></td>
                        <td>
                            ${v.status === 'pre_registered' ? `<button class="btn btn-secondary btn-sm" onclick="checkIn(${v.id})">In</button>` : ''}
                            ${v.status === 'checked_in' ? `<button class="btn btn-secondary btn-sm" onclick="checkOut(${v.id})">Out</button>` : ''}
                        </td>
                    </tr>
                `).join('');
            }
        }
        
        async function loadVisitors() {
            const res = await axios.get(API + '/visitors?per_page=50');
            const visitors = res.data.data || res.data;
            
            document.getElementById('allVisitors').innerHTML = visitors.length ? visitors.map(v => `
                <tr>
                    <td>${v.first_name} ${v.last_name}</td>
                    <td>${v.email || '-'}</td>
                    <td>${v.phone || '-'}</td>
                    <td>${v.company || '-'}</td>
                    <td>${v.last_visit_at ? v.last_visit_at.split('T')[0] : 'Never'}</td>
                </tr>
            `).join('') : '<tr><td colspan="5" class="empty-state">No visitors</td></tr>';
        }
        
        async function loadRooms() {
            const res = await axios.get(API + '/meeting-rooms?per_page=50');
            const rooms = res.data.data || res.data;
            
            document.getElementById('allRooms').innerHTML = rooms.length ? rooms.map(r => `
                <tr>
                    <td>${r.name}</td>
                    <td>${r.building?.name || '-'}</td>
                    <td>${r.capacity}</td>
                    <td>${r.floor || '-'}</td>
                    <td><span class="badge badge-${r.is_active ? 'checked_in' : 'checked_out'}">${r.is_active ? 'Active' : 'Inactive'}</span></td>
                </tr>
            `).join('') : '<tr><td colspan="5" class="empty-state">No rooms</td></tr>';
        }
        
        async function loadUsers() {
            const res = await axios.get(API + '/users?per_page=50');
            const users = res.data.data || res.data;
            
            document.getElementById('allUsers').innerHTML = users.length ? users.map(u => `
                <tr>
                    <td>${u.name}</td>
                    <td>${u.email}</td>
                    <td>${u.role}</td>
                    <td><span class="badge badge-${u.is_active ? 'checked_in' : 'checked_out'}">${u.is_active ? 'Active' : 'Inactive'}</span></td>
                </tr>
            `).join('') : '<tr><td colspan="4" class="empty-state">No users</td></tr>';
        }
        
        async function loadSettings() {
            const userRes = await axios.get(API + '/auth/me');
            const tenant = userRes.data.tenant;
            
            if (tenant) {
                document.getElementById('companyName').value = tenant.name || '';
                document.getElementById('contactPerson').value = tenant.contact_person || '';
                document.getElementById('settingsEmail').value = tenant.email || '';
                document.getElementById('settingsPhone').value = tenant.phone || '';
                document.getElementById('gdprRetention').value = tenant.gdpr_retention_months || 6;
            }
        }
        
        async function checkIn(visitId) {
            const userRes = await axios.get(API + '/auth/me');
            await axios.post(API + `/visits/${visitId}/check-in`, { checked_in_by: userRes.data.id });
            loadDashboard();
            loadVisits();
        }
        
        async function checkOut(visitId) {
            const userRes = await axios.get(API + '/auth/me');
            await axios.post(API + `/visits/${visitId}/check-out`, { checked_out_by: userRes.data.id });
            loadDashboard();
            loadVisits();
        }
        
        function showVisitForm() {
            alert('Visit form coming soon!');
        }
        
        function showRoomForm() {
            alert('Room form coming soon!');
        }
        
        function showUserForm() {
            alert('User form coming soon!');
        }
        
        document.getElementById('settingsForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            alert('Settings saved!');
        });
        
        async function logout() {
            try { await axios.post(API + '/auth/logout'); } catch(e) {}
            localStorage.removeItem('token');
            window.location.href = '/login';
        }
        
        // Init
        axios.get(API + '/auth/me').then(r => {
            document.getElementById('userName').textContent = r.data.name;
        }).catch(() => window.location.href = '/login');
        
        loadDashboard();
    </script>
</body>
</html>
