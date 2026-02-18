<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Visitor;
use App\Models\Visit;
use App\Models\MeetingRoom;
use App\Models\Booking;
use App\Models\Location;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create a Test Hub (Coworking Owner)
        $hub = Tenant::create([
            'name' => 'Jucu Hub Coworking',
            'slug' => 'jucu-hub',
            'is_hub' => true,
            'plan' => 'pro',
            'status' => 'active',
            'contact_name' => 'Zmeul Owner',
            'contact_email' => 'admin@zml.ro',
            'contact_phone' => '+40728528323',
            'billing_address' => 'Cluj-Napoca, Romania',
            'vat_id' => 'RO12345678',
            'monthly_rate' => 99.00,
            'subscription_ends_at' => Carbon::now()->addMonths(6),
            'settings' => [
                'primary_color' => '#4f46e5',
                'require_photo' => false,
            ],
        ]);

        // 2. Create the Hub Admin
        User::create([
            'tenant_id' => $hub->id,
            'name' => 'Zmeul Admin',
            'email' => 'admin@zml.ro',
            'password' => Hash::make('password'),
            'is_super_admin' => true,
        ]);

        // 3. Create Locations for the Hub
        $locA = Location::create(['tenant_id' => $hub->id, 'name' => 'Building A', 'slug' => 'building-a']);
        $locD = Location::create(['tenant_id' => $hub->id, 'name' => 'Building D', 'slug' => 'building-d']);

        // 4. Create 5 Tenants inside Jucu Hub
        $companies = ['CyberDyne Systems', 'Stark Industries', 'Wayne Enterprises', 'Umbrella Corp', 'Hooli'];
        foreach ($companies as $name) {
            $child = Tenant::create([
                'parent_id' => $hub->id,
                'name' => $name,
                'slug' => \Illuminate\Support\Str::slug($name),
                'plan' => 'free',
                'status' => 'active',
            ]);
            
            User::create([
                'tenant_id' => $child->id,
                'name' => $name . ' Manager',
                'email' => strtolower(str_replace(' ', '.', $name)) . '@example.com',
                'password' => Hash::make('password'),
            ]);
        }

        // 5. Create Meeting Rooms linked to Locations
        $roomA = MeetingRoom::create([
            'tenant_id' => $hub->id,
            'location_id' => $locA->id,
            'name' => 'Conference Room Alpha',
            'capacity' => 12,
            'amenities' => ['4K TV', 'Whiteboard', 'Coffee Machine'],
        ]);

        $roomC1 = MeetingRoom::create([
            'tenant_id' => $hub->id,
            'location_id' => $locD->id,
            'name' => 'Room C1',
            'capacity' => 4,
            'amenities' => ['Whiteboard', 'Fast WiFi'],
        ]);

        $roomB = MeetingRoom::create([
            'tenant_id' => $hub->id,
            'location_id' => $locD->id,
            'name' => 'Focus Pod 1',
            'capacity' => 2,
            'amenities' => ['AC', 'Fast WiFi'],
        ]);

        // 6. Create some Visitors
        $vData = [
            ['first' => 'Ion', 'last' => 'Popescu', 'email' => 'ion@example.com'],
            ['first' => 'Elena', 'last' => 'Radu', 'email' => 'elena@example.com'],
        ];

        $visitors = [];
        foreach ($vData as $data) {
            $visitors[] = Visitor::create([
                'tenant_id' => $hub->id,
                'first_name' => $data['first'],
                'last_name' => $data['last'],
                'email' => $data['email'],
                'phone' => '+40700000000',
            ]);
        }

        // 7. Create some Bookings to test availability
        $scheduled = Carbon::now()->addDays(1)->setTime(10, 0);
        $v1 = Visit::create([
            'tenant_id' => $hub->id,
            'visitor_id' => $visitors[0]->id,
            'user_id' => 1,
            'purpose' => 'Meeting',
            'scheduled_at' => $scheduled,
        ]);

        Booking::create([
            'tenant_id' => $hub->id,
            'meeting_room_id' => $roomC1->id,
            'visit_id' => $v1->id,
            'starts_at' => $scheduled,
            'ends_at' => $scheduled->copy()->addHour(),
        ]);
    }
}
