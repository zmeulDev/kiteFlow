<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;

class SubscriptionController extends Controller
{
    /**
     * Redirects the Tenant Admin to their Stripe Billing Portal.
     */
    public function billingPortal(Request $request, Tenant $tenant)
    {
        // In a real application, ensure the user is authorized to manage billing for this tenant.
        // E.g., $request->user()->hasRole('TenantAdmin') && $request->user()->tenant_id === $tenant->id

        if (!$tenant->stripe_id) {
            $tenant->createAsStripeCustomer();
        }

        return $tenant->redirectToBillingPortal(
            url('/admin/dashboard')
        );
    }
}
