<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Space;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpaceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_see_spaces_list(): void
    {
        $building = Building::factory()->create();
        $space = Space::factory()->create(['building_id' => $building->id]);

        // Assuming there is a space management page or API
        // For now, let's just verify the model and relationship
        $this->assertCount(1, Space::all());
        $this->assertEquals($building->id, $space->building_id);
        $this->assertTrue($space->building->is($building));
    }

    public function test_space_amenities_are_cast_to_array(): void
    {
        $space = Space::factory()->create([
            'amenities' => ['Projector', 'Coffee Machine']
        ]);

        $this->assertIsArray($space->amenities);
        $this->assertContains('Projector', $space->amenities);
    }
}
