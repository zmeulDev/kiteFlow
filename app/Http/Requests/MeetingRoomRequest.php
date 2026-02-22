<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MeetingRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        $tenant = $this->route('tenant');
        if (is_string($tenant)) {
            $tenant = \App\Models\Tenant::where('slug', $tenant)->first();
        }
        return $tenant && auth()->user()->belongsToTenant($tenant->id);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'capacity' => 'required|integer|min:1',
            'location' => 'nullable|string|max:255',
            'amenities' => 'nullable|array',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'building_id' => 'nullable|exists:buildings,id',
            'access_point_id' => 'nullable|exists:access_points,id',
        ];
    }
}