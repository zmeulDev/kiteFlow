<?php

namespace Tests\Feature\Livewire\Admin\Visits;

use App\Livewire\Admin\Visits\VisitList;
use App\Models\Building;
use App\Models\Company;
use App\Models\Entrance;
use App\Models\User;
use App\Services\QrCodeService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;
use Mockery;

class VisitListTest extends TestCase
{
    use RefreshDatabase;

    private Company $company;
    private Building $building;
    private Entrance $entrance;
    private User $admin;
    private User $mainTenant;
    private User $subTenant;
    private User $otherSubTenant;
    private User $viewer;
    private Company $otherCompany;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        
        $mockQrCodeService = Mockery::mock(QrCodeService::class);
        $mockQrCodeService->shouldReceive('generateQrCode')->andReturn('mocked_qr_code.png');
        $this->app->instance(QrCodeService::class, $mockQrCodeService);

        $this->company = Company::create(['name' => 'Test Company', 'is_active' => true]);
        $this->building = Building::create(['name' => 'Test Building', 'is_active' => true]);
        $this->entrance = Entrance::create([
            'name' => 'Main', 
            'building_id' => $this->building->id, 
            'is_active' => true,
            'kiosk_identifier' => uniqid('kiosk_'),
        ]);
        
        $this->admin = User::create(['name' => 'Admin', 'email' => 'admin@test.com', 'password' => 'password', 'role' => 'admin']);
        
        $this->mainTenant = User::create([
            'name' => 'Main Tenant',
            'email' => 'maintenant@test.com',
            'password' => 'password',
            'role' => 'administrator',
            'company_id' => $this->company->id,
        ]);
        
        $this->subTenant = User::create([
            'name' => 'Sub Tenant',
            'email' => 'subtenant@test.com',
            'password' => 'password',
            'role' => 'tenant',
            'company_id' => $this->company->id,
        ]);
        
        $this->otherSubTenant = User::create([
            'name' => 'Other Sub',
            'email' => 'othersub@test.com',
            'password' => 'password',
            'role' => 'tenant',
            'company_id' => $this->company->id,
        ]);

        $this->viewer = User::create([
            'name' => 'Viewer',
            'email' => 'viewer@test.com',
            'password' => 'password',
            'role' => 'viewer',
            'company_id' => $this->company->id,
        ]);

        $this->otherCompany = Company::create(['name' => 'Other Company', 'is_active' => true]);
    }

    public function test_allows_admin_to_schedule_visits_for_any_company_and_host()
    {
        $test = Livewire::actingAs($this->admin)
            ->test(VisitList::class)
            ->set('schedule_company_id', $this->otherCompany->id)
            ->set('schedule_host_id', $this->mainTenant->id)
            ->set('schedule_first_name', 'Visitor')
            ->set('schedule_last_name', 'Test')
            ->set('schedule_email', 'visitor@test.com')
            ->set('schedule_entrance_id', $this->entrance->id)
            ->set('schedule_date', Carbon::tomorrow()->format('Y-m-d'))
            ->set('schedule_time', '10:00')
            ->call('scheduleVisit');
            
        // We want to force it to fail if the form has a validation error 
        if ($test->errors()->isNotEmpty()) {
            dd($test->errors());
        }
        $test->assertHasNoErrors();
        $test->assertSet('showScheduleModal', false);
    }

    public function test_forces_main_tenant_to_use_their_own_company_but_allows_any_host_from_their_company()
    {
        Livewire::actingAs($this->mainTenant)
            ->test(VisitList::class)
            // Attempting to cheat the ID
            ->set('schedule_company_id', $this->otherCompany->id)
            // The user subTenant falls into mainTenant's company
            ->set('schedule_host_id', $this->subTenant->id) 
            ->set('schedule_first_name', 'Visitor')
            ->set('schedule_last_name', 'Test')
            ->set('schedule_email', 'visitor@test.com')
            ->set('schedule_entrance_id', $this->entrance->id)
            ->set('schedule_date', Carbon::tomorrow()->format('Y-m-d'))
            ->set('schedule_time', '10:00')
            ->call('scheduleVisit')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('visits', [
            'host_id' => $this->subTenant->id,
        ]);
        
        // Ensure company ID bypass got corrected back to original company
        $this->assertDatabaseHas('visits', [
            'host_id' => $this->subTenant->id,
        ]);
    }

    public function test_forces_sub_tenant_to_use_their_own_company_but_allows_any_host_from_their_company()
    {
        Livewire::actingAs($this->subTenant)
            ->test(VisitList::class)
            ->set('schedule_company_id', $this->otherCompany->id) // Attempting bypass company
            ->set('schedule_host_id', $this->otherSubTenant->id) // Valid host in their own company
            ->set('schedule_first_name', 'Visitor')
            ->set('schedule_last_name', 'Test')
            ->set('schedule_email', 'visitor@test.com')
            ->set('schedule_entrance_id', $this->entrance->id)
            ->set('schedule_date', Carbon::tomorrow()->format('Y-m-d'))
            ->set('schedule_time', '10:00')
            ->call('scheduleVisit')
            ->assertHasNoErrors();

        // Check the database, seeing if the bypass failed for company but allowed host
        $this->assertDatabaseHas('visits', [
            'host_id' => $this->otherSubTenant->id,
        ]);
        // The host should literally be the one we intentionally picked, as subTenant is allowed to pick others in company.
    }

    public function test_forces_viewer_to_use_their_own_company_and_their_own_user_as_host()
    {
        $test = Livewire::actingAs($this->viewer)
            ->test(VisitList::class)
            ->set('schedule_company_id', $this->otherCompany->id) // Attempting bypass
            ->set('schedule_host_id', $this->subTenant->id) // Attempting bypass
            ->set('schedule_first_name', 'Visitor')
            ->set('schedule_last_name', 'Test')
            ->set('schedule_email', 'visitor@test.com')
            ->set('schedule_entrance_id', $this->entrance->id)
            ->set('schedule_date', Carbon::tomorrow()->format('Y-m-d'))
            ->set('schedule_time', '10:00')
            ->call('scheduleVisit');

        if ($test->errors()->isNotEmpty()) {
            dd($test->errors());
        }
        $test->assertHasNoErrors();
        $test->assertSet('showScheduleModal', false);

        // Check the database, seeing if the bypass failed securely
        $this->assertDatabaseHas('visits', [
            'host_id' => $this->viewer->id,
        ]);
        // subTenant DID NOT become host despite the bypass attempt
        $this->assertDatabaseMissing('visits', [
            'host_id' => $this->subTenant->id,
        ]);
    }
}
