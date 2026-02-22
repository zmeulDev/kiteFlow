<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MeetingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'purpose' => ['nullable', 'string', 'max:255'],
            'meeting_room_id' => ['nullable', 'exists:meeting_rooms,id'],
            'host_id' => ['nullable', 'exists:users,id'],
            'start_at' => ['required', 'date', 'after:now'],
            'end_at' => ['required', 'date', 'after:start_at'],
            'timezone' => ['nullable', 'string', 'max:50'],
            'is_all_day' => ['boolean'],
            'meeting_type' => ['nullable', 'in:in_person,virtual,hybrid'],
            'meeting_url' => ['nullable', 'url', 'max:500'],
            'is_recurring' => ['boolean'],
            'recurrence_rule' => ['required_if:is_recurring,true', 'array'],
            'recurrence_rule.frequency' => ['required_with:recurrence_rule', 'in:daily,weekly,monthly,yearly'],
            'recurrence_rule.interval' => ['nullable', 'integer', 'min:1'],
            'recurrence_rule.end_date' => ['nullable', 'date', 'after:start_at'],
            'recurrence_rule.count' => ['nullable', 'integer', 'min:1'],
            'visualitor_ids' => ['nullable', 'array'],
            'visitor_ids.*' => ['exists:visitors,id'],
            'custom_fields' => ['nullable', 'array'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'start_at.after' => 'The meeting start time must be in the future.',
            'end_at.after' => 'The meeting end time must be after the start time.',
            'recurrence_rule.required_if' => 'Recurrence rule is required when the meeting is recurring.',
        ];
    }
}