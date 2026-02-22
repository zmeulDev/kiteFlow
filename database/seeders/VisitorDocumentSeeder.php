<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\Visitor;
use App\Models\VisitorDocument;
use Illuminate\Database\Seeder;

class VisitorDocumentSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::active()->get();

        foreach ($tenants as $tenant) {
            $visitors = Visitor::where('tenant_id', $tenant->id)->get();

            foreach ($visitors as $visitor) {
                // Add ID card for some visitors
                if (fake()->boolean(60)) {
                    VisitorDocument::factory()->idCard()->create([
                        'tenant_id' => $tenant->id,
                        'visitor_id' => $visitor->id,
                    ]);
                }

                // Add passport for some visitors
                if (fake()->boolean(30)) {
                    VisitorDocument::factory()->passport()->create([
                        'tenant_id' => $tenant->id,
                        'visitor_id' => $visitor->id,
                    ]);
                }

                // Add driver's license for some visitors
                if (fake()->boolean(50)) {
                    VisitorDocument::factory()->driverLicense()->create([
                        'tenant_id' => $tenant->id,
                        'visitor_id' => $visitor->id,
                    ]);
                }

                // Add NDA for some visitors
                if (fake()->boolean(20)) {
                    VisitorDocument::factory()->nda()->create([
                        'tenant_id' => $tenant->id,
                        'visitor_id' => $visitor->id,
                    ]);
                }

                // Add photo for some visitors
                if (fake()->boolean(40)) {
                    VisitorDocument::factory()->photo()->create([
                        'tenant_id' => $tenant->id,
                        'visitor_id' => $visitor->id,
                    ]);
                }

                // Add signature for some visitors
                if (fake()->boolean(30)) {
                    VisitorDocument::factory()->signature()->create([
                        'tenant_id' => $tenant->id,
                        'visitor_id' => $visitor->id,
                    ]);
                }

                // Add random additional documents for some visitors
                if (fake()->boolean(15)) {
                    VisitorDocument::factory()->count(fake()->numberBetween(1, 2))->create([
                        'tenant_id' => $tenant->id,
                        'visitor_id' => $visitor->id,
                    ]);
                }
            }
        }

        $this->command->info('Visitor documents seeded successfully.');
    }
}