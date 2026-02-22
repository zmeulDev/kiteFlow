<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTenantBusinessSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $tenant = $this->route('tenant');
        return $tenant && auth()->user()->belongsToTenant($tenant->id);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // GDPR Settings
            'gdpr_enabled' => 'boolean',
            'gdpr_consent_required' => 'boolean',
            'gdpr_data_retention_days' => 'nullable|integer|min:1|max:3650',
            'gdpr_right_to_be Forgotten' => 'boolean',
            'gdpr_data_export_enabled' => 'boolean',

            // NDA Settings
            'nda_required' => 'boolean',
            'nda_template' => 'nullable|string',
            'nda_digital_signature' => 'boolean',

            // Data Retention Settings
            'data_retention_enabled' => 'boolean',
            'data_retention_days' => 'nullable|integer|min:1|max:3650',
            'data_retention_policy' => 'nullable|string',
            'auto_delete_expired' => 'boolean',

            // Business Rules
            'visitor_checkin_required_fields' => 'array',
            'visitor_checkin_required_fields.*' => 'in:first_name,last_name,email,company,phone,purpose',
            'badge_required' => 'boolean',
            'photo_required' => 'boolean',
            'id_verification_required' => 'boolean',

            // Meeting Settings
            'meeting_auto_reminder_enabled' => 'boolean',
            'meeting_reminder_minutes_before' => 'nullable|integer|min:5|max:1440',
            'meeting_max_duration_hours' => 'nullable|integer|min:1|max:24',
            'meeting_allow_visitor_booking' => 'boolean',

            // Parking Settings
            'parking_enabled' => 'boolean',
            'parking_auto_assign' => 'boolean',
            'parking_hourly_rate' => 'nullable|numeric|min:0|max:1000',
            'parking_daily_rate' => 'nullable|numeric|min:0|max:10000',

            // Access Settings
            'access_badge_expiry_days' => 'nullable|integer|min:1|max:3650',
            'access_kiosk_enabled' => 'boolean',
            'access_qr_code_enabled' => 'boolean',
            'access_biometric_enabled' => 'boolean',

            // Notification Settings
            'notifications_email_enabled' => 'boolean',
            'notifications_sms_enabled' => 'boolean',
            'notifications_push_enabled' => 'boolean',

            // Security Settings
            'security_blacklist_sync_enabled' => 'boolean',
            'security_two_factor_required' => 'boolean',
            'security_password_min_length' => 'nullable|integer|min:8|max:64',
            'security_session_timeout_minutes' => 'nullable|integer|min:5|max:1440',
        ];
    }
}
