<?php

namespace Database\Seeders;

use App\Models\AccessLog;
use App\Models\AccessPoint;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Database\Seeder;

class AccessLogSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::active()->get();

        foreach ($tenants as $tenant) {
            $accessPoints = AccessPoint::where('tenant_id', $tenant->id)->active()->get();
            // Get users who belong to this tenant
            $users = User::whereHas('tenants', fn ($q) => $q->where('tenant_id', $tenant->id))->get();
            $visitors = Visitor::where('tenant_id', $tenant->id)->where('is_blacklisted', false)->get();

            if ($accessPoints->isEmpty()) {
                continue;
            }

            // Create access logs for users
            $userAccessCount = fake()->numberBetween(50, 100);
            for ($i = 0; $i < $userAccessCount; $i++) {
                $user = fake()->randomElement($users);
                if (!$user) continue;

                $accessPoint = fake()->randomElement($accessPoints);
                $isEntry = fake()->boolean(50);

                AccessLog::factory()->forUser($user)->create([
                    'tenant_id' => $tenant->id,
                    'access_point_id' => $accessPoint->id,
                    'direction' => $isEntry ? 'entry' : 'exit',
                    'result' => fake()->boolean(95) ? 'granted' : 'denied',
                    'denial_reason' => null,
                ]);
            }

            // Create access logs for visitors
            $visitorAccessCount = fake()->numberBetween(20, 50);
            for ($i = 0; $i < $visitorAccessCount; $i++) {
                $visitor = fake()->randomElement($visitors);
                if (!$visitor) continue;

                $accessPoint = fake()->randomElement($accessPoints);
                $isEntry = fake()->boolean(50);

                AccessLog::factory()->forVisitor($visitor)->create([
                    'tenant_id' => $tenant->id,
                    'access_point_id' => $accessPoint->id,
                    'direction' => $isEntry ? 'entry' : 'exit',
                    'result' => fake()->boolean(90) ? 'granted' : 'denied',
                    'denial_reason' => null,
                ]);
            }

            // Create some denied access logs
            $deniedCount = fake()->numberBetween(5, 15);
            for ($i = 0; $i < $deniedCount; $i++) {
                $subject = fake()->randomElement([
                    ['type' => User::class, 'model' => fake()->randomElement($users)],
                    ['type' => Visitor::class, 'model' => fake()->randomElement($visitors)],
                ]);

                if (!$subject['model']) continue;

                AccessLog::factory()->denied()->create([
                    'tenant_id' => $tenant->id,
                    'access_point_id' => fake()->randomElement($accessPoints)->id,
                    'subject_type' => $subject['type'],
                    'subject_id' => $subject['model']->id,
                ]);
            }

            // Create today's access logs
            $todayAccessCount = fake()->numberBetween(10, 30);
            for ($i = 0; $i < $todayAccessCount; $i++) {
                $user = fake()->randomElement($users);
                if (!$user) continue;

                AccessLog::factory()->forUser($user)->today()->create([
                    'tenant_id' => $tenant->id,
                    'access_point_id' => fake()->randomElement($accessPoints)->id,
                    'result' => 'granted',
                ]);
            }
        }

        $this->command->info('Access logs seeded successfully.');
    }
}