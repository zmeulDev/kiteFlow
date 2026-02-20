<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Tenant;
use App\Models\Visit;

new #[Layout('components.layouts.app')] class extends Component {
    public ?Tenant $tenant = null;
    public string $mode = 'home';
    public string $code = '';
    public ?Visit $visit = null;
    public string $signature = '';

    public function mount(Tenant $tenant) {
        $this->tenant = $tenant;
    }

    public function findVisit() {
        $this->validate(['code' => 'required']);
        $visit = Visit::with(['host', 'meetingRoom'])->where('tenant_id', $this->tenant->id)
                      ->where('invite_code', $this->code)->first();
        if ($visit) {
             $this->visit = $visit;
             $this->mode = 'checkin';
        } else {
             session()->flash('error', 'Visit not found or invalid code.');
        }
    }

    public function checkIn() {
        $this->validate(['signature' => 'required|string']);
        $this->visit->update([
            'status' => 'checked_in',
            'check_in_time' => now(),
            'nda_signature' => $this->signature
        ]);
        \App\Jobs\NotifyHostArrivalJob::dispatch($this->visit);
        $this->mode = 'success';
    }

    public function setMode($newMode) {
        if ($newMode === 'home') {
            $this->visit = null;
            $this->code = '';
            $this->signature = '';
        }
        $this->mode = $newMode;
    }

    public function handleScan($decodedText) {
        if ($this->mode !== 'scanner') return;
        $this->code = $decodedText;
        $this->findVisit();
    }

    public function manualFind() {
        $this->validate(['code' => 'required|email'], ['code.email' => 'Please enter the email you registered with.']);
        $visitor = \App\Models\Visitor::where('email', $this->code)->first();
        if ($visitor) {
            $visit = Visit::with(['host', 'meetingRoom'])->where('tenant_id', $this->tenant->id)
                          ->where('visitor_id', $visitor->id)
                          ->where('status', 'pending')
                          ->first();
            if ($visit) {
                $this->visit = $visit;
                $this->mode = 'checkin';
                return;
            }
        }
        session()->flash('error', 'No pending visit found for this email address.');
    }
};

?>

