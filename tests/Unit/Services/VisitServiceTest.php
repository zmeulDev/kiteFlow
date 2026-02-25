<?php

namespace Tests\Unit\Services;

use App\Models\Entrance;
use App\Models\Visit;
use App\Models\Visitor;
use App\Services\QrCodeService;
use App\Services\VisitService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class VisitServiceTest extends TestCase
{
    use RefreshDatabase;

    protected VisitService $visitService;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock QrCodeService
        $qrCodeService = Mockery::mock(QrCodeService::class);
        $qrCodeService->shouldReceive('generateQrCode')->andReturn('test-qr-code-123');

        $this->visitService = new VisitService($qrCodeService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_creates_a_new_visitor_and_visit(): void
    {
        $entrance = Entrance::factory()->create();

        $visitorData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
        ];

        $visitData = [
            'host_name' => 'Jane Smith',
            'host_email' => 'jane@company.com',
            'purpose' => 'Business meeting',
        ];

        $visit = $this->visitService->createVisit($visitorData, $visitData, $entrance);

        $this->assertInstanceOf(Visit::class, $visit);
        $this->assertEquals('pending', $visit->status);
        $this->assertEquals('test-qr-code-123', $visit->qr_code);
        $this->assertEquals($entrance->id, $visit->entrance_id);

        $this->assertDatabaseHas('visitors', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
        ]);
    }

    #[Test]
    public function it_reuses_existing_visitor(): void
    {
        $entrance = Entrance::factory()->create();
        $existingVisitor = Visitor::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
        ]);

        $visitorData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '+9999999999', // Different phone
        ];

        $visitData = [
            'host_name' => 'Jane Smith',
            'purpose' => 'Follow-up visit',
        ];

        $visit = $this->visitService->createVisit($visitorData, $visitData, $entrance);

        $this->assertEquals($existingVisitor->id, $visit->visitor_id);
        $this->assertEquals(1, Visitor::count()); // No new visitor created
    }

    #[Test]
    public function it_checks_in_a_visit(): void
    {
        $visit = Visit::factory()->pending()->create();

        $consentData = [
            'gdpr' => true,
            'nda' => true,
            'signature' => 'base64-signature',
            'photo_path' => 'photos/visitor.jpg',
        ];

        $result = $this->visitService->checkIn($visit, $consentData);

        $this->assertEquals('checked_in', $result->status);
        $this->assertNotNull($result->check_in_at);
        $this->assertNotNull($result->gdpr_consent_at);
        $this->assertNotNull($result->nda_consent_at);
        $this->assertEquals('base64-signature', $result->signature);
        $this->assertEquals('photos/visitor.jpg', $result->photo_path);
    }

    #[Test]
    public function it_checks_in_without_consent_data(): void
    {
        $visit = Visit::factory()->pending()->create();

        $result = $this->visitService->checkIn($visit, []);

        $this->assertEquals('checked_in', $result->status);
        $this->assertNotNull($result->check_in_at);
        $this->assertNull($result->gdpr_consent_at);
        $this->assertNull($result->nda_consent_at);
    }

    #[Test]
    public function it_checks_out_a_visit(): void
    {
        $visit = Visit::factory()->checkedIn()->create();

        $result = $this->visitService->checkOut($visit);

        $this->assertEquals('checked_out', $result->status);
        $this->assertNotNull($result->check_out_at);
    }

    #[Test]
    public function it_finds_visit_by_qr_code(): void
    {
        $visit = Visit::factory()->create(['qr_code' => 'unique-qr-code']);

        $found = $this->visitService->findByQrCode('unique-qr-code');

        $this->assertNotNull($found);
        $this->assertTrue($visit->is($found));
    }

    #[Test]
    public function it_returns_null_for_nonexistent_qr_code(): void
    {
        $found = $this->visitService->findByQrCode('nonexistent-code');

        $this->assertNull($found);
    }

    #[Test]
    public function it_gets_active_visits(): void
    {
        $entrance = Entrance::factory()->create();
        Visit::factory()->checkedIn()->create(['entrance_id' => $entrance->id]);
        Visit::factory()->pending()->create(['entrance_id' => $entrance->id]);
        Visit::factory()->checkedOut()->create(['entrance_id' => $entrance->id]);

        $activeVisits = $this->visitService->getActiveVisits();

        $this->assertCount(1, $activeVisits);
        $this->assertEquals('checked_in', $activeVisits->first()->status);
    }

    #[Test]
    public function it_gets_active_visits_for_specific_entrance(): void
    {
        $entrance1 = Entrance::factory()->create();
        $entrance2 = Entrance::factory()->create();
        Visit::factory()->checkedIn()->create(['entrance_id' => $entrance1->id]);
        Visit::factory()->checkedIn()->create(['entrance_id' => $entrance2->id]);

        $activeVisits = $this->visitService->getActiveVisits($entrance1);

        $this->assertCount(1, $activeVisits);
        $this->assertEquals($entrance1->id, $activeVisits->first()->entrance_id);
    }

    #[Test]
    public function it_gets_todays_visits(): void
    {
        $entrance = Entrance::factory()->create();
        Visit::factory()->checkedIn()->create([
            'entrance_id' => $entrance->id,
            'check_in_at' => now(),
        ]);
        Visit::factory()->checkedIn()->create([
            'entrance_id' => $entrance->id,
            'check_in_at' => now()->subDay(),
        ]);

        $todaysVisits = $this->visitService->getTodaysVisits();

        $this->assertCount(1, $todaysVisits);
    }

    #[Test]
    public function it_loads_relationships_for_active_visits(): void
    {
        Visit::factory()->checkedIn()->create();

        $activeVisits = $this->visitService->getActiveVisits();

        $this->assertTrue($activeVisits->first()->relationLoaded('visitor'));
        $this->assertTrue($activeVisits->first()->relationLoaded('entrance'));
    }
}