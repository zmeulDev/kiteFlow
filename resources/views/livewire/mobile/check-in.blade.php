<div class="max-w-lg mx-auto py-6">
    @if($completed)
    <div class="card text-center">
        <div class="w-16 h-16 mx-auto mb-4 icon-container icon-container--green">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <h2 class="text-2xl font-bold mb-2">Check-in Complete!</h2>
        <p class="text-secondary mb-4">Thank you, {{ $visit->visitor->first_name }}! You have been successfully checked in.</p>
        <div class="bg-main rounded-lg p-4 text-left">
            <p class="text-sm text-secondary"><strong class="text-primary">Host:</strong> {{ $visit->host_name }}</p>
            <p class="text-sm text-secondary"><strong class="text-primary">Location:</strong> {{ $visit->entrance->name }}</p>
            <p class="text-sm text-secondary"><strong class="text-primary">Building:</strong> {{ $visit->entrance->building->name }}</p>
        </div>
        <p class="text-sm text-muted mt-4">You can now close this page.</p>
    </div>
    @else
    <div class="card">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold">Visitor Check-in</h1>
            <p class="text-secondary">{{ $visit->entrance->building->name }} - {{ $visit->entrance->name }}</p>
        </div>

        <!-- Progress -->
        <div class="mb-6">
            <div class="flex justify-between text-xs text-secondary mb-1">
                <span>Step {{ $step }} of 4</span>
            </div>
            <div class="progress-bar">
                <div class="progress-bar-fill" style="width: {{ ($step / 4) * 100 }}%"></div>
            </div>
        </div>

        @if($step === 1)
        <h2 class="text-lg font-semibold mb-4">Your Details</h2>
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-3">
                <div class="input-group">
                    <x-input-label for="mobile_first_name" value="First Name *" />
                    <input type="text" wire:model="first_name" class="input" id="mobile_first_name">
                    @error('first_name') <p class="text-xs text-error">{{ $message }}</p> @enderror
                </div>
                <div class="input-group">
                    <x-input-label for="mobile_last_name" value="Last Name *" />
                    <input type="text" wire:model="last_name" class="input" id="mobile_last_name">
                    @error('last_name') <p class="text-xs text-error">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="input-group">
                <x-input-label for="mobile_email" value="Email" />
                <input type="email" wire:model="email" class="input" id="mobile_email">
            </div>
            <div class="input-group">
                <x-input-label for="mobile_phone" value="Phone" />
                <input type="tel" wire:model="phone" class="input" id="mobile_phone">
            </div>
            <div class="input-group">
                <x-input-label for="mobile_company" value="Company" />
                <select wire:model="company_id" class="input" id="mobile_company">
                    <option value="">Select...</option>
                    @foreach($companies as $company)
                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                    @endforeach
                </select>
            </div>
            @if(!$company_id)
            <div class="input-group">
                <x-input-label for="mobile_new_company" value="Or Add New Company" />
                <input type="text" wire:model="new_company_name" class="input" id="mobile_new_company">
            </div>
            @endif
        </div>
        @endif

        @if($step === 2)
        <h2 class="text-lg font-semibold mb-4">Visit Details</h2>
        <div class="space-y-4">
            <div class="input-group">
                <x-input-label for="mobile_host_name" value="Host Name *" />
                <input type="text" wire:model="host_name" class="input" id="mobile_host_name">
                @error('host_name') <p class="text-xs text-error">{{ $message }}</p> @enderror
            </div>
            <div class="input-group">
                <x-input-label for="mobile_host_email" value="Host Email" />
                <input type="email" wire:model="host_email" class="input" id="mobile_host_email">
            </div>
            <div class="input-group">
                <x-input-label for="mobile_purpose" value="Purpose" />
                <textarea wire:model="purpose" rows="2" class="input" id="mobile_purpose"></textarea>
            </div>
        </div>
        @endif

        @if($step === 3)
        <h2 class="text-lg font-semibold mb-4">Consent</h2>
        <div class="space-y-4">
            <div class="p-3 bg-main rounded-lg">
                <p class="text-sm text-secondary mb-2">{{ $kioskSetting?->gdpr_text ?? 'I consent to data processing.' }}</p>
                <label class="flex items-center">
                    <input type="checkbox" wire:model="gdpr_consent" class="checkbox">
                    <span class="ml-2 text-sm">I agree *</span>
                </label>
                @error('gdpr_consent') <p class="text-xs text-error">{{ $message }}</p> @enderror
            </div>
            @if($kioskSetting?->show_nda)
            <div class="p-3 bg-main rounded-lg">
                <p class="text-sm text-secondary mb-2">{{ $kioskSetting?->nda_text ?? 'I agree to confidentiality.' }}</p>
                <label class="flex items-center">
                    <input type="checkbox" wire:model="nda_consent" class="checkbox">
                    <span class="ml-2 text-sm">I agree *</span>
                </label>
                @error('nda_consent') <p class="text-xs text-error">{{ $message }}</p> @enderror
            </div>
            @endif
        </div>
        @endif

        @if($step === 4)
        <h2 class="text-lg font-semibold mb-4">Signature</h2>
        <div class="border-2 border-light rounded-lg p-2">
            <canvas id="sig-pad" class="w-full h-32 bg-surface cursor-crosshair"></canvas>
        </div>
        <input type="hidden" wire:model="signature_data">
        <button type="button" onclick="clearSig()" class="mt-2 text-sm link link-primary">Clear</button>
        <script>
            const c = document.getElementById('sig-pad');
            const x = c.getContext('2d');
            let d = false, lx = 0, ly = 0;
            c.width = c.offsetWidth; c.height = c.offsetHeight;
            c.onmousedown = e => { d = true; [lx, ly] = [e.offsetX, e.offsetY]; };
            c.onmousemove = e => { if (!d) return; x.beginPath(); x.moveTo(lx, ly); x.lineTo(e.offsetX, e.offsetY); x.stroke(); [lx, ly] = [e.offsetX, e.offsetY]; @this.set('signature_data', c.toDataURL()); };
            c.onmouseup = c.onmouseout = () => d = false;
            c.ontouchstart = e => { e.preventDefault(); const t = e.touches[0], r = c.getBoundingClientRect(); d = true; lx = t.clientX - r.left; ly = t.clientY - r.top; };
            c.ontouchmove = e => { e.preventDefault(); if (!d) return; const t = e.touches[0], r = c.getBoundingClientRect(), nx = t.clientX - r.left, ny = t.clientY - r.top; x.beginPath(); x.moveTo(lx, ly); x.lineTo(nx, ny); x.stroke(); lx = nx; ly = ny; @this.set('signature_data', c.toDataURL()); };
            c.ontouchend = () => d = false;
            function clearSig() { x.clearRect(0, 0, c.width, c.height); @this.set('signature_data', ''); }
        </script>
        @endif

        <div class="flex justify-between mt-6">
            @if($step > 1)
            <button wire:click="previousStep" class="btn btn-outline">Back</button>
            @else
            <div></div>
            @endif
            @if($step < 4)
            <button wire:click="nextStep" class="btn">Next</button>
            @else
            <button wire:click="submit" class="btn btn-success">Complete</button>
            @endif
        </div>
    </div>
    @endif
</div>