<div class="w-full max-w-2xl mx-auto">
    @if($step <= $totalSteps)
    <div class="card kiosk-card">
        <!-- Building & Entrance Info -->
        <div class="mb-6 p-4 bg-main rounded-lg">
            <div class="flex items-center gap-2 text-secondary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <span class="font-medium">{{ $entrance->building->name }}</span>
                <span class="mx-2">|</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span class="font-medium">{{ $entrance->name }}</span>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="flex justify-between items-center mb-3">
                <span class="kiosk-step-text">STEP {{ $step }} OF {{ $totalSteps }}</span>
                <span class="kiosk-step-text">{{ round(($step / $totalSteps) * 100) }}%</span>
            </div>
            <div class="progress-bar kiosk-progress">
                <div class="progress-bar-fill" style="width: {{ ($step / $totalSteps) * 100 }}%"></div>
            </div>
        </div>

        @if($step === 1)
        <!-- Visitor Details -->
        <h2 class="text-2xl font-bold mb-6">Your Details</h2>
        <div class="space-y-5">
            <div class="grid grid-cols-2 gap-4">
                <div class="input-group">
                    <x-input-label for="first_name" value="First Name *" />
                    <input type="text" wire:model="first_name" class="input kiosk-input" id="first_name">
                    @error('first_name') <p class="text-sm mt-1" style="color: #DC2626;">{{ $message }}</p> @enderror
                </div>
                <div class="input-group">
                    <x-input-label for="last_name" value="Last Name *" />
                    <input type="text" wire:model="last_name" class="input kiosk-input" id="last_name">
                    @error('last_name') <p class="text-sm mt-1" style="color: #DC2626;">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="input-group">
                <x-input-label for="email" value="Email" />
                <input type="email" wire:model="email" class="input kiosk-input" id="email">
            </div>
            <div class="input-group">
                <x-input-label for="phone" value="Phone" />
                <input type="tel" wire:model="phone" class="input kiosk-input" id="phone">
            </div>
            <div class="input-group">
                <x-input-label for="company_id" value="Company" />
                <select wire:model="company_id" class="input kiosk-input" id="company_id">
                    <option value="">Select Company (or add new below)</option>
                    @foreach($companies as $company)
                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                    @endforeach
                </select>
            </div>
            @if(!$company_id)
            <div class="input-group">
                <x-input-label for="new_company_name" value="Or Add New Company" />
                <input type="text" wire:model="new_company_name" placeholder="Company name" class="input kiosk-input" id="new_company_name">
            </div>
            @endif
        </div>
        @endif

        @if($step === 2)
        <!-- Visit Details -->
        <h2 class="text-2xl font-bold mb-6">Visit Details</h2>
        <div class="space-y-5">
            <div class="input-group">
                <x-input-label for="host_id" value="Host *" />
                <select wire:model="host_id" class="input kiosk-input" id="host_id">
                    <option value="">Select Host</option>
                    @foreach($hosts as $host)
                    <option value="{{ $host->id }}">{{ $host->name }}</option>
                    @endforeach
                </select>
                @error('host_id') <p class="text-sm mt-1" style="color: #DC2626;">{{ $message }}</p> @enderror
            </div>
            <div class="input-group">
                <x-input-label for="purpose" value="Purpose of Visit *" />
                <select wire:model="purpose" class="input kiosk-input" id="purpose">
                    <option value="">Select Purpose</option>
                    @foreach($purposes as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('purpose') <p class="text-sm mt-1" style="color: #DC2626;">{{ $message }}</p> @enderror
            </div>
        </div>
        @endif

        @if($step === 3)
        <!-- Consent -->
        <h2 class="text-2xl font-bold mb-6">Consent</h2>
        <div class="space-y-5">
            <div class="kiosk-consent-card">
                <p class="text-secondary mb-4">{{ $entrance->kioskSetting?->gdpr_text ?? 'I consent to the collection and processing of my personal data.' }}</p>
                <label class="flex items-center">
                    <input type="checkbox" wire:model="gdpr_consent" class="checkbox kiosk-checkbox">
                    <span class="ml-3">I agree to the GDPR terms *</span>
                </label>
                @error('gdpr_consent') <p class="text-sm mt-1" style="color: #DC2626;">{{ $message }}</p> @enderror
            </div>

            @if($entrance->kioskSetting?->show_nda)
            <div class="kiosk-consent-card">
                <p class="text-secondary mb-4">{{ $entrance->kioskSetting?->nda_text ?? 'I agree to maintain confidentiality.' }}</p>
                <label class="flex items-center">
                    <input type="checkbox" wire:model="nda_consent" class="checkbox kiosk-checkbox">
                    <span class="ml-3">I agree to the NDA terms *</span>
                </label>
                @error('nda_consent') <p class="text-sm mt-1" style="color: #DC2626;">{{ $message }}</p> @enderror
            </div>
            @endif
        </div>
        @endif

        @if($step === 4 && ($entrance->kioskSetting?->require_signature || $entrance->kioskSetting?->require_photo))
        <!-- Signature -->
        <h2 class="text-2xl font-bold mb-6">Your Signature</h2>
        <div class="space-y-5">
            <div class="kiosk-signature-pad p-4">
                <canvas id="signature-pad" class="w-full h-48 rounded-lg cursor-crosshair"></canvas>
            </div>
            <input type="hidden" wire:model="signature_data">
            <div class="flex justify-center">
                <button type="button" onclick="clearSignature()" class="btn btn-outline kiosk-btn">Clear Signature</button>
            </div>
        </div>

        <script>
            const canvas = document.getElementById('signature-pad');
            const ctx = canvas.getContext('2d');
            let isDrawing = false;
            let lastX = 0;
            let lastY = 0;

            canvas.width = canvas.offsetWidth;
            canvas.height = canvas.offsetHeight;

            canvas.addEventListener('mousedown', startDrawing);
            canvas.addEventListener('mousemove', draw);
            canvas.addEventListener('mouseup', stopDrawing);
            canvas.addEventListener('mouseout', stopDrawing);
            canvas.addEventListener('touchstart', handleTouch);
            canvas.addEventListener('touchmove', handleTouchMove);
            canvas.addEventListener('touchend', stopDrawing);

            function startDrawing(e) {
                isDrawing = true;
                [lastX, lastY] = [e.offsetX, e.offsetY];
            }

            function draw(e) {
                if (!isDrawing) return;
                ctx.beginPath();
                ctx.moveTo(lastX, lastY);
                ctx.lineTo(e.offsetX, e.offsetY);
                ctx.strokeStyle = '#1e293b';
                ctx.lineWidth = 3;
                ctx.lineCap = 'round';
                ctx.stroke();
                [lastX, lastY] = [e.offsetX, e.offsetY];
                @this.set('signature_data', canvas.toDataURL());
            }

            function stopDrawing() {
                isDrawing = false;
            }

            function handleTouch(e) {
                e.preventDefault();
                const touch = e.touches[0];
                const rect = canvas.getBoundingClientRect();
                isDrawing = true;
                lastX = touch.clientX - rect.left;
                lastY = touch.clientY - rect.top;
            }

            function handleTouchMove(e) {
                e.preventDefault();
                if (!isDrawing) return;
                const touch = e.touches[0];
                const rect = canvas.getBoundingClientRect();
                const x = touch.clientX - rect.left;
                const y = touch.clientY - rect.top;
                ctx.beginPath();
                ctx.moveTo(lastX, lastY);
                ctx.lineTo(x, y);
                ctx.strokeStyle = '#1e293b';
                ctx.lineWidth = 3;
                ctx.lineCap = 'round';
                ctx.stroke();
                lastX = x;
                lastY = y;
                @this.set('signature_data', canvas.toDataURL());
            }

            function clearSignature() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                @this.set('signature_data', '');
            }
        </script>
        @endif

        <!-- Navigation Buttons -->
        <div class="flex justify-between mt-8">
            @if($step > 1)
            <button wire:click="previousStep" class="btn btn-outline kiosk-btn">Back</button>
            @else
            <a href="{{ route('kiosk.welcome', $entrance->kiosk_identifier) }}" class="btn btn-outline kiosk-btn">Cancel</a>
            @endif

            @if($step < $totalSteps)
            <button wire:click="nextStep" class="btn kiosk-btn">Continue</button>
            @else
            <button wire:click="submit" class="btn kiosk-btn" style="background-color: #16a34a;">Complete Check-in</button>
            @endif
        </div>
    </div>
    @else
    <!-- Success -->
    <div class="card kiosk-card text-center">
        <div class="kiosk-success-icon rounded-full bg-green-100 flex items-center justify-center">
            <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <h2 class="text-3xl font-bold mb-4">Check-in Complete!</h2>
        <p class="text-lg text-secondary mb-6">Thank you, {{ $currentVisit->visitor->first_name }}! You're all set.</p>

        <div class="bg-main rounded-lg p-5 mb-6 text-left">
            <div class="kiosk-detail-row">
                <span class="font-semibold text-secondary">Host</span>
                <span>{{ $currentVisit->host?->name ?? $currentVisit->host_name }}</span>
            </div>
            <div class="kiosk-detail-row">
                <span class="font-semibold text-secondary">Purpose</span>
                <span>{{ $currentVisit->purpose }}</span>
            </div>
            <div class="kiosk-detail-row">
                <span class="font-semibold text-secondary">Location</span>
                <span>{{ $currentVisit->entrance->name }}</span>
            </div>
            <div class="kiosk-detail-row">
                <span class="font-semibold text-secondary">Building</span>
                <span>{{ $currentVisit->entrance->building->name }}</span>
            </div>
        </div>

        <a href="{{ route('kiosk.welcome', $entrance->kiosk_identifier) }}" class="btn kiosk-btn">Done</a>
    </div>
    @endif
</div>