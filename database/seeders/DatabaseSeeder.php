<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\Company;
use App\Models\Entrance;
use App\Models\KioskSetting;
use App\Models\Setting;
use App\Models\Space;
use App\Models\User;
use App\Models\Visit;
use App\Models\Visitor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create buildings and entrances
        $buildings = Building::factory()->count(2)->create()->each(function ($building) {
            $entrances = Entrance::factory()->count(2)->create(['building_id' => $building->id]);
            foreach ($entrances as $entrance) {
                KioskSetting::factory()->create(['entrance_id' => $entrance->id]);
            }
            Space::factory()->count(3)->create(['building_id' => $building->id]);
        });

        // Create companies
        $acme = Company::factory()->create([
            'name' => 'Acme Corporation',
            'email' => 'info@acme.com',
        ]);

        $subCompany = Company::factory()->create([
            'name' => 'Acme Sub-Division',
            'parent_id' => $acme->id,
        ]);

        $techSolutions = Company::factory()->create([
            'name' => 'Tech Solutions Ltd',
        ]);

        // Create users for Acme
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'company_id' => $acme->id,
        ]);

        $receptionist = User::factory()->create([
            'name' => 'Receptionist',
            'email' => 'reception@example.com',
            'role' => 'receptionist',
            'company_id' => $acme->id,
        ]);

        $manager = User::factory()->create([
            'name' => 'Company Admin',
            'email' => 'manager@acme.com',
            'role' => 'administrator',
            'company_id' => $acme->id,
        ]);

        $subManager = User::factory()->create([
            'name' => 'Sub-Division Manager',
            'email' => 'submanager@acme.com',
            'role' => 'administrator',
            'company_id' => $subCompany->id,
        ]);

        // Create some random employees
        User::factory()->count(10)->create(['company_id' => $acme->id, 'role' => 'viewer']);
        User::factory()->count(5)->create(['company_id' => $techSolutions->id, 'role' => 'viewer']);

        // Create visitors and visits
        $visitors = Visitor::factory()->count(20)->create();
        $spaces = Space::all();
        $entrances = Entrance::all();
        $hosts = User::where('role', '!=', 'admin')->get();

        foreach ($visitors as $visitor) {
            Visit::factory()->count(fake()->numberBetween(1, 3))->create([
                'visitor_id' => $visitor->id,
                'entrance_id' => $entrances->random()->id,
                'space_id' => $spaces->random()->id,
                'host_id' => $hosts->random()->id,
                'status' => fake()->randomElement(['pending', 'checked_in', 'checked_out']),
                'check_in_at' => fake()->dateTimeBetween('-1 week', 'now'),
            ]);
        }

        // Create default settings
        Setting::set('business_name', 'Acme Corporation');
        Setting::set('business_address', '123 Business Street, City, Country');
        Setting::set('business_phone', '+1 234 567 890');
        Setting::set('business_email', 'info@acme.com');
        Setting::set('data_retention_days', 90);
    }
}