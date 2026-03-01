<?php

namespace Tests\Unit;

use App\Models\Company;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_all_company_ids(): void
    {
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();
        $admin = User::factory()->create(['role' => 'admin', 'company_id' => $company1->id]);

        $managedIds = $admin->getManagedCompanyIds();

        $this->assertContains($company1->id, $managedIds);
        $this->assertContains($company2->id, $managedIds);
    }

    public function test_company_administrator_can_manage_own_and_child_company_ids(): void
    {
        $parent = Company::factory()->create();
        $child = Company::factory()->create(['parent_id' => $parent->id]);
        $other = Company::factory()->create();
        
        $manager = User::factory()->create([
            'role' => 'administrator',
            'company_id' => $parent->id
        ]);

        $managedIds = $manager->getManagedCompanyIds();

        $this->assertContains($parent->id, $managedIds);
        $this->assertContains($child->id, $managedIds);
        $this->assertNotContains($other->id, $managedIds);
    }

    public function test_receptionist_can_manage_own_and_child_company_ids(): void
    {
        $parent = Company::factory()->create();
        $child = Company::factory()->create(['parent_id' => $parent->id]);
        $other = Company::factory()->create();

        $receptionist = User::factory()->create([
            'role' => 'receptionist',
            'company_id' => $parent->id
        ]);

        $managedIds = $receptionist->getManagedCompanyIds();

        $this->assertContains($parent->id, $managedIds);
        $this->assertContains($child->id, $managedIds);
        $this->assertNotContains($other->id, $managedIds);
    }

    public function test_viewer_can_only_manage_own_company_id(): void
    {
        $company = Company::factory()->create();
        $viewer = User::factory()->create(['role' => 'viewer', 'company_id' => $company->id]);

        $managedIds = $viewer->getManagedCompanyIds();

        $this->assertCount(1, $managedIds);
        $this->assertEquals($company->id, $managedIds[0]);
    }

    public function test_has_permission_basic_logic(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        // Default admin behavior when no settings exist
        $this->assertTrue($admin->hasPermission('any_permission'));

        // Mock settings
        Setting::set('rbac_permissions', [
            'admin' => ['manage_users'],
            'viewer' => ['view_dashboard']
        ]);

        $this->assertTrue($admin->hasPermission('manage_users'));
        $this->assertFalse($admin->hasPermission('view_dashboard'));

        $viewer = User::factory()->create(['role' => 'viewer']);
        $this->assertTrue($viewer->hasPermission('view_dashboard'));
        $this->assertFalse($viewer->hasPermission('manage_users'));
    }

    public function test_default_role_permissions_for_settings(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        \Livewire\Livewire::actingAs($admin)
            ->test(\App\Livewire\Admin\Settings\RolePermissions::class)
            ->assertSet('permissions.administrator', function ($value) {
                return in_array('manage_settings', $value);
            })
            ->assertSet('permissions.receptionist', function ($value) {
                return !in_array('manage_settings', $value);
            });
    }
}
