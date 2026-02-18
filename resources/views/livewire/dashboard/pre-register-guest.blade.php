<!-- projects/visiflow/resources/views/livewire/dashboard/pre-register-guest.blade.php -->
<div x-data="{ open: @entangle('showModal') }" x-on:openInviteModal.window="open = true">
    <div 
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-950/90"
        role="dialog"
        aria-modal="true"
        x-cloak
    >
        <div 
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            class="bg-white dark:bg-slate-900 rounded-[32px] w-full max-w-xl shadow-2xl border border-slate-200 dark:border-slate-800 flex flex-col"
            style="max-height: 90vh;"
            @click.away="open = false"
        >
            <header class="flex justify-between items-center p-8 pb-4 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <h3 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight uppercase italic">Invite <span class="text-indigo-600">Guest</span></h3>
                    <p class="text-slate-500 dark:text-slate-500 text-[10px] font-black uppercase tracking-widest mt-1">Create a verified fast pass</p>
                </div>
                <button @click="open = false" class="h-10 w-10 flex items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-rose-500 transition-all">✕</button>
            </header>

            <div class="flex-1 overflow-y-auto p-8 pt-6">
                <form wire:submit.prevent="submit" class="space-y-8">
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="first_name" class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-400 tracking-widest ml-1">First Name</label>
                            <input id="first_name" wire:model="first_name" type="text" class="w-full h-14 px-6 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-inner" placeholder="John" required>
                        </div>
                        <div class="space-y-2">
                            <label for="last_name" class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-400 tracking-widest ml-1">Last Name</label>
                            <input id="last_name" wire:model="last_name" type="text" class="w-full h-14 px-6 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-inner" placeholder="Doe" required>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="email" class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-400 tracking-widest ml-1">Email Address</label>
                        <input id="email" wire:model="email" type="email" class="w-full h-14 px-6 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-inner" placeholder="john@example.com" required>
                    </div>

                    @if(auth()->user()->tenant?->is_hub)
                        <div class="space-y-2">
                            <label for="target_tenant_id" class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-400 tracking-widest ml-1">Visiting Company</label>
                            <div class="relative">
                                <select id="target_tenant_id" wire:model="target_tenant_id" class="w-full h-14 pl-6 pr-10 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white font-black uppercase tracking-tight focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-inner appearance-none">
                                    <option value="">Main Office (Hub)</option>
                                    @foreach($subtenants as $sub)
                                        <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="expected_at" class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-400 tracking-widest ml-1">Expected At</label>
                            <input id="expected_at" wire:model.live="expected_at" type="datetime-local" class="w-full h-14 px-6 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-inner" required>
                        </div>
                        <div class="space-y-2">
                            <label for="visitor_count" class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-400 tracking-widest ml-1">Visitors Count</label>
                            <input id="visitor_count" wire:model.live="visitor_count" type="number" min="1" class="w-full h-14 px-6 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-inner">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="meeting_room_id" class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-400 tracking-widest ml-1">Assign Room</label>
                        <div class="relative">
                            <select id="meeting_room_id" wire:model="meeting_room_id" class="w-full h-14 pl-6 pr-10 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white font-black uppercase tracking-tight focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-inner appearance-none">
                                <option value="">No room assigned</option>
                                @foreach($availableRooms as $room)
                                    <option value="{{ $room->id }}">{{ $room->name }} @if($room->location) ({{ $room->location->name }}) @endif</option>
                                @endforeach
                            </select>
                            <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full h-16 bg-indigo-600 text-white rounded-2xl font-black uppercase tracking-[0.2em] text-xs shadow-xl shadow-indigo-600/20 hover:bg-indigo-700 transition-all active:scale-[0.98] border border-indigo-500" wire:loading.attr="disabled">
                        <span wire:loading.remove>Send Invitation ✉️</span>
                        <span wire:loading>Sending... ⏳</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
