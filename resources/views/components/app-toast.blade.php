<div
    x-data="{
        notifications: [],
        add(data) {
            const id = Date.now();
            const notification = {
                id: id,
                type: data.type || 'success',
                message: data.message || '',
                show: false
            };
            this.notifications.push(notification);
            
            // Allow DOM to render before showing for transition
            this.$nextTick(() => {
                const index = this.notifications.findIndex(n => n.id === id);
                if (index !== -1) this.notifications[index].show = true;
            });

            setTimeout(() => {
                this.remove(id);
            }, 5000);
        },
        remove(id) {
            const index = this.notifications.findIndex(n => n.id === id);
            if (index !== -1) {
                this.notifications[index].show = false;
                setTimeout(() => {
                    this.notifications = this.notifications.filter(n => n.id !== id);
                }, 300);
            }
        }
    }"
    @notify.window="add($event.detail)"
    class="fixed bottom-4 right-4 z-[99] flex flex-col gap-3 pointer-events-none min-w-[320px] max-w-md"
>
    <template x-for="notification in notifications" :key="notification.id">
        <div
            x-show="notification.show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2 sm:translate-y-0 sm:translate-x-8"
            x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="bg-white dark:bg-slate-900 shadow-2xl rounded-xl pointer-events-auto border border-slate-200 dark:border-slate-800 overflow-hidden"
        >
            <div class="p-4 flex items-center gap-4">
                <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center"
                    :class="{
                        'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400': notification.type === 'success',
                        'bg-rose-50 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400': notification.type === 'error',
                        'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400': notification.type === 'warning'
                    }"
                >
                    <template x-if="notification.type === 'success'">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </template>
                    <template x-if="notification.type === 'error'">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </template>
                    <template x-if="notification.type === 'warning'">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </template>
                </div>
                
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-900 dark:text-white" x-text="notification.message"></p>
                </div>

                <button @click="remove(notification.id)" class="flex-shrink-0 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                    <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
            
            <div class="h-1 bg-slate-100 dark:bg-slate-800">
                <div class="h-full bg-current transition-all duration-[5000ms] ease-linear"
                    :style="{ width: notification.show ? '0%' : '100%' }"
                    :class="{
                        'text-indigo-500': notification.type === 'success',
                        'text-rose-500': notification.type === 'error',
                        'text-amber-500': notification.type === 'warning'
                    }"
                ></div>
            </div>
        </div>
    </template>
</div>
