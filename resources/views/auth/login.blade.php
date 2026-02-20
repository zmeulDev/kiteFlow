<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - KiteFlow</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>KiteFlow</h1>
                <p>Visitor Management</p>
            </div>
            
            <form id="loginForm">
                <div class="error-box" id="error" style="display: none;"></div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" required autofocus>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required>
                </div>
                
                <button type="submit" class="btn-primary">Sign In</button>
            </form>
            
            <div class="login-footer">
                <a href="{{ route('kiosk') }}">← Back to Kiosk</a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('loginForm');
            if (loginForm) {
                loginForm.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    
                    const email = document.getElementById('email').value;
                    const password = document.getElementById('password').value;
                    const errorEl = document.getElementById('error');
                    
                    console.log('Login attempt:', email);
                    errorEl.style.display = 'none';
                    
                    try {
                        const response = await fetch('/login', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ email, password })
                        });
                        
                        console.log('Response status:', response.status);
                        
                        const data = await response.json();
                        console.log('Response data:', data);
                        
                        if (response.status === 200) {
                            // Login successful, redirect
                            window.location.href = data.redirect || '/dashboard';
                        } else {
                            errorEl.textContent = data.message || 'Login failed';
                            errorEl.style.display = 'block';
                        }
                    } catch (err) {
                        console.error('Login error:', err);
                        errorEl.textContent = 'Connection error';
                        errorEl.style.display = 'block';
                    }
                });
            }
        });
    </script>
</body>
</html>

<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
    .login-container { width: 100%; max-width: 400px; padding: 1rem; }
    .login-card { background: #fff; border: 1px solid #e5e5e5; border-radius: 8px; padding: 2rem; }
    .login-header { text-align: center; margin-bottom: 2rem; }
    .login-header h1 { font-size: 1.75rem; font-weight: 700; color: #1a1a1a; }
    .login-header p { color: #666; margin-top: 0.5rem; }
    .form-group { margin-bottom: 1.5rem; }
    .form-group label { display: block; font-size: 0.875rem; font-weight: 500; color: #666; margin-bottom: 0.5rem; }
    .form-group input { width: 100%; padding: 0.75rem; border: 1px solid #e5e5e5; border-radius: 6px; font-size: 1rem; }
    .form-group input:focus { outline: none; border-color: #1a1a1a; }
    .btn-primary { width: 100%; padding: 0.75rem; background: #1a1a1a; color: #fff; border: none; border-radius: 6px; font-size: 1rem; font-weight: 600; cursor: pointer; }
    .btn-primary:hover { background: #333; }
    .error-box { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 0.75rem; border-radius: 6px; margin-bottom: 1.5rem; font-size: 0.875rem; }
    .login-footer { text-align: center; margin-top: 1.5rem; }
    .login-footer a { color: #666; font-size: 0.875rem; text-decoration: none; }
    .login-footer a:hover { color: #1a1a1a; }
</style>
