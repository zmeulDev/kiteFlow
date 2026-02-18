<!-- projects/visiflow/resources/views/livewire/kiosk/check-in.blade.php -->
<div class="flex flex-col justify-center space-y-8 w-full max-w-md mx-auto">
    <header>
        <h3 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">{{ __('messages.check_in') }}</h3>
        <p class="text-slate-500 dark:text-slate-400 text-sm font-medium mt-1">Step {{ $currentStep }} of {{ ($selectedTenantModel?->settings['require_photo'] ?? false) ? '2' : '1' }}</p>
    </header>

    @if($currentStep === 1)
        <form wire:submit.prevent="nextStep" class="space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-500 tracking-widest" for="first_name">{{ __('messages.first_name') }}</label>
                    <input wire:model="first_name" id="first_name" type="text" class="flex h-14 w-full rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 px-4 py-2 text-sm font-bold dark:text-white transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500" placeholder="John" required>
                    @error('first_name') <span class="text-rose-500 text-[10px] font-bold">{{ $message }}</span> @enderror
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-500 tracking-widest" for="last_name">{{ __('messages.last_name') }}</label>
                    <input wire:model="last_name" id="last_name" type="text" class="flex h-14 w-full rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 px-4 py-2 text-sm font-bold dark:text-white transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500" placeholder="Doe" required>
                    @error('last_name') <span class="text-rose-500 text-[10px] font-bold">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-500 tracking-widest" for="company">{{ __('messages.company') }}</label>
                <select wire:model.live="selected_company" id="company" class="flex h-14 w-full rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 px-4 py-2 text-sm font-bold dark:text-white transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500" required>
                    @if($tenant->is_hub)
                        <option value="">Select a company...</option>
                    @endif
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                    @endforeach
                </select>
                @error('selected_company') <span class="text-rose-500 text-[10px] font-bold">{{ $message }}</span> @enderror
            </div>

            @if($locations->count() > 0)
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-500 tracking-widest" for="location">Reception / Desk</label>
                    <select wire:model="selected_location" id="location" class="flex h-14 w-full rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 px-4 py-2 text-sm font-bold dark:text-white transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                        <option value="">Main Entrance</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-500 tracking-widest" for="purpose">{{ __('messages.purpose') }}</label>
                <textarea wire:model="purpose" id="purpose" class="flex min-h-[100px] w-full rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 px-4 py-3 text-sm font-bold dark:text-white transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500" placeholder="Meeting purpose" required></textarea>
                @error('purpose') <span class="text-rose-500 text-[10px] font-bold">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center space-x-3 rounded-2xl border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 p-5">
                <input type="checkbox" id="terms" class="h-6 w-6 rounded border-slate-300 dark:bg-slate-950 text-indigo-600 focus:ring-indigo-500 transition-all" required>
                <label for="terms" class="text-xs font-medium text-slate-600 dark:text-slate-400">
                    {{ __('messages.agree_terms') }} <a href="#" class="text-indigo-600 font-bold underline">Safety Policy</a>.
                </label>
            </div>

            <button type="submit" class="inline-flex h-16 w-full items-center justify-center rounded-2xl bg-indigo-600 text-lg font-black uppercase tracking-widest text-white shadow-xl shadow-indigo-100 dark:shadow-none transition-all duration-300 hover:bg-indigo-700 hover:shadow-indigo-200 active:scale-95">
                {{ ($selectedTenantModel?->settings['require_photo'] ?? false) ? 'Next: Snap Photo' : __('messages.complete_checkin') }}
            </button>
        </form>
    @else
        <div class="space-y-6" x-data="{
            stream: null,
            init() {
                navigator.mediaDevices.getUserMedia({ video: true })
                    .then(s => {
                        this.stream = s;
                        $refs.video.srcObject = s;
                    })
                    .catch(e => console.error('Camera error:', e));
            },
            capture() {
                let canvas = document.createElement('canvas');
                canvas.width = $refs.video.videoWidth;
                canvas.height = $refs.video.videoHeight;
                canvas.getContext('2d').drawImage($refs.video, 0, 0);
                let data = canvas.toDataURL('image/png');
                @this.set('photo', data);
                if(this.stream) this.stream.getTracks().forEach(track => track.stop());
                @this.submit();
            }
        }">
            <div class="relative overflow-hidden rounded-[32px] bg-black aspect-video flex items-center justify-center shadow-2xl">
                <video x-ref="video" autoplay playsinline class="w-full h-full object-cover"></video>
                <div class="absolute inset-0 border-4 border-dashed border-white/20 rounded-[32px] pointer-events-none m-4"></div>
            </div>
            <p class="text-center text-sm font-medium text-slate-500">Please look at the camera and click the button below.</p>
            <button @click="capture" class="inline-flex h-16 w-full items-center justify-center rounded-2xl bg-indigo-600 text-lg font-black uppercase tracking-widest text-white shadow-xl shadow-indigo-100 dark:shadow-none transition-all hover:bg-indigo-700 active:scale-[0.98]">
                📸 Capture & Check-in
            </button>
            <button wire:click="$set('currentStep', 1)" class="w-full text-xs font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 transition-colors">
                Back to details
            </button>
        </div>
    @endif
</div>
