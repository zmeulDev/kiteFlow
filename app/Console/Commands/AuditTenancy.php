<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class AuditTenancy extends Command
{
    protected $signature = 'audit:tenancy';
    protected $description = 'Run a full audit of tenant isolation and relationships.';

    public function handle()
    {
        $this->info("--- KITEFLOW TENANCY AUDIT ---");

        // 1. Verify Database
        $tenantCount = Tenant::withoutGlobalScopes()->count();
        $this->line("Tenants in DB: $tenantCount");
        if ($tenantCount === 0) {
            $this->error("No tenants found! Audit aborted.");
            return;
        }

        // 2. Test Hub Isolation
        $hub = Tenant::withoutGlobalScopes()->where('is_hub', true)->first();
        if ($hub) {
            $user = $hub->users()->first();
            $this->info("Testing Hub Owner access for: {$hub->name} (User: {$user->name})");
            
            Auth::login($user);
            session()->put('tenant_id', $hub->id);
            
            // Mock accessible IDs (IdentifyTenant middleware logic)
            $childIds = $hub->children()->pluck('id')->toArray();
            $accessibleIds = array_merge([$hub->id], $childIds);
            session()->put('accessible_tenant_ids', $accessibleIds);
            
            $visibleVisits = Visit::count();
            $expectedCount = Visit::withoutGlobalScopes()->whereIn('tenant_id', $accessibleIds)->count();
            
            $this->line("Hub Owner sees $visibleVisits visits (Expected: $expectedCount).");
            
            if ($visibleVisits === $expectedCount) {
                $this->info("✅ Hub Isolation: PASSED");
            } else {
                $this->error("❌ Hub Isolation: FAILED");
            }
        }

        // 3. Test Regular Tenant Isolation
        $regular = Tenant::withoutGlobalScopes()->where('is_hub', false)->whereNotNull('parent_id')->first();
        if ($regular) {
            $user = $regular->users()->first();
            $this->info("Testing Regular Tenant isolation for: {$regular->name} (User: {$user->name})");
            
            Auth::login($user);
            session()->forget('accessible_tenant_ids');
            session()->put('tenant_id', $regular->id);
            
            $visibleVisits = Visit::count();
            $expectedCount = Visit::withoutGlobalScopes()->where('tenant_id', $regular->id)->count();
            
            $this->line("Regular Tenant sees $visibleVisits visits (Expected: $expectedCount).");
            
            if ($visibleVisits === $expectedCount) {
                $this->info("✅ Tenant Isolation: PASSED");
            } else {
                $this->error("❌ Tenant Isolation: FAILED");
            }
        }

        // 4. Test Super Admin Bypass
        $admin = User::withoutGlobalScopes()->where('is_super_admin', true)->first();
        if ($admin) {
            $this->info("Testing Super Admin bypass for: {$admin->name}");
            
            Auth::login($admin);
            session()->forget(['tenant_id', 'accessible_tenant_ids', 'impersonator_id']);
            
            $visibleVisits = Visit::count();
            $totalVisits = Visit::withoutGlobalScopes()->count();
            
            $this->line("Super Admin (not impersonating) sees $visibleVisits/$totalVisits visits.");
            
            if ($visibleVisits === $totalVisits) {
                $this->info("✅ Super Admin Bypass: PASSED");
            } else {
                $this->error("❌ Super Admin Bypass: FAILED");
            }
        }

        $this->info("--- AUDIT COMPLETE ---");
    }
}
