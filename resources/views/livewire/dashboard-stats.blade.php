<div class="dashboard">
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Today's Visits</div>
            <div class="stat-value">{{ $stats['today_total'] ?? 0 }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Checked In</div>
            <div class="stat-value">{{ $stats['checked_in'] ?? 0 }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Checked Out</div>
            <div class="stat-value">{{ $stats['checked_out'] ?? 0 }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Visitors</div>
            <div class="stat-value">{{ $stats['total_visitors'] ?? 0 }}</div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Today's Schedule</h3>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Visitor</th>
                    <th>Host</th>
                    <th>Room</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($todayVisits as $visit)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($visit['scheduled_start'])->format('H:i') }}</td>
                    <td>{{ $visit['visitor']['first_name'] }} {{ $visit['visitor']['last_name'] }}</td>
                    <td>{{ $visit['host_user']['name'] ?? '-' }}</td>
                    <td>{{ $visit['meeting_room']['name'] ?? '-' }}</td>
                    <td>
                        <span class="badge badge-{{ $visit['status'] }}">
                            {{ $visit['status'] }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center">No visits scheduled for today</td></tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<style>
    .dashboard { padding: 2rem; max-width: 1400px; margin: 0 auto; }
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
    .stat-card { background: #fff; border: 1px solid #e5e5e5; border-radius: 8px; padding: 1.5rem; }
    .stat-label { font-size: 0.875rem; color: #666; margin-bottom: 0.5rem; }
    .stat-value { font-size: 2rem; font-weight: 700; color: #1a1a1a; }
    .card { background: #fff; border: 1px solid #e5e5e5; border-radius: 8px; overflow: hidden; }
    .card-header { padding: 1.5rem; border-bottom: 1px solid #e5e5e5; }
    .card-header h3 { margin: 0; font-size: 1.125rem; font-weight: 600; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 1rem 1.5rem; text-align: left; border-bottom: 1px solid #e5e5e5; }
    th { font-weight: 600; color: #666; font-size: 0.875rem; background: #f9f9f9; }
    tr:last-child td { border-bottom: none; }
    .badge { display: inline-block; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 500; }
    .badge-pre_registered { background: #dbeafe; color: #1e40af; }
    .badge-checked_in { background: #dcfce7; color: #166534; }
    .badge-checked_out { background: #f3f4f6; color: #6b7280; }
    .text-center { text-align: center; color: #999; }
</style>
