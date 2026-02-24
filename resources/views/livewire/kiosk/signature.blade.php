<div class="w-full max-w-2xl mx-auto">
    <div class="card kiosk-card">
        <h2 class="text-2xl font-bold mb-6 text-center">Please Sign</h2>

        <div class="kiosk-signature-pad p-4">
            <canvas id="signature-canvas" class="w-full h-48 bg-white rounded-lg cursor-crosshair touch-none"></canvas>
        </div>

        @error('signature')
        <p class="text-sm mt-2 text-center" style="color: #DC2626;">{{ $message }}</p>
        @enderror

        <div class="flex justify-center gap-4 mt-6">
            <button wire:click="clearSignature" class="btn btn-outline kiosk-btn">Clear</button>
            <button wire:click="submit" class="btn kiosk-btn">Confirm Signature</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const canvas = document.getElementById('signature-canvas');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        let isDrawing = false;
        let lastX = 0;
        let lastY = 0;

        function resize() {
            canvas.width = canvas.offsetWidth;
            canvas.height = canvas.offsetHeight;
        }
        resize();
        window.addEventListener('resize', resize);

        function getCoords(e) {
            const rect = canvas.getBoundingClientRect();
            if (e.touches) {
                return {
                    x: e.touches[0].clientX - rect.left,
                    y: e.touches[0].clientY - rect.top
                };
            }
            return { x: e.offsetX, y: e.offsetY };
        }

        function startDrawing(e) {
            e.preventDefault();
            isDrawing = true;
            const coords = getCoords(e);
            lastX = coords.x;
            lastY = coords.y;
        }

        function draw(e) {
            if (!isDrawing) return;
            e.preventDefault();
            const coords = getCoords(e);
            ctx.beginPath();
            ctx.moveTo(lastX, lastY);
            ctx.lineTo(coords.x, coords.y);
            ctx.strokeStyle = '#1e293b';
            ctx.lineWidth = 3;
            ctx.lineCap = 'round';
            ctx.stroke();
            lastX = coords.x;
            lastY = coords.y;

            @this.call('captureSignature', canvas.toDataURL());
        }

        function stopDrawing() {
            isDrawing = false;
        }

        canvas.addEventListener('mousedown', startDrawing);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', stopDrawing);
        canvas.addEventListener('mouseout', stopDrawing);
        canvas.addEventListener('touchstart', startDrawing);
        canvas.addEventListener('touchmove', draw);
        canvas.addEventListener('touchend', stopDrawing);
    });
</script>