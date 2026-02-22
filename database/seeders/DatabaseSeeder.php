<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * This seeder covers all business logic scenarios for KiteFlow:
     * - Multi-tenant architecture with parent/child hierarchy
     * - User roles and permissions (super-admin, admin, receptionist, user)
     * - Facility management (buildings, zones, access points)
     * - Visitor management (profiles, visits, documents, blacklist)
     * - Meeting management (rooms, meetings, attendees, notifications)
     * - Parking management (spots, records with various states)
     * - Access control (logs for entry/exit tracking)
     * - Activity logging (comprehensive audit trail)
     * - Tenant settings (configuration per tenant)
     */
    public function run(): void
    {
        $this->command->info('Starting KiteFlow database seeding...');

        // Step 1: Seed roles and permissions first
        $this->call(RolesAndPermissionsSeeder::class);

        // Step 2: Seed tenants (multi-tenant setup)
        $this->call(TenantSeeder::class);

        // Step 3: Seed users with different roles
        $this->call(UserSeeder::class);

        // Step 4: Seed tenant settings (configuration)
        $this->call(TenantSettingSeeder::class);

        // Step 5: Seed facility management
        $this->call(BuildingSeeder::class);
        $this->call(ZoneSeeder::class);
        $this->call(AccessPointSeeder::class);

        // Step 6: Seed meeting rooms
        $this->call(MeetingRoomSeeder::class);

        // Step 7: Seed visitor management
        $this->call(VisitorSeeder::class);
        $this->call(VisitorVisitSeeder::class);
        $this->call(VisitorDocumentSeeder::class);

        // Step 8: Seed meeting management
        $this->call(MeetingSeeder::class);
        $this->call(MeetingAttendeeSeeder::class);
        $this->call(MeetingNotificationSeeder::class);

        // Step 9: Seed parking management
        $this->call(ParkingSeeder::class);

        // Step 10: Seed access control
        $this->call(AccessLogSeeder::class);

        // Step 11: Seed activity logs
        $this->call(ActivityLogSeeder::class);

        $this->command->info('KiteFlow database seeding completed successfully!');
        $this->command->newLine();
        $this->command->info('Summary:');
        $this->command->info('- Tenants: Active, suspended, inactive, trial, parent/child hierarchy');
        $this->command->info('- Users: super-admin, admin, receptionist, user roles; active/inactive states');
        $this->command->info('- Buildings: Multiple per tenant, active/inactive states');
        $this->command->info('- Zones: Office, conference, lobby, secure, restricted, public types');
        $this->command->info('- Access Points: Door, gate, kiosk; entry/exit directions; active/inactive');
        $this->command->info('- Meeting Rooms: Various capacities and amenities');
        $this->command->info('- Visitors: Regular and blacklisted states');
        $this->command->info('- Visitor Visits: Checked-in, checked-out, cancelled; various badge types');
        $this->command->info('- Visitor Documents: ID cards, passports, NDAs, photos, signatures');
        $this->command->info('- Meetings: Scheduled, ongoing, completed, cancelled, recurring');
        $this->command->info('- Meeting Types: In-person, virtual, hybrid');
        $this->command->info('- Meeting Attendees: Users and visitors; required/optional; accepted/declined/tentative');
        $this->command->info('- Meeting Notifications: Created, updated, cancelled, reminder, starting_soon');
        $this->command->info('- Parking Spots: Standard, compact, large, EV, disabled, VIP; available/occupied/reserved');
        $this->command->info('- Parking Records: Active, completed, paid states');
        $this->command->info('- Access Logs: Entry/exit; granted/denied; various access methods');
        $this->command->info('- Activity Logs: Login/logout, visitor actions, meeting actions, tenant updates');
        $this->command->info('- Tenant Settings: Visitor, meeting, parking, access, notification configs');
    }
}