<div class="flex flex-col items-center justify-center min-h-screen">
    
    <!-- Header Area -->
    <div class="text-center mb-12 animate-fade-in-up">
        @if($tenant->logo_path)
            <img src="{{ Storage::url($tenant->logo_path) }}" alt="{{ $tenant->name }} Logo" class="h-16 mx-auto mb-6 object-contain">
        @endif
        <h1 class="text-4xl lg:text-5xl font-extrabold tracking-tight mb-2">{{ $tenant->name }}</h1>
        <p class="text-gray-500 font-bold tracking-wide">Visitor Reception</p>
    </div>

    <div class="w-full max-w-4xl px-4 lg:px-0">
        @if($mode === 'home')
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 animate-fade-in-up delay-100">
                <button wire:click="setMode('scanner')" class="card group flex flex-col items-center justify-center p-12 hover:shadow-lg transition-all duration-300 text-center cursor-pointer min-h-[250px]">
                    <span class="text-5xl mb-6 group-hover:scale-110 transition-transform duration-300">📱</span>
                    <h3 class="text-xl font-bold mb-2">Scan QR</h3>
                    <p class="text-sm text-gray-500">Scan your mobile pass</p>
                </button>
                
                <button wire:click="setMode('code')" class="card group flex flex-col items-center justify-center p-12 hover:shadow-lg transition-all duration-300 text-center cursor-pointer min-h-[250px]">
                    <span class="text-5xl mb-6 group-hover:scale-110 transition-transform duration-300">🎟️</span>
                    <h3 class="text-xl font-bold mb-2">Invite Code</h3>
                    <p class="text-sm text-gray-500">Enter your 6-digit pin</p>
                </button>
                
                <button wire:click="setMode('manual')" class="card group flex flex-col items-center justify-center p-12 hover:shadow-lg transition-all duration-300 text-center cursor-pointer min-h-[250px]">
                    <span class="text-5xl mb-6 group-hover:scale-110 transition-transform duration-300">📋</span>
                    <h3 class="text-xl font-bold mb-2">Check-in</h3>
                    <p class="text-sm text-gray-500">Find your registration</p>
                </button>
            </div>

        @elseif($mode === 'code')
            <div class="card max-w-lg mx-auto p-12 animate-fade-in-up">
                <h2 class="text-3xl font-extrabold mb-8 text-center text-gray-900">Verify Registration</h2>
                @if(session()->has('error'))
                    <div class="bg-red-50 text-red-600 font-bold p-4 mb-6 rounded-xl text-center">
                        {{ session('error') }}
                    </div>
                @endif
                <div class="input-group mb-8">
                    <label class="sr-only">Invite Code</label>
                    <input wire:model="code" type="text" class="input text-center text-4xl font-extrabold tracking-[0.2em] uppercase py-6" placeholder="------" maxlength="12" autofocus>
                </div>
                <div class="space-y-4">
                    <button wire:click="findVisit" class="btn w-full py-5 text-xl">Continue</button>
                    <button wire:click="setMode('home')" class="btn btn-outline w-full py-4 text-gray-500 border-none hover:bg-transparent hover:text-gray-900">Go Back</button>
                </div>
            </div>

        @elseif($mode === 'manual')
            <div class="card max-w-lg mx-auto p-12 animate-fade-in-up">
                <h2 class="text-3xl font-extrabold mb-3 text-center text-gray-900">Lookup Visit</h2>
                <p class="text-center text-gray-500 font-medium mb-8">Please enter the email address used when registering.</p>
                @if(session()->has('error'))
                    <div class="bg-red-50 text-red-600 font-bold p-4 mb-6 rounded-xl text-center">
                        {{ session('error') }}
                    </div>
                @endif
                <div class="input-group mb-8">
                    <input wire:model="code" type="email" class="input py-5 text-xl" placeholder="visitor@example.com" autofocus>
                </div>
                <div class="space-y-4">
                    <button wire:click="manualFind" class="btn w-full py-5 text-xl">Find Details</button>
                    <button wire:click="setMode('home')" class="btn btn-outline w-full py-4 text-gray-500 border-none hover:bg-transparent hover:text-gray-900">Go Back</button>
                </div>
            </div>

        @elseif($mode === 'scanner')
            <div class="card max-w-2xl mx-auto p-10 animate-fade-in-up md:p-12 text-center" x-data="{
                scanner: null,
                initScanner() {
                    if(!document.getElementById('qr-reader')) return;
                    this.scanner = new Html5QrcodeScanner('qr-reader', { fps: 10, qrbox: {width: 250, height: 250} }, false);
                    this.scanner.render((decodedText, decodedResult) => {
                        this.scanner.pause(true);
                        $wire.handleScan(decodedText);
                    }, (errorMessage) => {
                        // Ignore continuous scan errors
                    });
                },
                cleanup() {
                    if(this.scanner) {
                        this.scanner.clear();
                        this.scanner = null;
                    }
                }
            }" x-init="initScanner()" x-on:livewire:navigated.window="cleanup(); initScanner()" @destroyed="cleanup()">
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-extrabold text-gray-900 mb-2">Scan Passport</h2>
                    <p class="text-gray-500 font-medium">Show your mobile device to the camera.</p>
                </div>
                
                <!-- The QR Scanner UI -->
                <div id="qr-reader" class="mx-auto rounded-[24px] overflow-hidden bg-gray-100 mb-8 border border-gray-200"></div>
                
                <button wire:click="setMode('home')" class="btn btn-outline w-full py-4 text-gray-500 border-none hover:bg-transparent hover:text-gray-900">Cancel</button>
                <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
            </div>

        @elseif($mode === 'checkin')
            <div class="card max-w-2xl mx-auto p-12 animate-fade-in-up">
                <div class="flex items-center justify-between border-b border-gray-100 pb-6 mb-8">
                    <div>
                        <h2 class="text-3xl font-extrabold text-gray-900 mb-1">Confirmation Details</h2>
                    </div>
                    <div>
                        <span class="badge badge-danger">Arrival Context</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-8 mb-8">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-bold mb-1">Host Entity</p>
                        <p class="text-lg font-bold">{{ $visit->host->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-bold mb-1">Scheduled Time</p>
                        <p class="text-lg font-bold">{{ \Carbon\Carbon::parse($visit->scheduled_at)->format('g:i A \o\n F j') }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-xs text-gray-500 uppercase font-bold mb-1">Facility / Room</p>
                        <p class="text-lg font-bold">{{ $visit->meetingRoom->name }}</p>
                    </div>
                </div>
                
                <div class="bg-gray-50 rounded-xl border border-gray-100 p-6 mb-8 h-48 overflow-y-auto">
                    <h3 class="text-sm uppercase text-gray-900 font-extrabold mb-3">Facility Policies & Agreements</h3>
                    <div class="text-gray-500 text-sm leading-relaxed whitespace-pre-line font-medium">
                        {{ $tenant->nda_text ?? 'Please sign to acknowledge our facility rules.' }}
                    </div>
                </div>
                
                <div class="input-group mb-12">
                    <label class="text-gray-900 font-bold mb-2">Electronic Signature Confirmation</label>
                    <input wire:model="signature" type="text" class="input py-5 text-xl font-bold" placeholder="Type your full legal name..." autofocus>
                    @error('signature') <span class="text-[#FF4B4B] text-sm mt-2 block font-bold">{{ $message }}</span> @enderror
                </div>
                
                <div class="flex flex-col sm:flex-row gap-4">
                    <button wire:click="setMode('home')" class="btn btn-outline flex-1 py-5 text-lg">Cancel</button>
                    <button wire:click="checkIn" class="btn flex-1 py-5 text-lg">Accept & Enter</button>
                </div>
            </div>

        @elseif($mode === 'success')
            <div class="card max-w-lg mx-auto p-12 text-center animate-fade-in-up border-t-8 border-t-[#10B981]">
                <div class="w-24 h-24 bg-[#10B981]/10 rounded-full flex items-center justify-center mx-auto mb-8 animate-fade-in-up delay-100 shadow-xl shadow-[#10B981]/10">
                    <svg class="w-12 h-12 text-[#10B981]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h2 class="text-3xl font-extrabold text-gray-900 mb-4 animate-fade-in-up delay-100">Check-in Secured</h2>
                <p class="text-gray-500 text-lg mb-8 leading-relaxed font-medium animate-fade-in-up delay-200">
                    Your host <strong class="text-gray-900">{{ $visit->host->name }}</strong> has been notified of your arrival. Please take a seat.
                </p>
                <button wire:click="setMode('home')" class="btn w-full py-5 text-xl animate-fade-in-up delay-300">Complete</button>
            </div>
        @endif
    </div>
</div>