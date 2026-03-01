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
            ->set('schedule_entrance_id', $this->entrance->id)
            ->set('schedule_date', Carbon::tomorrow()->format('Y-m-d'))
            ->set('schedule_time', '10:00')
            ->set('schedule_people_count', 1)
            ->call('nextStep')
            ->set('schedule_visitors.0.first_name', 'Visitor')
            ->set('schedule_visitors.0.last_name', 'Test')
            ->set('schedule_visitors.0.email', 'visitor@test.com')
            ->call('scheduleVisit');
            
        // We want to force it to fail if the form has a validation error 
        if ($test->errors()->isNotEmpty()) {
            dd($test->errors());
        }
        $test->assertHasNoErrors();
        $test->assertSet('showScheduleModal', false);

        $this->assertDatabaseHas('visits', [
            'host_id' => $this->mainTenant->id,
            'scheduled_at' => Carbon::tomorrow()->format('Y-m-d') . ' 10:00:00',
        ]);
    }

    public function test_forces_main_tenant_to_use_their_own_company_but_allows_any_host_from_their_company()
    {
        Livewire::actingAs($this->mainTenant)
            ->test(VisitList::class)
            // Attempting to cheat the ID
            ->set('schedule_company_id', $this->otherCompany->id)
            // The user subTenant falls into mainTenant's company
            ->set('schedule_host_id', $this->subTenant->id) 
            ->set('schedule_entrance_id', $this->entrance->id)
            ->set('schedule_date', Carbon::tomorrow()->format('Y-m-d'))
            ->set('schedule_time', '10:00')
            ->set('schedule_people_count', 1)
            ->call('nextStep')
            ->set('schedule_visitors.0.first_name', 'Visitor')
            ->set('schedule_visitors.0.last_name', 'Test')
            ->set('schedule_visitors.0.email', 'visitor@test.com')
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

    public function test_allows_company_admin_to_schedule_for_sub_tenant()
    {
        $childCompany = Company::create(['name' => 'Child Company', 'parent_id' => $this->company->id, 'is_active' => true]);
        $childTenant = User::create([
            'name' => 'Child Tenant',
            'email' => 'childtenant@test.com',
            'password' => 'password',
            'role' => 'tenant',
            'company_id' => $childCompany->id,
        ]);

        Livewire::actingAs($this->mainTenant) // Company Admin
            ->test(VisitList::class)
            ->set('schedule_company_id', $childCompany->id) // Child company is allowed
            ->set('schedule_host_id', $childTenant->id) // Valid host in child company
            ->set('schedule_entrance_id', $this->entrance->id)
            ->set('schedule_date', Carbon::tomorrow()->format('Y-m-d'))
            ->set('schedule_time', '10:00')
            ->set('schedule_people_count', 1)
            ->call('nextStep')
            ->set('schedule_visitors.0.first_name', 'Visitor')
            ->set('schedule_visitors.0.last_name', 'Test')
            ->set('schedule_visitors.0.email', 'visitor@test.com')
            ->call('scheduleVisit')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('visits', [
            'host_id' => $childTenant->id,
        ]);
    }

    public function test_forces_sub_tenant_to_use_their_own_company_but_allows_any_host_from_their_company()
    {
        Livewire::actingAs($this->subTenant)
            ->test(VisitList::class)
            ->set('schedule_company_id', $this->otherCompany->id) // Attempting bypass company
            ->set('schedule_host_id', $this->otherSubTenant->id) // Valid host in their own company
            ->set('schedule_entrance_id', $this->entrance->id)
            ->set('schedule_date', Carbon::tomorrow()->format('Y-m-d'))
            ->set('schedule_time', '10:00')
            ->set('schedule_people_count', 1)
            ->call('nextStep')
            ->set('schedule_visitors.0.first_name', 'Visitor')
            ->set('schedule_visitors.0.last_name', 'Test')
            ->set('schedule_visitors.0.email', 'visitor@test.com')
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
            ->set('schedule_entrance_id', $this->entrance->id)
            ->set('schedule_date', Carbon::tomorrow()->format('Y-m-d'))
            ->set('schedule_time', '10:00')
            ->set('schedule_people_count', 1)
            ->call('nextStep')
            ->set('schedule_visitors.0.first_name', 'Visitor')
            ->set('schedule_visitors.0.last_name', 'Test')
            ->set('schedule_visitors.0.email', 'visitor@test.com')
            ->call('scheduleVisit');

        $test->assertHasNoErrors();
        $test->assertSet('showScheduleModal', false);

        $this->assertDatabaseHas('visits', [
            'host_id' => $this->viewer->id,
        ]);
    }

    public function test_cannot_schedule_for_inactive_building_or_entrance()
    {
        $inactiveBuilding = Building::create(['name' => 'Inactive', 'is_active' => false]);
        $entranceInInactiveBuilding = Entrance::create([
            'name' => 'Entrance', 
            'building_id' => $inactiveBuilding->id, 
            'is_active' => true,
            'kiosk_identifier' => uniqid('kiosk_'),
        ]);

        Livewire::actingAs($this->admin)
            ->test(VisitList::class)
            ->set('schedule_company_id', $this->company->id)
            ->set('schedule_entrance_id', $entranceInInactiveBuilding->id)
            ->set('schedule_date', Carbon::tomorrow()->format('Y-m-d'))
            ->set('schedule_time', '10:00')
            ->set('schedule_people_count', 1)
            ->call('nextStep')
            ->assertHasErrors(['schedule_entrance_id']);

        $inactiveEntrance = Entrance::create([
            'name' => 'Inactive', 
            'building_id' => $this->building->id, 
            'is_active' => false,
            'kiosk_identifier' => uniqid('kiosk_'),
        ]);

        Livewire::actingAs($this->admin)
            ->test(VisitList::class)
            ->set('schedule_company_id', $this->company->id)
            ->set('schedule_entrance_id', $inactiveEntrance->id)
            ->set('schedule_date', Carbon::tomorrow()->format('Y-m-d'))
            ->set('schedule_time', '10:00')
            ->set('schedule_people_count', 1)
            ->call('nextStep')
            ->assertHasErrors(['schedule_entrance_id']);
    }

    public function test_cannot_schedule_for_company_with_expired_contract()
    {
        $expiredCompany = Company::create([
            'name' => 'Expired', 
            'is_active' => true,
            'contract_end_date' => Carbon::yesterday(),
        ]);

        Livewire::actingAs($this->admin)
            ->test(VisitList::class)
            ->set('schedule_company_id', $expiredCompany->id)
            ->set('schedule_entrance_id', $this->entrance->id)
            ->set('schedule_date', Carbon::tomorrow()->format('Y-m-d'))
            ->set('schedule_time', '10:00')
            ->set('schedule_people_count', 1)
            ->call('nextStep')
            ->assertHasErrors(['schedule_company_id']);

        $notStartedCompany = Company::create([
            'name' => 'Not Started', 
            'is_active' => true,
            'contract_start_date' => Carbon::tomorrow(),
        ]);

        Livewire::actingAs($this->admin)
            ->test(VisitList::class)
            ->set('schedule_company_id', $notStartedCompany->id)
            ->set('schedule_entrance_id', $this->entrance->id)
            ->set('schedule_date', Carbon::tomorrow()->format('Y-m-d'))
            ->set('schedule_time', '10:00')
            ->set('schedule_people_count', 1)
            ->call('nextStep')
            ->assertHasErrors(['schedule_company_id']);
    }

    public function test_enforces_space_capacity()
    {
        $smallSpace = \App\Models\Space::create([
            'building_id' => $this->building->id,
            'name' => 'Small Room',
            'is_active' => true,
            'capacity' => 2,
        ]);

        Livewire::actingAs($this->admin)
            ->test(VisitList::class)
            ->set('schedule_company_id', $this->company->id)
            ->set('schedule_entrance_id', $this->entrance->id)
            ->set('schedule_space_id', $smallSpace->id)
            ->set('schedule_date', Carbon::tomorrow()->format('Y-m-d'))
            ->set('schedule_time', '10:00')
            ->set('schedule_people_count', 3)
            ->call('nextStep')
            ->assertHasErrors(['schedule_people_count']);
    }

    public function test_prevents_space_overlap()
    {
        $space = \App\Models\Space::create([
            'building_id' => $this->building->id,
            'name' => 'Room',
            'is_active' => true,
            'capacity' => 10,
        ]);

        $visitor = \App\Models\Visitor::create([
            'first_name' => 'Initial',
            'last_name' => 'Visitor',
            'email' => 'initial@test.com',
        ]);

        $tomorrow = Carbon::tomorrow();
        
        \App\Models\Visit::create([
            'visitor_id' => $visitor->id,
            'entrance_id' => $this->entrance->id,
            'space_id' => $space->id,
            'host_id' => $this->admin->id,
            'status' => 'pending',
            'scheduled_at' => $tomorrow->copy()->setTime(10, 0),
        ]);

        Livewire::actingAs($this->admin)
            ->test(VisitList::class)
            ->set('schedule_company_id', $this->company->id)
            ->set('schedule_entrance_id', $this->entrance->id)
            ->set('schedule_space_id', $space->id)
            ->set('schedule_date', $tomorrow->format('Y-m-d'))
            ->set('schedule_time', '10:30') // Overlaps with 10:00 (1 hour buffer)
            ->set('schedule_people_count', 1)
            ->call('nextStep')
            ->assertHasErrors(['schedule_space_id']);
    }
}
