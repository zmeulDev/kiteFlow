<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TenantController extends Controller
{
    /**
     * List all tenants (Super Admin only)
     */
    public function index(Request $request): JsonResponse
    {
        $tenants = Tenant::query()
            ->when($request->search, fn($q, $search) => 
                $q->where('name', 'like', "%{$search}%")
            )
            ->when($request->has('active'), fn($q) => 
                $q->where('is_active', $request->boolean('active'))
            )
            ->orderBy('name')
            ->paginate($request->per_page ?? 20);

        return response()->json($tenants);
    }

    /**
     * Create a new tenant
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'contact_person' => 'nullable|string|max:255',
            'gdpr_retention_months' => 'nullable|integer|min:1|max:36',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        
        // Ensure unique slug
        $baseSlug = $validated['slug'];
        $counter = 1;
        while (Tenant::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $tenant = Tenant::create($validated);

        return response()->json([
            'message' => 'Tenant created successfully',
            'tenant' => $tenant->load('subTenants'),
        ], 201);
    }

    /**
     * Show a single tenant
     */
    public function show(Tenant $tenant): JsonResponse
    {
        return response()->json($tenant->load([
            'subTenants',
            'buildings',
            'users',
        ]));
    }

    /**
     * Update a tenant
     */
    public function update(Request $request, Tenant $tenant): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'contact_person' => 'nullable|string|max:255',
            'gdpr_retention_months' => 'nullable|integer|min:1|max:36',
            'nda_text' => 'nullable|string',
            'terms_text' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        if (isset($validated['name']) && $validated['name'] !== $tenant->name) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $tenant->update($validated);

        return response()->json([
            'message' => 'Tenant updated successfully',
            'tenant' => $tenant->fresh(),
        ]);
    }

    /**
     * Delete a tenant
     */
    public function destroy(Tenant $tenant): JsonResponse
    {
        $tenant->delete();

        return response()->json([
            'message' => 'Tenant deleted successfully',
        ]);
    }

    /**
     * Upload tenant logo
     */
    public function uploadLogo(Request $request, Tenant $tenant): JsonResponse
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpg,jpeg,png,svg|max:2048',
        ]);

        $path = $request->file('logo')->store("tenants/{$tenant->id}", 'public');
        $tenant->update(['logo_path' => $path]);

        return response()->json([
            'message' => 'Logo uploaded successfully',
            'logo_path' => $path,
        ]);
    }
}
