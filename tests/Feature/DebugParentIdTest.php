<?php

namespace Tests\Feature;

use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DebugParentIdTest extends TestCase
{
    use RefreshDatabase;

    public function test_parent_id_can_be_saved_directly()
    {
        $parent = Tenant::create([
            'name' => 'Parent Company',
            'slug' => 'parent-company',
            'domain' => 'parent-company.kiteflow.test',
            'email' => 'parent@test.com',
        ]);

        // Direct DB insert with parent_id
        $childId = \Illuminate\Support\Facades\DB::table('tenants')->insertGetId([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'name' => 'Child Company',
            'slug' => 'child-company-1',
            'domain' => 'child-company-1.kiteflow.test',
            'email' => 'child1@test.com',
            'parent_id' => $parent->id,
            'status' => 'trial',
            'timezone' => 'UTC',
            'locale' => 'en',
            'currency' => 'USD',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $child = \Illuminate\Support\Facades\DB::table('tenants')->where('id', $childId)->first();
        $this->assertEquals($parent->id, $child->parent_id, 'parent_id not saved via DB insert');
    }

    public function test_parent_id_can_be_saved_via_model()
    {
        $parent = Tenant::create([
            'name' => 'Parent Company',
            'slug' => 'parent-company-2',
            'domain' => 'parent-company-2.kiteflow.test',
            'email' => 'parent2@test.com',
        ]);

        // Save then update parent_id
        $child = new Tenant([
            'name' => 'Child Company',
            'slug' => 'child-company-2',
            'domain' => 'child-company-2.kiteflow.test',
            'email' => 'child2@test.com',
            'status' => 'trial',
        ]);

        $child->save();
        $child->parent_id = $parent->id;
        $child->save();

        $child->fresh();
        $this->assertEquals($parent->id, $child->parent_id, 'parent_id not saved via model update');
    }

    public function test_parent_id_with_force_create()
    {
        $parent = Tenant::create([
            'name' => 'Parent Company',
            'slug' => 'parent-company-3',
            'domain' => 'parent-company-3.kiteflow.test',
            'email' => 'parent3@test.com',
        ]);

        $child = Tenant::forceCreate([
            'name' => 'Child Company',
            'slug' => 'child-company-3',
            'domain' => 'child-company-3.kiteflow.test',
            'email' => 'child3@test.com',
            'parent_id' => $parent->id,
            'status' => 'trial',
        ]);

        $child->fresh();
        $this->assertEquals($parent->id, $child->parent_id, 'parent_id not saved via forceCreate');
    }

    public function test_check_table_columns()
    {
        $columns = \Illuminate\Support\Facades\Schema::getColumnListing('tenants');
        $this->assertContains('parent_id', $columns, 'parent_id column does not exist');
    }
}