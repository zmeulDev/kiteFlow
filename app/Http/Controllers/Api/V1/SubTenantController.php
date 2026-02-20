<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SubTenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubTenantController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $subTenants = SubTenant::query()
            ->with('tenant')
            ->when($request->tenant_id, fn($q, $id) => $q->where('tenant_id', $id))
            ->when($request->search, fn($q, $search) => 
                $q->where('name', 'like', "%{$search}%")
            )
            ->orderBy('name')
            ->paginate($request->per_page ?? 20);

        return response()->json($subTenants);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['slug'] = \Str::slug($validated['name']);
        
        // Ensure unique slug within tenant
        $baseSlug = $validated['slug'];
        $counter = 1;
        while (SubTenant::where('tenant_id', $validated['tenant_id'])->where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $subTenant = SubTenant::create($validated);

        return response()->json([
            'message' => 'Sub-tenant created successfully',
            'sub_tenant' => $subTenant,
        ], 201);
    }

    public function show(SubTenant $subTenant): JsonResponse
    {
        return response()->json($subTenant->load(['tenant', 'users', 'visits']));
    }

    public function update(Request $request, SubTenant $subTenant): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean',
        ]);

        if (isset($validated['name']) && $validated['name'] !== $subTenant->name) {
            $validated['slug'] = \Str::slug($validated['name']);
        }

        $subTenant->update($validated);

        return response()->json([
            'message' => 'Sub-tenant updated successfully',
            'sub_tenant' => $subTenant->fresh(),
        ]);
    }

    public function destroy(SubTenant $subTenant): JsonResponse
    {
        $subTenant->delete();

        return response()->json([
            'message' => 'Sub-tenant deleted successfully',
        ]);
    }
}
