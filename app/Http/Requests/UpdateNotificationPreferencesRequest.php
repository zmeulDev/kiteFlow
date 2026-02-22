<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNotificationPreferencesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $tenant = $this->route('tenant');
        if (is_string($tenant)) {
            $tenant = \App\Models\Tenant::where('slug', $tenant)->first();
        }
        return $tenant && auth()->user()->belongsToTenant($tenant->id);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'visitor_check_in' => 'nullable|array',
            'meeting_reminder' => 'nullable|array',
            'visitor_check_out' => 'nullable|array',
        ];
    }
}