<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kiosk Check-In</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; color: #1a1a1a; min-height: 100vh; display: flex; flex-direction: column; }
        .kiosk-container { max-width: 800px; margin: 0 auto; padding: 2rem; flex: 1; display: flex; flex-direction: column; justify-content: center; }
        .header { text-align: center; margin-bottom: 3rem; }
        .logo { font-size: 1.5rem; font-weight: 600; color: #1a1a1a; margin-bottom: 0.5rem; }
        .header h1 { font-size: 2rem; font-weight: 700; color: #1a1a1a; }
        .card { background: #ffffff; border: 1px solid #e5e5e5; border-radius: 8px; padding: 2rem; }
        .mode-tabs { display: flex; gap: 0; margin-bottom: 2rem; border: 1px solid #e5e5e5; border-radius: 8px; overflow: hidden; }
        .mode-tab { flex: 1; padding: 1rem; border: none; background: #f5f5f5; color: #666; font-size: 1rem; font-weight: 500; cursor: pointer; transition: background 0.2s; }
        .mode-tab:hover { background: #e5e5e5; }
        .mode-tab.active { background: #1a1a1a; color: #fff; }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; font-size: 0.875rem; font-weight: 500; color: #666; margin-bottom: 0.5rem; }
        .form-group input { width: 100%; padding: 1rem; border: 1px solid #e5e5e5; border-radius: 6px; font-size: 1.125rem; }
        .form-group input:focus { outline: none; border-color: #1a1a1a; }
        .btn { display: inline-flex; align-items: center; justify-content: center; padding: 1rem 2rem; border: none; border-radius: 6px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        .btn-primary { width: 100%; background: #1a1a1a; color: #fff; }
        .btn-primary:hover { background: #333; }
        .btn-secondary { background: #f5f5f5; color: #1a1a1a; border: 1px solid #e5e5e5; }
        .error-message { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem; font-size: 0.875rem; }
        .visitor-info { text-align: center; }
        .visitor-avatar { width: 80px; height: 80px; background: #f5f5f5; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 2rem; font-weight: 600; color: #666; }
        .visitor-name { font-size: 1.5rem; font-weight: 700; color: #1a1a1a; margin-bottom: 0.5rem; }
        .visitor-company { font-size: 1rem; color: #666; margin-bottom: 2rem; }
        .visit-details { background: #f9f9f9; border-radius: 6px; padding: 1.5rem; margin-bottom: 2rem; text-align: left; }
        .detail-row { display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #e5e5e5; }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { color: #666; font-size: 0.875rem; }
        .detail-value { font-weight: 600; color: #1a1a1a; }
        .success-message { text-align: center; padding: 2rem; }
        .success-icon { width: 80px; height: 80px; background: #22c55e; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 2.5rem; color: #fff; }
        .success-title { font-size: 1.5rem; font-weight: 700; color: #1a1a1a; margin-bottom: 0.5rem; }
        .success-text { color: #666; margin-bottom: 2rem; }
        .returning-badge { display: inline-block; background: #e5e5e5; color: #666; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; margin-bottom: 1rem; }
        .btn-group { display: flex; gap: 1rem; }
        .btn-group .btn { flex: 1; }
    </style>
</head>
<body>
    @livewire('kiosk.check-in', ['tenantId' => $tenant?->id])
</body>
</html>
