<div class="flex items-center gap-2 px-3 py-2">
    @if($stats['checked_in'] > 0)
    <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-[#FF4B4B]/10 text-[#FF4B4B]">
        {{ $stats['checked_in'] }} in
    </span>
    @endif
</div>