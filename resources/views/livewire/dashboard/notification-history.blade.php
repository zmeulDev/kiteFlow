<!-- resources/views/livewire/dashboard/notification-history.blade.php -->
<div class="space-y-8 animate-fade-in-up">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h3 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight uppercase italic">Notification <span class="text-indigo-600">Center</span></h3>
            <p class="text-[10px] text-slate-400 dark:text-slate-500 font-black uppercase tracking-widest mt-1">System alerts & updates</p>
        </div>
        
        @if(auth()->user()->unreadNotifications->count() > 0)
            <button wire:click="markAllAsRead" class="h-12 px-6 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-2xl font-black uppercase tracking-widest text-[10px] hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition-all active:scale-95 shadow-sm border border-indigo-100 dark:border-indigo-800">
                Mark All Read
            </button>
        @endif
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-[32px] border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden p-8">
        <div class="space-y-4">
            @forelse($notifications as $notification)
                <div wire:key="notification-{{ $notification->id }}" class="group relative flex items-start gap-6 p-6 rounded-[24px] transition-all duration-300 {{ $notification->read_at ? 'bg-slate-50 dark:bg-slate-900/50 border border-transparent' : 'bg-white dark:bg-slate-900 border border-indigo-100 dark:border-indigo-900/50 shadow-lg shadow-indigo-500/5' }}">
                    <div class="flex-shrink-0">
                        <div class="h-14 w-14 rounded-2xl flex items-center justify-center text-2xl shadow-inner {{ $notification->read_at ? 'bg-slate-200 dark:bg-slate-800 text-slate-400' : 'bg-indigo-600 text-white shadow-indigo-500/30' }}">
                            @if(str_contains($notification->type, 'VisitorArrived'))
                                👋
                            @elseif(str_contains($notification->type, 'SecurityAlert'))
                                ⚠️
                            @else
                                🔔
                            @endif
                        </div>
                    </div>

                    <div class="flex-1 min-w-0 pt-1">
                        <div class="flex items-center justify-between gap-4 mb-2">
                            <h4 class="font-bold text-slate-900 dark:text-white truncate {{ $notification->read_at ? 'text-slate-600 dark:text-slate-400' : '' }}">
                                {{ $notification->data['title'] ?? 'System Update' }}
                            </h4>
                            <span class="text-[9px] font-black uppercase tracking-widest text-slate-400 whitespace-nowrap">{{ $notification->created_at->diffForHumans() }}</span>
                        </div>
                        
                        <p class="text-sm font-medium leading-relaxed mb-4 {{ $notification->read_at ? 'text-slate-500 dark:text-slate-500' : 'text-slate-600 dark:text-slate-300' }}">
                            {{ $notification->data['message'] ?? 'A new visitor has arrived at the reception.' }}
                        </p>

                        <div class="flex items-center gap-4 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-2 group-hover:translate-y-0">
                            @if(!$notification->read_at)
                                <button wire:click="markAsRead('{{ $notification->id }}')" class="text-[10px] font-black uppercase tracking-widest text-indigo-600 hover:text-indigo-700 transition-colors flex items-center gap-1">
                                    <span>Mark Read</span>
                                </button>
                            @endif
                            <button wire:click="deleteNotification('{{ $notification->id }}')" class="text-[10px] font-black uppercase tracking-widest text-rose-500 hover:text-rose-600 transition-colors flex items-center gap-1">
                                <span>Remove</span>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-20 text-center">
                    <div class="w-24 h-24 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl grayscale opacity-50">📭</div>
                    <h3 class="text-xl font-black text-slate-900 dark:text-white uppercase italic">All Caught Up</h3>
                    <p class="text-slate-500 dark:text-slate-400 text-sm font-medium mt-2">No new notifications to display</p>
                </div>
            @endforelse
        </div>
    </div>

    <div class="mt-8 flex justify-center">
        {{ $notifications->links() }}
    </div>
</div>
