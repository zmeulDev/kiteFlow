<div class="w-full max-w-2xl mx-auto kiosk-fade-in">
    <div class="card kiosk-card">
        <div class="text-center mb-8">
            <div class="w-14 h-14 mx-auto mb-4 icon-container icon-container--coral rounded-2xl">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            <h2 class="text-3xl font-bold mb-2">Photo Capture</h2>
            <p class="text-secondary">Please take a photo for your visitor badge</p>
        </div>

        @if($showCamera)
        <div class="text-center">
            <div class="bg-gray-900 rounded-2xl overflow-hidden mb-6 shadow-xl border-4 border-light">
                <video id="video" autoplay playsinline class="w-full max-h-80 object-cover"></video>
            </div>
            <canvas id="canvas" class="hidden"></canvas>
            <button onclick="capture()" class="btn kiosk-btn">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke-width="2"></circle>
                    <circle cx="12" cy="12" r="3" fill="currentColor"></circle>
                </svg>
                Take Photo
            </button>
        </div>

        <script>
            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');
            navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } })
                .then(stream => video.srcObject = stream)
                .catch(err => console.error('Camera error:', err));

            function capture() {
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                canvas.getContext('2d').drawImage(video, 0, 0);
                const data = canvas.toDataURL('image/jpeg');
                @this.call('capturePhoto', data);
            }
        </script>
        @else
        <div class="text-center">
            <div class="bg-main p-4 rounded-2xl mb-6">
                <img src="{{ $capturedPhoto }}" alt="Captured" class="mx-auto rounded-xl shadow-lg max-h-80">
            </div>
            <div class="flex flex-col sm:flex-row justify-center gap-3">
                <button wire:click="retakePhoto" class="btn btn-outline kiosk-btn justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Retake
                </button>
                <button wire:click="submit" class="btn btn-success kiosk-btn justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Use This Photo
                </button>
            </div>
        </div>
        @endif

        <div class="mt-8 text-center pt-6 border-t border-light">
            <button wire:click="skip" class="link">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
                </svg>
                Skip photo
            </button>
        </div>
    </div>
</div>