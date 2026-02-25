<div class="w-full max-w-2xl mx-auto kiosk-fade-in">
    @if(!$checkedIn)
    <div class="card kiosk-card">
        <!-- Building & Entrance Info -->
        <div class="mb-8 p-5 building-info-card rounded-2xl">
            <div class="flex flex-wrap items-center gap-3 text-secondary">
                <div class="flex items-center gap-2 bg-surface px-3 py-1.5 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span class="font-semibold text-primary">{{ $entrance->building->name }}</span>
                </div>
                <span class="text-muted">|</span>
                <div class="flex items-center gap-2 bg-surface px-3 py-1.5 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span class="font-semibold text-primary">{{ $entrance->name }}</span>
                </div>
            </div>
        </div>

        <!-- Welcome Message -->
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold mb-2">Welcome, {{ $visit->visitor->first_name }}!</h2>
            <p class="text-secondary">Please complete the check-in process for your visit with <strong class="text-primary">{{ $visit->host?->name ?? $visit->host_name }}</strong></p>
        </div>

        @if($totalSteps > 1)
        <!-- Progress Bar -->
        <div class="mb-10">
            <div class="flex justify-between items-center mb-3">
                <span class="kiosk-step-text text-sm">Step {{ $step }} of {{ $totalSteps }}</span>
                <span class="kiosk-step-text text-sm">{{ round(($step / $totalSteps) * 100) }}%</span>
            </div>
            <div class="progress-bar kiosk-progress">
                <div class="progress-bar-fill" style="width: {{ ($step / $totalSteps) * 100 }}%"></div>
            </div>
        </div>
        @endif

        @if($step === 1)
        <!-- Consent -->
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 icon-container icon-container--green rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold">Consent</h2>
            </div>
            <div class="space-y-5">
                <div class="kiosk-consent-card">
                    <div class="flex items-start gap-4">
                        <input type="checkbox" wire:model="gdpr_consent" class="checkbox kiosk-checkbox mt-1 flex-shrink-0" id="gdpr_consent">
                        <div>
                            <label for="gdpr_consent" class="font-semibold text-primary cursor-pointer">I agree to the GDPR terms <span class="text-error">*</span></label>
                            <p class="text-secondary mt-1 text-sm leading-relaxed">{{ $kioskSetting?->gdpr_text ?? 'I consent to the collection and processing of my personal data.' }}</p>
                        </div>
                    </div>
                    @error('gdpr_consent') <p class="text-sm mt-3 text-error ml-10">{{ $message }}</p> @enderror
                </div>

                @if($kioskSetting?->show_nda)
                <div class="kiosk-consent-card">
                    <div class="flex items-start gap-4">
                        <input type="checkbox" wire:model="nda_consent" class="checkbox kiosk-checkbox mt-1 flex-shrink-0" id="nda_consent">
                        <div>
                            <label for="nda_consent" class="font-semibold text-primary cursor-pointer">I agree to the NDA terms <span class="text-error">*</span></label>
                            <p class="text-secondary mt-1 text-sm leading-relaxed">{{ $kioskSetting?->nda_text ?? 'I agree to maintain confidentiality.' }}</p>
                        </div>
                    </div>
                    @error('nda_consent') <p class="text-sm mt-3 text-error ml-10">{{ $message }}</p> @enderror
                </div>
                @endif
            </div>
        </div>
        @endif

        @if($step === 2 && ($kioskSetting?->require_signature || $kioskSetting?->require_photo))
        <!-- Signature -->
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 icon-container icon-container--purple rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold">Your Signature</h2>
            </div>
            <div class="space-y-5">
                <div class="kiosk-signature-pad p-6 bg-main rounded-2xl">
                    <canvas id="signature-pad" class="w-full h-48 rounded-xl cursor-crosshair bg-surface"></canvas>
                </div>
                <input type="hidden" wire:model="signature_data">
                <div class="flex justify-center">
                    <button type="button" onclick="clearSignature()" class="btn btn-outline kiosk-btn">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Clear Signature
                    </button>
                </div>
            </div>
        </div>

        <script>
            const canvas = document.getElementById('signature-pad');
            const ctx = canvas.getContext('2d');
            let isDrawing = false;
            let lastX = 0;
            let lastY = 0;

            function resizeCanvas() {
                const rect = canvas.getBoundingClientRect();
                canvas.width = rect.width;
                canvas.height = rect.height;
            }
            resizeCanvas();
            window.addEventListener('resize', resizeCanvas);

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
                ctx.strokeStyle = '#111827';
                ctx.lineWidth = 2.5;
                ctx.lineCap = 'round';
                ctx.lineJoin = 'round';
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
                ctx.strokeStyle = '#111827';
                ctx.lineWidth = 2.5;
                ctx.lineCap = 'round';
                ctx.lineJoin = 'round';
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
        <div class="flex justify-between pt-6 border-t border-light">
            @if($step > 1)
            <button wire:click="previousStep" class="btn btn-outline kiosk-btn">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back
            </button>
            @else
            <a href="{{ route('kiosk.check-in-code', $entrance->kiosk_identifier) }}" class="btn btn-outline kiosk-btn">Cancel</a>
            @endif

            @if($step < $totalSteps)
            <button wire:click="nextStep" class="btn kiosk-btn">
                Continue
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
            @else
            <button wire:click="submit" class="btn btn-success kiosk-btn">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Complete Check-in
            </button>
            @endif
        </div>
    </div>
    @else
    <!-- Success -->
    <div class="card kiosk-card text-center">
        <div class="kiosk-success-icon w-20 h-20 mx-auto mb-4 icon-container icon-container--green rounded-full flex items-center justify-center">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <h2 class="text-3xl font-bold mb-3">Check-in Complete!</h2>
        <p class="text-lg text-secondary mb-8">Thank you, {{ $visit->visitor->first_name }}! You're all set.</p>

        <div class="detail-card mb-8">
            <div class="kiosk-detail-row">
                <span class="font-semibold text-secondary">Host</span>
                <span class="font-medium text-primary">{{ $visit->host?->name ?? $visit->host_name }}</span>
            </div>
            <div class="kiosk-detail-row">
                <span class="font-semibold text-secondary">Purpose</span>
                <span class="font-medium text-primary">{{ $visit->purpose ?? 'Not specified' }}</span>
            </div>
            <div class="kiosk-detail-row">
                <span class="font-semibold text-secondary">Location</span>
                <span class="font-medium text-primary">{{ $visit->entrance->name }}</span>
            </div>
            <div class="kiosk-detail-row">
                <span class="font-semibold text-secondary">Building</span>
                <span class="font-medium text-primary">{{ $visit->entrance->building->name }}</span>
            </div>
        </div>

        <a href="{{ route('kiosk.welcome', $entrance->kiosk_identifier) }}" class="btn kiosk-btn">Done</a>
    </div>
    @endif
</div>