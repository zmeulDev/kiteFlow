<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\SubTenant;
use App\Models\User;
use App\Models\Building;
use App\Models\MeetingRoom;
use App\Models\Visitor;
use App\Models\Visit;
use Illuminate\Database\Seeder;

class VisitorManagementSeeder extends Seeder
{
    public function run(): void
    {
        // Create a demo tenant
        $tenant = Tenant::create([
            'name' => 'Acme Corporation',
            'slug' => 'acme-corp',
            'address' => '123 Business Street',
            'city' => 'San Francisco',
            'country' => 'USA',
            'phone' => '+1 555-0100',
            'email' => 'reception@acme.com',
            'contact_person' => 'John Manager',
            'gdpr_retention_months' => 6,
            'nda_text' => 'By checking in, you agree to our NDA terms...',
            'terms_text' => 'By checking in, you agree to our visitor terms...',
            'is_active' => true,
        ]);

        // Create sub-tenants (departments)
        $departments = ['Sales', 'Engineering', 'HR', 'Executive'];
        foreach ($departments as $dept) {
            SubTenant::create([
                'tenant_id' => $tenant->id,
                'name' => $dept,
                'slug' => strtolower($dept),
                'is_active' => true,
            ]);
        }

        // Create admin user
        $admin = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Admin User',
            'email' => 'admin@acme.com',
            'password' => bcrypt('password'),
            'role' => 'tenant_admin',
            'is_active' => true,
        ]);

        // Create regular user (host)
        $host = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'John Host',
            'email' => 'john@acme.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'is_active' => true,
        ]);

        // Create buildings
        $hq = Building::create([
            'tenant_id' => $tenant->id,
            'name' => 'Headquarters',
            'address' => '123 Business Street',
            'city' => 'San Francisco',
            'country' => 'USA',
            'is_active' => true,
        ]);

        // Create meeting rooms
        $rooms = [
            ['name' => 'Conference Room A', 'capacity' => 10, 'floor' => '1'],
            ['name' => 'Conference Room B', 'capacity' => 6, 'floor' => '1'],
            ['name' => 'Meeting Room 1', 'capacity' => 4, 'floor' => '2'],
            ['name' => 'Meeting Room 2', 'capacity' => 4, 'floor' => '2'],
            ['name' => 'Executive Boardroom', 'capacity' => 20, 'floor' => '3'],
        ];

        foreach ($rooms as $room) {
            MeetingRoom::create([
                'tenant_id' => $tenant->id,
                'building_id' => $hq->id,
                'name' => $room['name'],
                'capacity' => $room['capacity'],
                'floor' => $room['floor'],
                'amenities' => json_encode(['projector', 'whiteboard', 'video_conferencing']),
                'is_active' => true,
            ]);
        }

        // Create sample visitors
        $visitors = [
            ['first_name' => 'Alice', 'last_name' => 'Johnson', 'email' => 'alice@example.com', 'company' => 'Tech Corp'],
            ['first_name' => 'Bob', 'last_name' => 'Smith', 'email' => 'bob@example.com', 'company' => 'Business LLC'],
            ['first_name' => 'Carol', 'last_name' => 'Williams', 'email' => 'carol@example.com', 'company' => 'Startup Inc'],
        ];

        foreach ($visitors as $v) {
            $visitor = Visitor::create(array_merge($v, [
                'tenant_id' => $tenant->id,
                'phone' => '+1 555-' . rand(1000, 9999),
            ]));

            // Create a pre-registered visit
            Visit::create([
                'visitor_id' => $visitor->id,
                'tenant_id' => $tenant->id,
                'host_user_id' => $host->id,
                'meeting_room_id' => rand(1, 3),
                'building_id' => $hq->id,
                'visit_code' => strtoupper(substr(md5(uniqid()), 0, 8)),
                'scheduled_start' => now()->addHours(rand(1, 5)),
                'scheduled_end' => now()->addHours(rand(6, 8)),
                'purpose' => 'Business Meeting',
                'status' => 'pre_registered',
            ]);
        }

        $this->command->info('Demo data seeded successfully!');
        $this->command->info('Admin login: admin@acme.com / password');
    }
}
