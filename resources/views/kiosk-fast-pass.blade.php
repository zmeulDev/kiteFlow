<!-- projects/visiflow/resources/views/kiosk-fast-pass.blade.php -->
@php
    $visit = \App\Models\Visit::withoutGlobalScopes()
        ->with(['tenant'])
        ->where('check_in_token', $token)
        ->first();
        
    // Always bypass scope for public layouts
    $tenant = $visit 
        ? \App\Models\Tenant::withoutGlobalScopes()->find($visit->tenant_id)
        : \App\Models\Tenant::withoutGlobalScopes()->first();
@endphp

@if($tenant)
    <x-kiosk-layout :tenant="$tenant">
        @livewire('kiosk.fast-pass', ['token' => $token])
    </x-kiosk-layout>
@else
    <div style="font-family: sans-serif; text-align: center; padding: 50px;">
        <h1>404</h1>
        <p>Tenant not found. Please contact support.</p>
    </div>
@endif
