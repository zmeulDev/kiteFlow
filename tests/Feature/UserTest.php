<?php

namespace Tests\Feature;

use App\Models\Meeting;
use App\Models\MeetingRoom;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Visitor;
use App\Models\VisitorVisit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $this->tenant = Tenant::factory()->create();
        $this->user = User::factory()->create();
        $this->user->assignRole('user');
        $this->tenant->users()->attach($this->user->id, ['is_owner' => false]);
    }

    /** @test */
    public function user_can_view_their_scheduled_meetings()
    {
        Meeting::factory()->count(3)->create([
            'tenant_id' => $this->tenant->id,
            'host_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/me/my-meetings');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function user_can_view_upcoming_meetings()
    {
        Meeting::factory()->count(2)->create([
            'tenant_id' => $this->tenant->id,
            'host_id' => $this->user->id,
            'start_at' => now()->addDays(2),
            'end_at' => now()->addDays(2)->addHours(2),
            'status' => 'scheduled',
        ]);

        Meeting::factory()->count(2)->create([
            'tenant_id' => $this->tenant->id,
            'host_id' => $this->user->id,
            'start_at' => now()->subDays(2),
            'end_at' => now()->subDays(2)->addHours(2),
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/me/my-meetings?filter=upcoming');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    /** @test */
    public function user_can_create_meeting()
    {
        $room = MeetingRoom::factory()->create(['tenant_id' => $this->tenant->id]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/tenants/{$this->tenant->slug}/meetings", [
                'title' => 'My Meeting',
                'meeting_room_id' => $room->id,
                'start_at' => now()->addDay()->setHour(10)->format('Y-m-d H:i:s'),
                'end_at' => now()->addDay()->setHour(11)->format('Y-m-d H:i:s'),
                'description' => 'Discussion about project',
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('meetings', [
            'title' => 'My Meeting',
            'host_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function user_can_view_visitors_for_their_meetings()
    {
        $meeting = Meeting::factory()->create([
            'tenant_id' => $this->tenant->id,
            'host_id' => $this->user->id,
        ]);

        $visitor = Visitor::factory()->create(['tenant_id' => $this->tenant->id]);
        VisitorVisit::factory()->create([
            'tenant_id' => $this->tenant->id,
            'visitor_id' => $visitor->id,
            'host_id' => $this->user->id,
            'meeting_id' => $meeting->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/me/my-visitors');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function user_can_update_own_profile()
    {
        $response = $this->actingAs($this->user)
            ->putJson('/api/me/profile', [
                'name' => 'Updated Name',
                'phone' => '+1234567890',
                'department' => 'Engineering',
                'job_title' => 'Senior Developer',
            ]);

        $response->assertStatus(200);
        $this->assertEquals('Updated Name', $this->user->fresh()->name);
    }

    /** @test */
    public function user_can_update_notification_preferences()
    {
        $response = $this->actingAs($this->user)
            ->putJson('/api/me/preferences/notifications', [
                'email_notifications' => true,
                'sms_notifications' => false,
                'meeting_reminders' => true,
                'visitor_check_in_alerts' => true,
            ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function user_can_view_own_analytics()
    {
        Meeting::factory()->count(5)->create([
            'tenant_id' => $this->tenant->id,
            'host_id' => $this->user->id,
        ]);

        VisitorVisit::factory()->count(10)->create([
            'tenant_id' => $this->tenant->id,
            'host_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/me/analytics');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'total_meetings',
                    'total_visitors',
                    'meetings_this_month',
                    'visitors_this_month',
                ],
            ]);
    }

    /** @test */
    public function user_cannot_view_other_users_meetings()
    {
        $otherUser = User::factory()->create();
        $this->tenant->users()->attach($otherUser->id);

        Meeting::factory()->create([
            'tenant_id' => $this->tenant->id,
            'host_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/me/my-meetings');

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }

    /** @test */
    public function user_cannot_delete_meeting_they_dont_own()
    {
        $otherUser = User::factory()->create();
        $this->tenant->users()->attach($otherUser->id);

        $meeting = Meeting::factory()->create([
            'tenant_id' => $this->tenant->id,
            'host_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/tenants/{$this->tenant->slug}/meetings/{$meeting->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function user_cannot_access_admin_functions()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/admin/tenants');

        $response->assertStatus(403);
    }

    /** @test */
    public function user_cannot_blacklist_visitors()
    {
        $visitor = Visitor::factory()->create(['tenant_id' => $this->tenant->id]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/tenants/{$this->tenant->slug}/visitors/{$visitor->id}/blacklist", [
                'reason' => 'Test',
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function user_permissions_are_correct()
    {
        $this->assertTrue($this->user->can('view visitors'));
        $this->assertTrue($this->user->can('create visitors'));
        $this->assertTrue($this->user->can('update visitors'));
        $this->assertTrue($this->user->can('check-in visitors'));
        $this->assertTrue($this->user->can('view meetings'));
        $this->assertTrue($this->user->can('create meetings'));
        $this->assertTrue($this->user->can('use kiosk'));

        $this->assertFalse($this->user->can('blacklist visitors'));
        $this->assertFalse($this->user->can('delete visitors'));
        $this->assertFalse($this->user->can('manage kiosks'));
        $this->assertFalse($this->user->can('view reports'));
        $this->assertFalse($this->user->can('manage users'));
        $this->assertFalse($this->user->can('view tenants'));
    }
}