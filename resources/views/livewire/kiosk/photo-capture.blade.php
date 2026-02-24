<div class="w-full max-w-2xl mx-auto">
    <div class="card kiosk-card">
        <h2 class="text-2xl font-bold mb-6 text-center">Photo Capture</h2>

        @if($showCamera)
        <div class="text-center">
            <div class="bg-gray-900 rounded-lg overflow-hidden mb-6 shadow-lg">
                <video id="video" autoplay playsinline class="w-full max-h-80 object-cover"></video>
            </div>
            <canvas id="canvas" class="hidden"></canvas>
            <button onclick="capture()" class="btn kiosk-btn">Take Photo</button>
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
            <img src="{{ $capturedPhoto }}" alt="Captured" class="mx-auto rounded-lg shadow-lg mb-6 max-h-80">
            <div class="flex justify-center gap-4">
                <button wire:click="retakePhoto" class="btn btn-outline kiosk-btn">Retake</button>
                <button wire:click="submit" class="btn kiosk-btn" style="background-color: #16a34a;">Use This Photo</button>
            </div>
        </div>
        @endif

        <div class="mt-6 text-center">
            <button wire:click="skip" class="link">Skip photo</button>
        </div>
    </div>
</div>