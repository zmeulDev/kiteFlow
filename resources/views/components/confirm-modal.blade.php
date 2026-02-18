<div
    x-data="{
        show: false,
        title: '',
        message: '',
        confirmText: 'Confirm',
        cancelText: 'Cancel',
        onConfirm: null,
        variant: 'danger',
        
        open(data) {
            this.title = data.title || 'Are you sure?';
            this.message = data.message || 'This action cannot be undone.';
            this.confirmText = data.confirmText || 'Confirm';
            this.cancelText = data.cancelText || 'Cancel';
            this.onConfirm = data.onConfirm || null;
            this.variant = data.variant || 'danger';
            this.show = true;
        },
        confirm() {
            if (this.onConfirm) {
                // If onConfirm is a string that looks like a Livewire call, we can't easily eval it here 
                // because we are outside the component.
                // Better approach: dispatch a custom event that the original component listens for,
                // or if it's a simple string, treat it as an event name.
                
                if (typeof this.onConfirm === 'string') {
                    if (this.onConfirm.includes('(')) {
                        // Likely a JS expression
                        try {
                            const func = new Function(this.onConfirm);
                            func();
                        } catch (e) {
                            console.error('Confirm modal error:', e);
                        }
                    } else {
                        // Likely an event name
                        window.dispatchEvent(new CustomEvent(this.onConfirm));
                    }
                } else if (typeof this.onConfirm === 'function') {
                    this.onConfirm();
                }
            }
            this.show = false;
        }
    }"
    @confirm.window="open($event.detail)"
    x-show="show"
    class="fixed inset-0 z-[100] overflow-y-auto"
    x-cloak
>
    <!-- Backdrop -->
    <div x-show="show" 
         x-transition:enter="transition ease-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="transition ease-in duration-200" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="show = false"></div>

    <!-- Modal -->
    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div 
            x-show="show" 
            x-transition:enter="transition ease-out duration-300" 
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
            x-transition:leave="transition ease-in duration-200" 
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative transform overflow-hidden rounded-3xl bg-white dark:bg-slate-900 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-200 dark:border-slate-800"
        >
            <div class="px-8 pt-8 pb-6">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl sm:mx-0"
                        :class="{
                            'bg-rose-50 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400': variant === 'danger',
                            'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400': variant === 'warning',
                            'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400': variant === 'info'
                        }"
                    >
                        <template x-if="variant === 'danger'">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                            </svg>
                        </template>
                        <template x-if="variant === 'warning'">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </template>
                        <template x-if="variant === 'info'">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                            </svg>
                        </template>
                    </div>
                    <div class="mt-4 text-center sm:ml-6 sm:mt-0 sm:text-left">
                        <h3 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight" x-text="title"></h3>
                        <div class="mt-3">
                            <p class="text-base text-slate-500 dark:text-slate-400 leading-relaxed font-medium" x-text="message"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-slate-50 dark:bg-slate-800/50 px-8 py-6 flex flex-row-reverse gap-3">
                <button @click="confirm()" type="button" 
                    class="inline-flex w-full justify-center rounded-2xl px-8 py-4 text-base font-black text-white shadow-xl transition-all sm:w-auto"
                    :class="{
                        'bg-rose-600 hover:bg-rose-500 shadow-rose-500/20': variant === 'danger',
                        'bg-amber-600 hover:bg-amber-500 shadow-amber-500/20': variant === 'warning',
                        'bg-indigo-600 hover:bg-indigo-500 shadow-indigo-500/20': variant === 'info'
                    }"
                    x-text="confirmText"
                ></button>
                <button @click="show = false" type="button" 
                    class="inline-flex w-full justify-center rounded-2xl bg-white dark:bg-slate-900 px-8 py-4 text-base font-black text-slate-700 dark:text-slate-200 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 sm:w-auto transition-all" 
                    x-text="cancelText"
                ></button>
            </div>
        </div>
    </div>
</div>
