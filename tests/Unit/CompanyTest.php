<?php

namespace Tests\Unit;

use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_has_parent_and_children_relationships(): void
    {
        $parent = Company::factory()->create(['name' => 'Parent']);
        $child1 = Company::factory()->create(['name' => 'Child 1', 'parent_id' => $parent->id]);
        $child2 = Company::factory()->create(['name' => 'Child 2', 'parent_id' => $parent->id]);

        $this->assertTrue($child1->parent->is($parent));
        $this->assertCount(2, $parent->children);
        $this->assertTrue($parent->children->contains($child1));
        $this->assertTrue($parent->children->contains($child2));
    }

    public function test_it_returns_all_direct_children_ids(): void
    {
        $parent = Company::factory()->create();
        $child1 = Company::factory()->create(['parent_id' => $parent->id]);
        $child2 = Company::factory()->create(['parent_id' => $parent->id]);
        $grandchild = Company::factory()->create(['parent_id' => $child1->id]);

        $ids = $parent->allChildrenIds();

        $this->assertCount(2, $ids);
        $this->assertContains($child1->id, $ids);
        $this->assertContains($child2->id, $ids);
        $this->assertNotContains($grandchild->id, $ids);
    }
}
