<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TenantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'slug' => $this->slug,
            'domain' => $this->domain,
            'email' => $this->email,
            'phone' => $this->phone,
            'logo' => $this->logo ? asset('storage/' . $this->logo) : null,
            'timezone' => $this->timezone,
            'locale' => $this->locale,
            'currency' => $this->currency,
            'status' => $this->status,
            'trial_ends_at' => $this->trial_ends_at?->toISOString(),
            'subscription_ends_at' => $this->subscription_ends_at?->toISOString(),
            // Contract details
            'subscription_plan' => $this->subscription_plan,
            'billing_cycle' => $this->billing_cycle,
            'monthly_price' => $this->monthly_price,
            'yearly_price' => $this->yearly_price,
            'contract_start_date' => $this->contract_start_date?->toDateString(),
            'contract_end_date' => $this->contract_end_date?->toDateString(),
            'payment_status' => $this->payment_status,
            'notes' => $this->notes,
            'settings' => $this->whenLoaded('settings', fn () => $this->settings->pluck('value', 'key')),
            'address' => $this->address,
            'parent' => new TenantResource($this->whenLoaded('parent')),
            'children' => TenantResource::collection($this->whenLoaded('children')),
            'users_count' => $this->whenCounted('users'),
            'visitors_count' => $this->whenCounted('visitors'),
            'meetings_count' => $this->whenCounted('meetings'),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}