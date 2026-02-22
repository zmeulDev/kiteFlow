<?php

namespace Database\Seeders;

use App\Models\Meeting;
use App\Models\MeetingAttendee;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Database\Seeder;

class MeetingAttendeeSeeder extends Seeder
{
    public function run(): void
    {
        $meetings = Meeting::all();

        foreach ($meetings as $meeting) {
            // Get users who belong to this meeting's tenant
            $tenantUsers = User::whereHas('tenants', fn ($q) => $q->where('tenant_id', $meeting->tenant_id))->get();
            $tenantVisitors = Visitor::where('tenant_id', $meeting->tenant_id)->get();

            // Add user attendees
            $userCount = fake()->numberBetween(2, min(5, $tenantUsers->count()));
            $selectedUsers = $tenantUsers->random($userCount);

            foreach ($selectedUsers as $index => $user) {
                $status = fake()->randomElement(['pending', 'accepted', 'declined', 'tentative']);
                $type = $index === 0 ? 'required' : 'optional';

                MeetingAttendee::factory()->forUser($user)->create([
                    'meeting_id' => $meeting->id,
                    'type' => $type,
                    'status' => $status,
                    'responded_at' => $status !== 'pending' ? now()->subHours(fake()->numberBetween(1, 24)) : null,
                    'notes' => $status === 'declined' ? fake()->sentence() : null,
                ]);
            }

            // Add visitor attendees (for some meetings)
            if ($meeting->status !== 'cancelled' && $tenantVisitors->isNotEmpty()) {
                $visitorCount = fake()->numberBetween(0, min(3, $tenantVisitors->count()));

                if ($visitorCount > 0) {
                    $selectedVisitors = $tenantVisitors->random($visitorCount);

                    foreach ($selectedVisitors as $visitor) {
                        MeetingAttendee::factory()->forVisitor($visitor)->create([
                            'meeting_id' => $meeting->id,
                            'type' => 'optional',
                            'status' => fake()->randomElement(['accepted', 'pending']),
                            'responded_at' => now()->subHours(fake()->numberBetween(1, 12)),
                        ]);
                    }
                }
            }
        }

        $this->command->info('Meeting attendees seeded successfully.');
    }
}