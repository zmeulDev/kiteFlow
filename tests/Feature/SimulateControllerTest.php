<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SimulateControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_simulate_controller_scenario()
    {
        // Create users and roles
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        // Create parent tenant
        $parentTenant = Tenant::create([
            'name' => 'Parent Company',
            'slug' => 'parent-company',
            'domain' => 'parent-company.kiteflow.test',
            'email' => 'parent@test.com',
            'status' => 'active',
        ]);

        // Create sub-tenant like in the controller
        $randomString = \Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(4));
        $subTenant = Tenant::forceCreate([
            'name' => 'New Subsidiary',
            'slug' => 'new-subsidiary-' . $randomString,
            'domain' => 'new-subsidiary-' . $randomString . '.kiteflow.test',
            'email' => 'contact@subsidiary.com',
            'phone' => '+1234567890',
            'parent_id' => $parentTenant->id,
            'status' => 'trial',
            'timezone' => 'UTC',
            'locale' => 'en',
            'currency' => 'USD',
        ]);

        // Check the database directly
        $dbRecord = \Illuminate\Support\Facades\DB::table('tenants')
            ->where('email', 'contact@subsidiary.com')
            ->first();

        dump([
            'parent_id from model' => $subTenant->parent_id,
            'parent_id from DB' => $dbRecord->parent_id,
            'expected parent_id' => $parentTenant->id,
        ]);

        $this->assertEquals($parentTenant->id, $subTenant->parent_id, 'Model parent_id mismatch');
        $this->assertEquals($parentTenant->id, $dbRecord->parent_id, 'DB parent_id mismatch');
    }

    public function test_with_auth_user()
    {
        // Create users and roles
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        // Create parent tenant
        $parentTenant = Tenant::create([
            'name' => 'Parent Company',
            'slug' => 'parent-company-2',
            'domain' => 'parent-company-2.kiteflow.test',
            'email' => 'parent2@test.com',
            'status' => 'active',
        ]);

        // Create tenant admin
        $tenantAdmin = User::factory()->create();
        $tenantAdmin->assignRole('admin');
        $parentTenant->users()->attach($tenantAdmin->id, ['is_owner' => true]);

        // Act as admin and check
        $this->actingAs($tenantAdmin);

        $randomString = \Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(4));
        $subTenant = Tenant::forceCreate([
            'name' => 'New Subsidiary',
            'slug' => 'new-subsidiary-2-' . $randomString,
            'domain' => 'new-subsidiary-2-' . $randomString . '.kiteflow.test',
            'email' => 'contact2@subsidiary.com',
            'phone' => '+1234567890',
            'parent_id' => $parentTenant->id,
            'status' => 'trial',
            'timezone' => 'UTC',
            'locale' => 'en',
            'currency' => 'USD',
        ]);

        $dbRecord = \Illuminate\Support\Facades\DB::table('tenants')
            ->where('email', 'contact2@subsidiary.com')
            ->first();

        dump([
            'parent_id from model' => $subTenant->parent_id,
            'parent_id from DB' => $dbRecord->parent_id,
            'expected parent_id' => $parentTenant->id,
        ]);

        $this->assertEquals($parentTenant->id, $subTenant->parent_id);
        $this->assertEquals($parentTenant->id, $dbRecord->parent_id);
    }
}