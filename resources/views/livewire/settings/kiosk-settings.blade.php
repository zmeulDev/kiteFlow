<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-gray-900">Kiosk Settings</h1>
            <p class="text-sm text-gray-500">Configure the visitor check-in kiosk experience</p>
        </div>
    </div>

    <form wire:submit="save" class="space-y-6">
        <!-- Visitor Fields -->
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Required Visitor Information</h3>
            <p class="text-sm text-gray-500 mb-4">Configure which fields visitors must complete at check-in</p>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer">
                    <input type="checkbox" wire:model="require_first_name" class="w-4 h-4 text-[#FF4B4B] rounded">
                    <span class="text-sm text-gray-700">First Name</span>
                </label>
                <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer">
                    <input type="checkbox" wire:model="require_last_name" class="w-4 h-4 text-[#FF4B4B] rounded">
                    <span class="text-sm text-gray-700">Last Name</span>
                </label>
                <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer">
                    <input type="checkbox" wire:model="require_email" class="w-4 h-4 text-[#FF4B4B] rounded">
                    <span class="text-sm text-gray-700">Email</span>
                </label>
                <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer">
                    <input type="checkbox" wire:model="require_phone" class="w-4 h-4 text-[#FF4B4B] rounded">
                    <span class="text-sm text-gray-700">Phone</span>
                </label>
                <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer">
                    <input type="checkbox" wire:model="require_company" class="w-4 h-4 text-[#FF4B4B] rounded">
                    <span class="text-sm text-gray-700">Company</span>
                </label>
                <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer">
                    <input type="checkbox" wire:model="require_id_number" class="w-4 h-4 text-[#FF4B4B] rounded">
                    <span class="text-sm text-gray-700">ID Number</span>
                </label>
                <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer">
                    <input type="checkbox" wire:model="require_photo" class="w-4 h-4 text-[#FF4B4B] rounded">
                    <span class="text-sm text-gray-700">Photo</span>
                </label>
                <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer">
                    <input type="checkbox" wire:model="require_purpose" class="w-4 h-4 text-[#FF4B4B] rounded">
                    <span class="text-sm text-gray-700">Purpose of Visit</span>
                </label>
                <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer">
                    <input type="checkbox" wire:model="require_host" class="w-4 h-4 text-[#FF4B4B] rounded">
                    <span class="text-sm text-gray-700">Host</span>
                </label>
            </div>
        </div>

        <!-- Check-in Options -->
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Check-in Options</h3>
            
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <input type="checkbox" wire:model="allow_code_checkin" class="w-4 h-4 text-[#FF4B4B] rounded">
                    <div>
                        <p class="text-sm font-medium text-gray-700">Allow check-in with code</p>
                        <p class="text-xs text-gray-500">Visitors can enter a meeting code to check in</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <input type="checkbox" wire:model="allow_manual_checkin" class="w-4 h-4 text-[#FF4B4B] rounded">
                    <div>
                        <p class="text-sm font-medium text-gray-700">Allow manual check-in</p>
                        <p class="text-xs text-gray-500">Visitors can manually search for their host</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <input type="checkbox" wire:model="auto_select_host" class="w-4 h-4 text-[#FF4B4B] rounded">
                    <div>
                        <p class="text-sm font-medium text-gray-700">Auto-select host</p>
                        <p class="text-xs text-gray-500">Automatically select host when checking in with code</p>
                    </div>
                </div>
                
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Default Purpose</label>
                    <input type="text" wire:model="default_purpose" placeholder="e.g., Meeting, Interview, Delivery" 
                           class="w-full max-w-md px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                </div>
            </div>
        </div>

        <!-- GDPR & Terms -->
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">GDPR & Terms</h3>
            
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <input type="checkbox" wire:model="require_gdpr" class="w-4 h-4 text-[#FF4B4B] rounded">
                    <div>
                        <p class="text-sm font-medium text-gray-700">Require GDPR consent</p>
                        <p class="text-xs text-gray-500">Visitors must accept data processing terms</p>
                    </div>
                </div>
                
                @if($require_gdpr)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">GDPR Text</label>
                    <textarea wire:model="gdpr_text" rows="3" 
                              class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg"></textarea>
                </div>
                @endif
                
                <div class="flex items-center gap-3">
                    <input type="checkbox" wire:model="require_nda" class="w-4 h-4 text-[#FF4B4B] rounded">
                    <div>
                        <p class="text-sm font-medium text-gray-700">Require NDA/Confidentiality agreement</p>
                        <p class="text-xs text-gray-500">Visitors must sign confidentiality terms</p>
                    </div>
                </div>
                
                @if($require_nda)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NDA Text</label>
                    <textarea wire:model="nda_text" rows="3" 
                              class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg"></textarea>
                </div>
                @endif
            </div>
        </div>

        <!-- Notifications -->
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Notifications</h3>
            
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <input type="checkbox" wire:model="notify_host_email" class="w-4 h-4 text-[#FF4B4B] rounded">
                    <div>
                        <p class="text-sm font-medium text-gray-700">Email host when visitor checks in</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <input type="checkbox" wire:model="notify_host_sms" class="w-4 h-4 text-[#FF4B4B] rounded">
                    <div>
                        <p class="text-sm font-medium text-gray-700">SMS host when visitor checks in</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <input type="checkbox" wire:model="notify_reception_email" class="w-4 h-4 text-[#FF4B4B] rounded">
                    <div>
                        <p class="text-sm font-medium text-gray-700">Email reception with visitor details</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Display Options -->
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Display Options</h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Welcome Message</label>
                    <input type="text" wire:model="welcome_message" 
                           class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                </div>
                
                <div class="flex items-center gap-3">
                    <input type="checkbox" wire:model="show_company_branding" class="w-4 h-4 text-[#FF4B4B] rounded">
                    <div>
                        <p class="text-sm font-medium text-gray-700">Show company branding</p>
                        <p class="text-xs text-gray-500">Display company logo on kiosk screen</p>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Theme</label>
                    <select wire:model="theme" class="w-full max-w-xs px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                        <option value="light">Light</option>
                        <option value="dark">Dark</option>
                        <option value="auto">Auto (system)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end">
            <button type="submit" class="px-6 py-2.5 text-sm font-semibold text-white bg-[#FF4B4B] rounded-xl hover:bg-[#E63E3E] transition-colors">
                <i class="fa-solid fa-save mr-2"></i>Save Settings
            </button>
        </div>
    </form>
</div>