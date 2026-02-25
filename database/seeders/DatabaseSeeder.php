<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\Company;
use App\Models\Entrance;
use App\Models\KioskSetting;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create companies
        $acmeCompany = Company::create([
            'name' => 'Acme Corporation',
            'address' => '123 Business Street, City, Country',
            'phone' => '+1 234 567 890',
            'email' => 'info@acme.com',
            'contact_person' => 'John Smith',
            'is_active' => true,
        ]);

        $techCompany = Company::create([
            'name' => 'Tech Solutions Ltd',
            'address' => '456 Innovation Avenue, Tech City',
            'phone' => '+1 555 123 4567',
            'email' => 'contact@techsolutions.com',
            'contact_person' => 'Jane Doe',
            'is_active' => true,
        ]);

        $globalCompany = Company::create([
            'name' => 'Global Services Inc',
            'address' => '789 Enterprise Road, Metro City',
            'phone' => '+1 888 999 0000',
            'email' => 'hello@globalservices.io',
            'contact_person' => 'Bob Johnson',
            'is_active' => true,
        ]);

        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
            'company_id' => $acmeCompany->id,
        ]);

        // Create receptionist user
        User::create([
            'name' => 'Receptionist',
            'email' => 'reception@example.com',
            'password' => Hash::make('password'),
            'role' => 'receptionist',
            'is_active' => true,
            'company_id' => $acmeCompany->id,
        ]);

        // Create buildings
        $building1 = Building::create([
            'name' => 'Main Building',
            'address' => '123 Main Street',
            'is_active' => true,
        ]);

        $building2 = Building::create([
            'name' => 'Annex Building',
            'address' => '456 Side Avenue',
            'is_active' => true,
        ]);

        // Create entrances for each building
        $entrance1 = Entrance::create([
            'building_id' => $building1->id,
            'name' => 'Main Entrance',
            'kiosk_identifier' => 'main-building-main',
            'is_active' => true,
        ]);

        $entrance2 = Entrance::create([
            'building_id' => $building1->id,
            'name' => 'Side Entrance',
            'kiosk_identifier' => 'main-building-side',
            'is_active' => true,
        ]);

        $entrance3 = Entrance::create([
            'building_id' => $building2->id,
            'name' => 'Front Entrance',
            'kiosk_identifier' => 'annex-front',
            'is_active' => true,
        ]);

        $entrance4 = Entrance::create([
            'building_id' => $building2->id,
            'name' => 'Back Entrance',
            'kiosk_identifier' => 'annex-back',
            'is_active' => true,
        ]);

        // Create kiosk settings for each entrance
        foreach ([$entrance1, $entrance2, $entrance3, $entrance4] as $entrance) {
            KioskSetting::create([
                'entrance_id' => $entrance->id,
                'welcome_message' => 'Welcome! Please check in below.',
                'background_color' => '#ffffff',
                'primary_color' => '#3b82f6',
                'require_photo' => false,
                'require_signature' => true,
                'show_nda' => false,
                'gdpr_text' => 'I consent to the collection and processing of my personal data for the purpose of visitor management, in accordance with the General Data Protection Regulation (GDPR). I understand that my data will be stored securely and used only for the purposes outlined in the privacy policy.',
                'nda_text' => 'I agree to maintain the confidentiality of any proprietary information I may encounter during my visit. I will not disclose, copy, or use any such information without proper authorization.',
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