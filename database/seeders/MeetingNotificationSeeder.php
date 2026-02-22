<?php

namespace Database\Seeders;

use App\Models\Meeting;
use App\Models\MeetingNotification;
use Illuminate\Database\Seeder;

class MeetingNotificationSeeder extends Seeder
{
    public function run(): void
    {
        $meetings = Meeting::all();

        foreach ($meetings as $meeting) {
            // Skip cancelled meetings for certain notifications
            if ($meeting->status === 'cancelled') {
                // Create cancellation notification
                MeetingNotification::factory()->cancelled()->create([
                    'meeting_id' => $meeting->id,
                ]);

                continue;
            }

            // Create meeting created notification
            MeetingNotification::factory()->created()->create([
                'meeting_id' => $meeting->id,
            ]);

            // Create reminder notification for scheduled meetings
            if ($meeting->status === 'scheduled') {
                MeetingNotification::factory()->reminder()->create([
                    'meeting_id' => $meeting->id,
                ]);

                // Create starting soon notification for meetings in the near future
                if ($meeting->start_at->isAfter(now()->addMinutes(15))) {
                    MeetingNotification::factory()->startingSoon()->create([
                        'meeting_id' => $meeting->id,
                    ]);
                }
            }

            // Create starting soon notification for ongoing meetings
            if ($meeting->status === 'in_progress') {
                MeetingNotification::factory()->startingSoon()->create([
                    'meeting_id' => $meeting->id,
                    'sent_at' => now()->subMinutes(5),
                ]);
            }

            // Create rescheduled notification for some meetings
            if (fake()->boolean(10)) {
                MeetingNotification::factory()->rescheduled()->create([
                    'meeting_id' => $meeting->id,
                ]);
            }

            // Create some pending notifications
            if (fake()->boolean(20)) {
                MeetingNotification::factory()->pending()->create([
                    'meeting_id' => $meeting->id,
                ]);
            }

            // Create some failed notifications
            if (fake()->boolean(5)) {
                MeetingNotification::factory()->failed()->create([
                    'meeting_id' => $meeting->id,
                ]);
            }
        }

        $this->command->info('Meeting notifications seeded successfully.');
    }
}