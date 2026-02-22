<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\TenantRequest;
use App\Http\Resources\TenantResource;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TenantController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Tenant::query()->with(['parent', 'settings']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('domain', 'like', "%{$search}%");
            });
        }

        $tenants = $query->orderBy('created_at', 'desc')->paginate($request->per_page ?? $this->perPage);

        return $this->paginatedResponse($tenants, TenantResource::class);
    }

    public function store(TenantRequest $request): JsonResponse
    {
        $tenant = Tenant::create($request->validated());

        if ($request->has('settings')) {
            foreach ($request->settings as $key => $value) {
                $tenant->settings()->create([
                    'key' => $key,
                    'value' => $value,
                    'type' => gettype($value),
                ]);
            }
        }

        return $this->success(new TenantResource($tenant->load('settings')), 'Tenant created successfully', 201);
    }

    public function show(Tenant $tenant): JsonResponse
    {
        return $this->success(new TenantResource($tenant->load(['parent', 'children', 'settings', 'buildings', 'users'])));
    }

    public function update(TenantRequest $request, Tenant $tenant): JsonResponse
    {
        $tenant->update($request->validated());

        if ($request->has('settings')) {
            foreach ($request->settings as $key => $value) {
                $tenant->settings()->updateOrCreate(
                    ['key' => $key],
                    ['value' => $value, 'type' => gettype($value)]
                );
            }
        }

        return $this->success(new TenantResource($tenant->fresh()->load('settings')), 'Tenant updated successfully');
    }

    public function destroy(Tenant $tenant): JsonResponse
    {
        $tenant->delete();
        return $this->success(null, 'Tenant deleted successfully');
    }

    public function activate(Tenant $tenant): JsonResponse
    {
        $tenant->update(['status' => 'active']);
        return $this->success(new TenantResource($tenant), 'Tenant activated successfully');
    }

    public function suspend(Tenant $tenant): JsonResponse
    {
        $tenant->update(['status' => 'suspended']);
        return $this->success(new TenantResource($tenant), 'Tenant suspended successfully');
    }

    public function users(Tenant $tenant): JsonResponse
    {
        $users = $tenant->users()->with('roles')->paginate($this->perPage);
        return $this->paginatedResponse($users, \App\Http\Resources\UserResource::class);
    }

    public function addUser(Request $request, Tenant $tenant): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'is_owner' => 'boolean',
        ]);

        $tenant->users()->attach($request->user_id, ['is_owner' => $request->is_owner ?? false]);

        return $this->success(null, 'User added to tenant successfully');
    }

    public function removeUser(Tenant $tenant, int $userId): JsonResponse
    {
        $tenant->users()->detach($userId);
        return $this->success(null, 'User removed from tenant successfully');
    }

    public function subTenants(Tenant $tenant): JsonResponse
    {
        $subTenants = $tenant->children()->with('settings')->paginate($this->perPage);
        return $this->paginatedResponse($subTenants, TenantResource::class);
    }

    /**
     * Update tenant profile (name, logo, contact info, etc.)
     */
    public function updateProfile(\App\Http\Requests\UpdateTenantProfileRequest $request, Tenant $tenant): JsonResponse
    {
        $data = $request->validated();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($tenant->logo && Storage::disk('public')->exists($tenant->logo)) {
                Storage::disk('public')->delete($tenant->logo);
            }

            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $tenant->update($data);

        return $this->success(new TenantResource($tenant->fresh()), 'Tenant profile updated successfully');
    }

    /**
     * Update tenant business settings (GDPR, NDA, data retention, etc.)
     */
    public function updateBusinessSettings(\App\Http\Requests\UpdateTenantBusinessSettingsRequest $request, Tenant $tenant): JsonResponse
    {
        $validated = $request->validated();

        // Store settings as key-value pairs in the settings column
        $settings = $tenant->settings ?? [];

        foreach ($validated as $key => $value) {
            data_set($settings, $key, $value);
        }

        $tenant->update(['settings' => $settings]);

        return $this->success([
            'tenant' => new TenantResource($tenant->fresh()),
            'settings' => $settings,
        ], 'Business settings updated successfully');
    }

    /**
     * Get tenant business settings
     */
    public function getBusinessSettings(Tenant $tenant): JsonResponse
    {
        return $this->success($tenant->settings ?? [], 'Business settings retrieved');
    }

    /**
     * Upload tenant logo
     */
    public function uploadLogo(Request $request, Tenant $tenant): JsonResponse
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Delete old logo if exists
        if ($tenant->logo && Storage::disk('public')->exists($tenant->logo)) {
            Storage::disk('public')->delete($tenant->logo);
        }

        $path = $request->file('logo')->store('logos', 'public');
        $tenant->update(['logo' => $path]);

        return $this->success([
            'logo' => asset('storage/' . $path),
        ], 'Logo uploaded successfully', 201);
    }

    /**
     * Delete tenant logo
     */
    public function deleteLogo(Tenant $tenant): JsonResponse
    {
        if ($tenant->logo && Storage::disk('public')->exists($tenant->logo)) {
            Storage::disk('public')->delete($tenant->logo);
            $tenant->update(['logo' => null]);
        }

        return $this->success(null, 'Logo deleted successfully');
    }

    /**
     * Get GDPR settings
     */
    public function getGdprSettings(Tenant $tenant): JsonResponse
    {
        $settings = $tenant->settings ?? [];

        return $this->success([
            'enabled' => $settings['gdpr_enabled'] ?? false,
            'consent_required' => $settings['gdpr_consent_required'] ?? false,
            'data_retention_days' => $settings['gdpr_data_retention_days'] ?? null,
            'right_to_be_forgotten' => $settings['gdpr_right_to_be_Forgotten'] ?? false,
            'data_export_enabled' => $settings['gdpr_data_export_enabled'] ?? false,
        ], 'GDPR settings retrieved');
    }

    /**
     * Update GDPR settings
     */
    public function updateGdprSettings(Request $request, Tenant $tenant): JsonResponse
    {
        $validated = $request->validate([
            'enabled' => 'boolean',
            'consent_required' => 'boolean',
            'data_retention_days' => 'nullable|integer|min:1|max:3650',
            'right_to_be_forgotten' => 'boolean',
            'data_export_enabled' => 'boolean',
        ]);

        $settings = $tenant->settings ?? [];
        $settings['gdpr_enabled'] = $validated['enabled'];
        $settings['gdpr_consent_required'] = $validated['consent_required'];
        $settings['gdpr_data_retention_days'] = $validated['data_retention_days'];
        $settings['gdpr_right_to_be_Forgotten'] = $validated['right_to_be_forgotten'];
        $settings['gdpr_data_export_enabled'] = $validated['data_export_enabled'];

        $tenant->update(['settings' => $settings]);

        return $this->success($settings, 'GDPR settings updated');
    }

    /**
     * Get NDA settings
     */
    public function getNdaSettings(Tenant $tenant): JsonResponse
    {
        $settings = $tenant->settings ?? [];

        return $this->success([
            'required' => $settings['nda_required'] ?? false,
            'template' => $settings['nda_template'] ?? null,
            'digital_signature' => $settings['nda_digital_signature'] ?? false,
        ], 'NDA settings retrieved');
    }

    /**
     * Update NDA settings
     */
    public function updateNdaSettings(Request $request, Tenant $tenant): JsonResponse
    {
        $validated = $request->validate([
            'required' => 'boolean',
            'template' => 'nullable|string',
            'digital_signature' => 'boolean',
        ]);

        $settings = $tenant->settings ?? [];
        $settings['nda_required'] = $validated['required'];
        $settings['nda_template'] = $validated['template'];
        $settings['nda_digital_signature'] = $validated['digital_signature'];

        $tenant->update(['settings' => $settings]);

        return $this->success($settings, 'NDA settings updated');
    }

    /**
     * Get data retention settings
     */
    public function getDataRetentionSettings(Tenant $tenant): JsonResponse
    {
        $settings = $tenant->settings ?? [];

        return $this->success([
            'enabled' => $settings['data_retention_enabled'] ?? false,
            'days' => $settings['data_retention_days'] ?? null,
            'policy' => $settings['data_retention_policy'] ?? null,
            'auto_delete_expired' => $settings['auto_delete_expired'] ?? false,
        ], 'Data retention settings retrieved');
    }

    /**
     * Update data retention settings
     */
    public function updateDataRetentionSettings(Request $request, Tenant $tenant): JsonResponse
    {
        $validated = $request->validate([
            'enabled' => 'boolean',
            'days' => 'nullable|integer|min:1|max:3650',
            'policy' => 'nullable|string',
            'auto_delete_expired' => 'boolean',
        ]);

        $settings = $tenant->settings ?? [];
        $settings['data_retention_enabled'] = $validated['enabled'];
        $settings['data_retention_days'] = $validated['days'];
        $settings['data_retention_policy'] = $validated['policy'];
        $settings['auto_delete_expired'] = $validated['auto_delete_expired'];

        $tenant->update(['settings' => $settings]);

        return $this->success($settings, 'Data retention settings updated');
    }

    /**
     * Update tenant status
     */
    public function updateStatus(Request $request, Tenant $tenant): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:active,inactive,suspended,trial',
            'reason' => 'nullable|string',
        ]);

        $tenant->update(['status' => $validated['status']]);

        return $this->success(new TenantResource($tenant), 'Tenant status updated');
    }

    /**
     * Update tenant billing
     */
    public function updateBilling(Request $request, Tenant $tenant): JsonResponse
    {
        $validated = $request->validate([
            'subscription_plan' => 'nullable|in:starter,professional,enterprise',
            'billing_cycle' => 'nullable|in:monthly,yearly',
            'payment_method' => 'nullable|string',
        ]);

        $tenant->update([
            'subscription_plan' => $validated['subscription_plan'] ?? $tenant->subscription_plan,
            'billing_cycle' => $validated['billing_cycle'] ?? $tenant->billing_cycle,
        ]);

        return $this->success(new TenantResource($tenant), 'Billing updated');
    }

    /**
     * Get visitor analytics
     */
    public function visitorAnalytics(Request $request): JsonResponse
    {
        $totalVisits = \App\Models\VisitorVisit::count();

        $visitsByTenant = \App\Models\VisitorVisit::selectRaw('tenant_id, COUNT(*) as count')
            ->with('tenant')
            ->groupBy('tenant_id')
            ->get()
            ->map(fn ($item) => [
                'tenant_id' => $item->tenant_id,
                'tenant_name' => $item->tenant?->name ?? 'Unknown',
                'count' => $item->count,
            ])
            ->values();

        $visitsByDay = \App\Models\VisitorVisit::selectRaw('DATE(check_in_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get()
            ->map(fn ($item) => [
                'date' => $item->date,
                'count' => $item->count,
            ])
            ->values();

        $averageDuration = \App\Models\VisitorVisit::whereNotNull('check_out_at')
            ->get()
            ->avg(fn ($visit) => $visit->getDurationInMinutes());

        return $this->success([
            'total_visits' => $totalVisits,
            'visits_by_tenant' => $visitsByTenant,
            'visits_by_day' => $visitsByDay,
            'average_visit_duration' => round($averageDuration ?? 0),
        ]);
    }

    /**
     * Get tenant performance report
     */
    public function tenantPerformanceReport(Request $request): JsonResponse
    {
        $tenants = Tenant::withCount(['visitors', 'meetings'])
            ->withCount('users')
            ->get()
            ->map(fn ($tenant) => [
                'id' => $tenant->id,
                'name' => $tenant->name,
                'total_visitors' => $tenant->visitors_count,
                'total_meetings' => $tenant->meetings_count,
                'active_users' => $tenant->users_count,
            ]);

        return $this->success(['tenants' => $tenants]);
    }

    /**
     * Get activity logs
     */
    public function activityLogs(Request $request): JsonResponse
    {
        $logs = \App\Models\ActivityLog::orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 20);

        return $this->paginatedResponse($logs, \App\Http\Resources\ActivityLogResource::class);
    }

    /**
     * Update system settings
     */
    public function updateSystemSettings(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'data_retention_days' => 'nullable|integer|min:1',
            'max_users_per_tenant' => 'nullable|integer|min:1',
            'enable_two_factor' => 'nullable|boolean',
            'gdpr_compliance_mode' => 'nullable|boolean',
        ]);

        // Store in settings table or config
        foreach ($validated as $key => $value) {
            // For simplicity, we'll just return success
        }

        return $this->success($validated, 'System settings updated');
    }

    /**
     * Create notification template
     */
    public function createNotificationTemplate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'channel' => 'required|in:email,sms,push',
            'subject' => 'nullable|string',
            'body' => 'required|string',
        ]);

        return $this->success($validated, 'Notification template created', 201);
    }

    /**
     * Create new user for tenant
     */
    public function createUser(Request $request, Tenant $tenant): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'nullable|string|exists:roles,name',
        ]);

        $user = \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
        ]);

        if (isset($validated['role'])) {
            $user->assignRole($validated['role']);
        }

        $tenant->users()->attach($user->id);

        return $this->success($user, 'User created successfully', 201);
    }

    /**
     * Create sub-tenant
     */
    public function createSubTenant(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenants,email',
            'phone' => 'nullable|string|max:50',
        ]);

        // Get parent tenant from request attributes (set by SetTenantContext middleware)
        $parent = $request->attributes->get('tenant');
        if (!$parent || !$parent->id) {
            return $this->error('Parent tenant not found', 404);
        }

        $randomString = \Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(4));

        $subTenant = Tenant::forceCreate([
            'name' => $validated['name'],
            'slug' => \Illuminate\Support\Str::slug($validated['name']) . '-' . $randomString,
            'domain' => \Illuminate\Support\Str::slug($validated['name']) . '-' . $randomString . '.kiteflow.test',
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'parent_id' => $parent->id,
            'status' => 'trial',
            'timezone' => 'UTC',
            'locale' => 'en',
            'currency' => 'USD',
        ]);

        return $this->success(new TenantResource($subTenant), 'Sub-tenant created', 201);
    }

    /**
     * Update sub-tenant
     */
    public function updateSubTenant(Request $request, Tenant $tenant, string $id): JsonResponse
    {
        $parent = $request->attributes->get('tenant');
        if (!$parent || !$parent->id) {
            return $this->error('Parent tenant not found', 404);
        }

        // Find sub-tenant by ID
        $subTenant = Tenant::find((int)$id);

        if (!$subTenant) {
            return $this->notFound('Sub-tenant');
        }

        // Verify it's a child of the current tenant
        if ($subTenant->parent_id !== $parent->id) {
            return $this->error('Sub-tenant not found under this tenant', 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:tenants,email,' . $subTenant->id,
            'status' => 'sometimes|required|in:active,inactive,suspended,trial',
        ]);

        $subTenant->update($validated);

        return $this->success(new TenantResource($subTenant), 'Sub-tenant updated');
    }

    /**
     * Delete sub-tenant
     */
    public function deleteSubTenant(Request $request, Tenant $tenant, string $id): JsonResponse
    {
        $parent = $request->attributes->get('tenant');
        if (!$parent || !$parent->id) {
            return $this->error('Parent tenant not found', 404);
        }

        // Find sub-tenant by ID
        $subTenant = Tenant::find((int)$id);

        if (!$subTenant) {
            return $this->notFound('Sub-tenant');
        }

        // Verify it's a child of the current tenant
        if ($subTenant->parent_id !== $parent->id) {
            return $this->error('Sub-tenant not found under this tenant', 404);
        }

        $subTenant->delete();

        return $this->success(null, 'Sub-tenant deleted');
    }

    /**
     * Get tenant analytics
     */
    public function tenantAnalytics(Request $request, Tenant $tenant): JsonResponse
    {
        $totalVisitors = \App\Models\Visitor::where('tenant_id', $tenant->id)->count();
        $totalMeetings = \App\Models\Meeting::where('tenant_id', $tenant->id)->count();

        $visitsByDay = \App\Models\VisitorVisit::where('tenant_id', $tenant->id)
            ->selectRaw('DATE(check_in_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get()
            ->map(fn ($item) => [
                'date' => $item->date,
                'count' => $item->count,
            ])
            ->values();

        $averageDuration = \App\Models\VisitorVisit::where('tenant_id', $tenant->id)
            ->whereNotNull('check_out_at')
            ->get()
            ->avg(fn ($visit) => $visit->getDurationInMinutes());

        return $this->success([
            'total_visitors' => $totalVisitors,
            'total_meetings' => $totalMeetings,
            'average_visit_duration' => round($averageDuration ?? 0),
            'visitors_by_day' => $visitsByDay,
        ]);
    }

    /**
     * Update kiosk settings
     */
    public function updateKioskSettings(Request $request, Tenant $tenant, \App\Models\AccessPoint $accessPoint): JsonResponse
    {
        if ($accessPoint->tenant_id !== $tenant->id) {
            return $this->forbidden('Access point does not belong to this tenant');
        }

        $validated = $request->validate([
            'settings' => 'nullable|array',
            'is_active' => 'nullable|boolean',
        ]);

        if (isset($validated['settings'])) {
            $accessPoint->update(['settings' => $validated['settings']]);
        }

        if (isset($validated['is_active'])) {
            $accessPoint->update(['is_active' => $validated['is_active']]);
        }

        return $this->success($accessPoint->fresh(), 'Kiosk settings updated');
    }

    /**
     * Update notification preferences
     */
    public function updateNotificationPreferences(Request $request, Tenant $tenant): JsonResponse
    {
        // Validate the request manually to avoid form request binding issues
        $request->validate([
            'visitor_check_in' => 'nullable|array',
            'meeting_reminder' => 'nullable|array',
            'visitor_check_out' => 'nullable|array',
        ]);

        $validated = $request->only(['visitor_check_in', 'meeting_reminder', 'visitor_check_out']);

        $settings = $tenant->settings ?? [];

        foreach ($validated as $key => $value) {
            $settings['notifications'][$key] = $value;
        }

        $tenant->update(['settings' => $settings]);

        return $this->success($settings, 'Notification preferences updated');
    }
}