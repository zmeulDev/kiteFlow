<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-gray-900">System Settings</h1>
            <p class="text-sm text-gray-500">Configure global system security and settings</p>
        </div>
    </div>

    <form wire:submit="save" class="space-y-6">
        <!-- Security -->
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fa-solid fa-shield-halved mr-2 text-[#FF4B4B]"></i>Security
            </h3>
            
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="text-sm font-medium text-gray-700">Require Two-Factor Authentication</p>
                        <p class="text-xs text-gray-500">Force all users to enable 2FA</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="require_2fa" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#FF4B4B]/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#FF4B4B]"></div>
                    </label>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password Min Length</label>
                        <input type="number" wire:model="password_min_length" min="6" max="32" 
                               class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Session Timeout (minutes)</label>
                        <input type="number" wire:model="session_timeout" min="5" max="480" 
                               class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Max Login Attempts</label>
                        <input type="number" wire:model="max_login_attempts" min="3" max="10" 
                               class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                    </div>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" wire:model="password_require_uppercase" class="w-4 h-4 text-[#FF4B4B] rounded">
                        <span class="text-sm text-gray-600">Uppercase letter</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" wire:model="password_require_numbers" class="w-4 h-4 text-[#FF4B4B] rounded">
                        <span class="text-sm text-gray-600">Numbers</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" wire:model="password_require_special" class="w-4 h-4 text-[#FF4B4B] rounded">
                        <span class="text-sm text-gray-600">Special characters</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Data Retention -->
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fa-solid fa-database mr-2 text-[#FF4B4B]"></i>Data Retention
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Visitor Data (days)</label>
                    <input type="number" wire:model="visitor_data_retention" min="30" max="2555" 
                           class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                    <p class="text-xs text-gray-500 mt-1">How long to keep visitor records</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Meeting Data (days)</label>
                    <input type="number" wire:model="meeting_data_retention" min="30" max="2555" 
                           class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Log Data (days)</label>
                    <input type="number" wire:model="log_data_retention" min="7" max="1095" 
                           class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                </div>
            </div>
            
            <div class="mt-4 flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div>
                    <p class="text-sm font-medium text-gray-700">Auto-delete blacklisted visitors</p>
                    <p class="text-xs text-gray-500">Permanently remove after retention period</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" wire:model="auto_delete_blacklisted" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#FF4B4B]/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#FF4B4B]"></div>
                </label>
            </div>
        </div>

        <!-- Notifications -->
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fa-solid fa-bell mr-2 text-[#FF4B4B]"></i>Notifications
            </h3>
            
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="text-sm font-medium text-gray-700">Email Notifications</p>
                        <p class="text-xs text-gray-500">Receive system notifications via email</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="email_notifications" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#FF4B4B]/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#FF4B4B]"></div>
                    </label>
                </div>
                
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="text-sm font-medium text-gray-700">Notify on new user registration</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="notify_admin_new_user" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#FF4B4B]/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#FF4B4B]"></div>
                    </label>
                </div>
                
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="text-sm font-medium text-gray-700">Notify on new tenant</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="notify_admin_new_tenant" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#FF4B4B]/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#FF4B4B]"></div>
                    </label>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notification Email</label>
                    <input type="email" wire:model="notification_email" placeholder="admin@example.com"
                           class="w-full max-w-md px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                </div>
            </div>
        </div>

        <!-- Features -->
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fa-solid fa-toggle-on mr-2 text-[#FF4B4B]"></i>Features
            </h3>
            
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="text-sm font-medium text-gray-700">Enable Kiosk Mode</p>
                        <p class="text-xs text-gray-500">Allow tenants to use self-service kiosks</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="enable_kiosk" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#FF4B4B]/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#FF4B4B]"></div>
                    </label>
                </div>
                
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="text-sm font-medium text-gray-700">Enable Parking Management</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="enable_parking" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#FF4B4B]/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#FF4B4B]"></div>
                    </label>
                </div>
                
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="text-sm font-medium text-gray-700">Enable Catering</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="enable_catering" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#FF4B4B]/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#FF4B4B]"></div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Branding -->
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fa-solid fa-palette mr-2 text-[#FF4B4B]"></i>Branding
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Application Name</label>
                    <input type="text" wire:model="app_name" 
                           class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Application URL</label>
                    <input type="url" wire:model="app_url" placeholder="https://kiteflow.app"
                           class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                </div>
            </div>
            
            <div class="mt-4 flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div>
                    <p class="text-sm font-medium text-gray-700">Allow Tenant Branding</p>
                    <p class="text-xs text-gray-500">Tenants can customize their logo and colors</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" wire:model="allow_tenant_branding" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#FF4B4B]/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#FF4B4B]"></div>
                </label>
            </div>
        </div>

        <!-- Save -->
        <div class="flex justify-end">
            <button type="submit" class="px-6 py-2.5 text-sm font-semibold text-white bg-[#FF4B4B] rounded-xl hover:bg-[#E63E3E] transition-colors">
                <i class="fa-solid fa-save mr-2"></i>Save Settings
            </button>
        </div>
    </form>
</div>