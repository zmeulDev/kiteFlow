<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivityLogSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            // Get users who belong to this tenant
            $users = User::whereHas('tenants', fn ($q) => $q->where('tenant_id', $tenant->id))->get();
            if ($users->isEmpty()) continue;

            // Create user login/logout logs
            $authLogsCount = fake()->numberBetween(30, 60);
            for ($i = 0; $i < $authLogsCount; $i++) {
                $user = fake()->randomElement($users);
                $isLogin = fake()->boolean(60);

                ActivityLog::factory()->forUser($user)->forTenant($tenant)->create([
                    'action' => $isLogin ? 'user.login' : 'user.logout',
                    'description' => $isLogin
                        ? "User {$user->name} logged in"
                        : "User {$user->name} logged out",
                ]);
            }

            // Create visitor check-in/out logs
            $visitorLogsCount = fake()->numberBetween(20, 40);
            for ($i = 0; $i < $visitorLogsCount; $i++) {
                $user = fake()->randomElement($users);
                $isCheckIn = fake()->boolean(50);

                ActivityLog::factory()->forUser($user)->forTenant($tenant)->create([
                    'action' => $isCheckIn ? 'visitor.check_in' : 'visitor.check_out',
                    'description' => $isCheckIn
                        ? 'Visitor checked in via ' . fake()->randomElement(['kiosk', 'reception', 'qr_code'])
                        : 'Visitor checked out',
                    'subject_type' => \App\Models\Visitor::class,
                    'subject_id' => fake()->numberBetween(1, 100),
                ]);
            }

            // Create meeting-related logs
            $meetingLogsCount = fake()->numberBetween(15, 30);
            for ($i = 0; $i < $meetingLogsCount; $i++) {
                $user = fake()->randomElement($users);
                $meetingLogTypes = ['meeting.created', 'meeting.updated', 'meeting.cancelled', 'meeting.completed'];
                $logType = fake()->randomElement($meetingLogTypes);

                ActivityLog::factory()->forUser($user)->forTenant($tenant)->create([
                    'action' => $logType,
                    'description' => match ($logType) {
                        'meeting.created' => 'New meeting created',
                        'meeting.updated' => 'Meeting details updated',
                        'meeting.cancelled' => 'Meeting was cancelled',
                        'meeting.completed' => 'Meeting marked as completed',
                    },
                    'subject_type' => \App\Models\Meeting::class,
                    'subject_id' => fake()->numberBetween(1, 100),
                ]);
            }

            // Create tenant-related logs
            if ($tenant->slug === 'acme-corp') {
                $tenantLogsCount = fake()->numberBetween(5, 15);
                for ($i = 0; $i < $tenantLogsCount; $i++) {
                    $user = fake()->randomElement($users);

                    ActivityLog::factory()->forUser($user)->forTenant($tenant)->create([
                        'action' => 'tenant.updated',
                        'description' => 'Tenant settings were updated',
                        'subject_type' => Tenant::class,
                        'subject_id' => $tenant->id,
                    ]);
                }
            }

            // Create other miscellaneous logs
            $miscLogsCount = fake()->numberBetween(10, 20);
            for ($i = 0; $i < $miscLogsCount; $i++) {
                $user = fake()->randomElement($users);
                $miscLogTypes = [
                    'badge.issued',
                    'badge.revoked',
                    'access.granted',
                    'access.denied',
                    'kiosk.used',
                    'document.uploaded',
                    'settings.changed',
                    'notification.sent',
                ];

                ActivityLog::factory()->forUser($user)->forTenant($tenant)->create([
                    'action' => fake()->randomElement($miscLogTypes),
                    'description' => fake()->sentence(),
                ]);
            }
        }

        $this->command->info('Activity logs seeded successfully.');
    }
}