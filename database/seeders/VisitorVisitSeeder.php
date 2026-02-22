<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Visitor;
use App\Models\VisitorVisit;
use Illuminate\Database\Seeder;

class VisitorVisitSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::active()->get();

        foreach ($tenants as $tenant) {
            $visitors = Visitor::where('tenant_id', $tenant->id)->where('is_blacklisted', false)->get();
            // Get users who belong to this tenant
            $hosts = User::whereHas('tenants', fn ($q) => $q->where('tenant_id', $tenant->id))->get();

            if ($visitors->isEmpty() || $hosts->isEmpty()) {
                continue;
            }

            // Create checked-in visitors
            VisitorVisit::factory()->count(5)->create([
                'tenant_id' => $tenant->id,
                'visitor_id' => fake()->randomElement($visitors)->id,
                'host_id' => fake()->randomElement($hosts)->id,
            ]);

            // Create checked-out visitors
            VisitorVisit::factory()->checkedOut()->count(20)->create([
                'tenant_id' => $tenant->id,
                'visitor_id' => fake()->randomElement($visitors)->id,
                'host_id' => fake()->randomElement($hosts)->id,
            ]);

            // Create cancelled visits
            VisitorVisit::factory()->cancelled()->count(3)->create([
                'tenant_id' => $tenant->id,
                'visitor_id' => fake()->randomElement($visitors)->id,
                'host_id' => fake()->randomElement($hosts)->id,
            ]);

            // Create some visits with kiosk check-in
            VisitorVisit::factory()->count(10)->create([
                'tenant_id' => $tenant->id,
                'visitor_id' => fake()->randomElement($visitors)->id,
                'host_id' => fake()->randomElement($hosts)->id,
                'check_in_method' => 'kiosk_code',
            ]);

            // Create some visits with reception check-in
            VisitorVisit::factory()->checkedOut()->count(10)->create([
                'tenant_id' => $tenant->id,
                'visitor_id' => fake()->randomElement($visitors)->id,
                'host_id' => fake()->randomElement($hosts)->id,
                'check_in_method' => 'reception',
            ]);

            // Create visits with different badge types
            $badgeTypes = ['visitor', 'contractor', 'vendor', 'executive'];
            foreach ($badgeTypes as $badgeType) {
                VisitorVisit::factory()->count(2)->create([
                    'tenant_id' => $tenant->id,
                    'visitor_id' => fake()->randomElement($visitors)->id,
                    'host_id' => fake()->randomElement($hosts)->id,
                    'badge_type' => $badgeType,
                ]);
            }
        }

        $this->command->info('Visitor visits seeded successfully.');
    }
}