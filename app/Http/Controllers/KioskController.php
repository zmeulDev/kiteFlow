<?php

namespace App\Http\Controllers;

use App\Models\AccessPoint;
use App\Models\Tenant;
use Illuminate\Http\Request;

class KioskController extends Controller
{
    /**
     * Show the kiosk mode interface
     */
    public function index(string $tenantSlug, string $accessPointUuid)
    {
        // Find tenant by slug
        $tenant = Tenant::where('slug', $tenantSlug)->firstOrFail();

        // Find access point by UUID and verify it belongs to the tenant and is in kiosk mode
        $accessPoint = AccessPoint::where('uuid', $accessPointUuid)
            ->where('tenant_id', $tenant->id)
            ->where('is_kiosk_mode', true)
            ->where('is_active', true)
            ->firstOrFail();

        // Render the kiosk Livewire component with the parameters
        return view('livewire.kiosk.kiosk-mode', [
            'tenant' => $tenant,
            'accessPoint' => $accessPoint,
        ])->layout('layouts.kiosk');
    }
}
