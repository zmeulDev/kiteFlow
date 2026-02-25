<?php

namespace App\Services;

use App\Mail\HostNotificationMail;
use App\Mail\VisitInvitationMail;
use App\Models\Company;
use App\Models\Entrance;
use App\Models\User;
use App\Models\Visitor;
use App\Models\Visit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class VisitSchedulingService
{
    public function __construct(
        protected QrCodeService $qrCodeService
    ) {}

    public function scheduleVisit(
        array $visitorData,
        array $visitData,
        Entrance $entrance,
        ?User $host = null,
        ?Company $visitorCompany = null
    ): Visit {
        return DB::transaction(function () use ($visitorData, $visitData, $entrance, $host, $visitorCompany) {
            // Create or find visitor
            $visitor = Visitor::firstOrCreate(
                ['email' => $visitorData['email']],
                array_merge($visitorData, [
                    'company_id' => $visitorCompany?->id,
                ])
            );

            // Generate unique 6-digit check-in code
            $checkInCode = $this->generateUniqueCheckInCode();

            // Create the scheduled visit
            $visit = Visit::create([
                'visitor_id' => $visitor->id,
                'entrance_id' => $entrance->id,
                'host_id' => $host?->id,
                'host_name' => $host?->name ?? $visitData['host_name'] ?? null,
                'host_email' => $host?->email ?? $visitData['host_email'] ?? null,
                'purpose' => $visitData['purpose'] ?? null,
                'status' => 'pending',
                'qr_code' => $this->qrCodeService->generateQrCode(),
                'check_in_code' => $checkInCode,
                'scheduled_at' => $visitData['scheduled_at'] ?? null,
            ]);

            // Load relationships for emails
            $visit->load(['visitor.company', 'entrance.building', 'host']);

            // Send invitation email to visitor
            if ($visitor->email) {
                Mail::to($visitor->email)
                    ->queue(new VisitInvitationMail($visit));
            }

            // Send notification to host if exists
            if ($host && $host->email) {
                Mail::to($host->email)
                    ->queue(new HostNotificationMail($visit));
            }

            return $visit;
        });
    }

    protected function generateUniqueCheckInCode(): string
    {
        do {
            $code = strtoupper(Str::random(6));
            // Ensure only alphanumeric characters
            $code = preg_replace('/[^A-Z0-9]/', '', $code);
            // Pad if needed
            $code = str_pad($code, 6, '0', STR_PAD_RIGHT);
        } while (Visit::where('check_in_code', $code)->exists());

        return $code;
    }

    public function findByCheckInCode(string $code): ?Visit
    {
        return Visit::with(['visitor', 'entrance.building', 'host'])
            ->where('check_in_code', strtoupper($code))
            ->first();
    }
}