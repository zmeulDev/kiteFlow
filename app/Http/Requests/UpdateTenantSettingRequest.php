<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTenantSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $tenant = $this->route('tenant');
        // Handle both Tenant object and string (slug) cases
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
            'key' => 'required|string|max:255',
            'value' => 'required',
            'type' => 'in:string,integer,float,boolean,array,json',
        ];
    }
}
