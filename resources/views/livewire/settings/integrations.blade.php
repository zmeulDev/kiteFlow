<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-gray-900">Integrations</h1>
            <p class="text-sm text-gray-500">Connect with external services and APIs</p>
        </div>
    </div>

    <form wire:submit="save" class="space-y-6">
        <!-- Calendar Integrations -->
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fa-brands fa-google mr-2 text-[#FF4B4B]"></i>Calendar Integrations
            </h3>
            
            <!-- Google Calendar -->
            <div class="mb-6 pb-6 border-b border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center">
                            <i class="fa-brands fa-google text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Google Calendar</p>
                            <p class="text-xs text-gray-500">Sync meetings with Google Calendar</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="google_enabled" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#FF4B4B]/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#FF4B4B]"></div>
                    </label>
                </div>
                
                @if($google_enabled)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pl-13">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Client ID</label>
                        <input type="text" wire:model="google_client_id" placeholder="Your Google Client ID"
                               class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Client Secret</label>
                        <input type="password" wire:model="google_client_secret" placeholder="Your Google Client Secret"
                               class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                    </div>
                    <div class="md:col-span-2 flex items-center gap-3">
                        <input type="checkbox" wire:model="google_auto_sync" class="w-4 h-4 text-[#FF4B4B] rounded">
                        <span class="text-sm text-gray-600">Auto-sync meetings to Google Calendar</span>
                    </div>
                </div>
                @endif
            </div>
            
            <!-- Microsoft Outlook -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center">
                            <i class="fa-brands fa-microsoft text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Microsoft Outlook</p>
                            <p class="text-xs text-gray-500">Sync meetings with Outlook Calendar</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="microsoft_enabled" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#FF4B4B]/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#FF4B4B]"></div>
                    </label>
                </div>
                
                @if($microsoft_enabled)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pl-13">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Client ID</label>
                        <input type="text" wire:model="microsoft_client_id" placeholder="Your Microsoft Client ID"
                               class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Client Secret</label>
                        <input type="password" wire:model="microsoft_client_secret" placeholder="Your Microsoft Client Secret"
                               class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Notifications -->
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fa-brands fa-slack mr-2 text-[#FF4B4B]"></i>Notifications
            </h3>
            
            <!-- Slack -->
            <div class="mb-6 pb-6 border-b border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center">
                            <i class="fa-brands fa-slack text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Slack</p>
                            <p class="text-xs text-gray-500">Send notifications to Slack channels</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="slack_enabled" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#FF4B4B]/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#FF4B4B]"></div>
                    </label>
                </div>
                
                @if($slack_enabled)
                <div class="space-y-4 pl-13">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Webhook URL</label>
                        <input type="url" wire:model="slack_webhook_url" placeholder="https://hooks.slack.com/services/..."
                               class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Channel</label>
                        <input type="text" wire:model="slack_channel" placeholder="#visitors"
                               class="w-full max-w-xs px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" wire:model="slack_notify_checkin" class="w-4 h-4 text-[#FF4B4B] rounded">
                            <span class="text-sm text-gray-600">Notify on visitor check-in</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" wire:model="slack_notify_meeting" class="w-4 h-4 text-[#FF4B4B] rounded">
                            <span class="text-sm text-gray-600">Notify on new meeting</span>
                        </label>
                    </div>
                </div>
                @endif
            </div>
            
            <!-- Zapier -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center">
                            <i class="fa-solid fa-bolt text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Zapier</p>
                            <p class="text-xs text-gray-500">Connect to 5000+ apps via Zapier</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="zapier_enabled" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#FF4B4B]/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#FF4B4B]"></div>
                    </label>
                </div>
                
                @if($zapier_enabled)
                <div class="pl-13">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Webhook URL</label>
                        <input type="url" wire:model="zapier_webhook_url" placeholder="https://hooks.zapier.com/..."
                               class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Access Control -->
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fa-solid fa-id-card mr-2 text-[#FF4B4B]"></i>Access Control Systems
            </h3>
            
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm font-medium text-gray-900">Enable Access Control Integration</p>
                    <p class="text-xs text-gray-500">Connect with physical access control systems</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" wire:model="access_control_enabled" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#FF4B4B]/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#FF4B4B]"></div>
                </label>
            </div>
            
            @if($access_control_enabled)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Provider</label>
                    <select wire:model="access_control_type" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                        <option value="none">Select provider...</option>
                        <option value="hid">HID</option>
                        <option value="assa">ASSA ABLOY</option>
                        <option value="custom">Custom API</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">API Key</label>
                    <input type="password" wire:model="access_control_api_key" placeholder="Your API key"
                           class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">API Endpoint</label>
                    <input type="url" wire:model="access_control_endpoint" placeholder="https://api.example.com/access"
                           class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                </div>
            </div>
            @endif
        </div>

        <!-- Custom Webhooks -->
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fa-solid fa-webhook mr-2 text-[#FF4B4B]"></i>Custom Webhooks
                </h3>
                <button type="button" wire:click="addWebhook" class="px-4 py-2 text-sm font-semibold text-white bg-[#FF4B4B] rounded-lg hover:bg-[#E63E3E]">
                    <i class="fa-solid fa-plus mr-2"></i>Add Webhook
                </button>
            </div>
            
            @if(count($webhooks) > 0)
                <div class="space-y-4">
                    @foreach($webhooks as $index => $webhook)
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-medium text-gray-700">Webhook #{{ $index + 1 }}</span>
                                <button type="button" wire:click="removeWebhook({{ $index }})" class="text-red-500 hover:text-red-700">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div class="md:col-span-2">
                                    <label class="block text-xs text-gray-500 mb-1">URL</label>
                                    <input type="url" wire:model="webhooks.{{ $index }}.url" placeholder="https://..."
                                           class="w-full px-3 py-2 text-sm bg-white border border-gray-200 rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Event</label>
                                    <select wire:model="webhooks.{{ $index }}.event" class="w-full px-3 py-2 text-sm bg-white border border-gray-200 rounded-lg">
                                        <option value="visitor.checkin">Visitor Check-in</option>
                                        <option value="visitor.checkout">Visitor Check-out</option>
                                        <option value="meeting.start">Meeting Start</option>
                                        <option value="meeting.end">Meeting End</option>
                                        <option value="meeting.create">Meeting Created</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500 text-center py-4">No webhooks configured. Click "Add Webhook" to create one.</p>
            @endif
        </div>

        <!-- Save -->
        <div class="flex justify-end">
            <button type="submit" class="px-6 py-2.5 text-sm font-semibold text-white bg-[#FF4B4B] rounded-xl hover:bg-[#E63E3E] transition-colors">
                <i class="fa-solid fa-save mr-2"></i>Save Settings
            </button>
        </div>
    </form>
</div>