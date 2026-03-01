<?php

namespace App\Services;

use App\Models\Entrance;
use App\Models\Visitor;
use App\Models\Visit;
use Illuminate\Support\Facades\DB;

class VisitService
{
    public function __construct(
        protected QrCodeService $qrCodeService
    ) {}

    public function createVisit(array $visitorData, array $visitData, Entrance $entrance): Visit
    {
        return DB::transaction(function () use ($visitorData, $visitData, $entrance) {
            $visitor = Visitor::firstOrCreate(
                [
                    'email' => $visitorData['email'] ?? null,
                    'first_name' => $visitorData['first_name'],
                    'last_name' => $visitorData['last_name'],
                ],
                array_merge($visitorData, [
                    'company_id' => $visitData['company_id'] ?? null,
                ])
            );

            $visit = Visit::create([
                'visitor_id' => $visitor->id,
                'entrance_id' => $entrance->id,
                'space_id' => $visitData['space_id'] ?? null,
                'host_id' => $visitData['host_id'] ?? null,
                'host_name' => $visitData['host_name'] ?? null,
                'host_email' => $visitData['host_email'] ?? null,
                'purpose' => $visitData['purpose'] ?? null,
                'status' => 'pending',
                'qr_code' => $this->qrCodeService->generateQrCode(),
                'check_in_code' => strtoupper(\Illuminate\Support\Str::random(6)),
                'scheduled_at' => $visitData['scheduled_at'] ?? null,
            ]);

            return $visit;
        });
    }

    public function checkIn(Visit $visit, array $consentData = []): Visit
    {
        $visit->update([
            'status' => 'checked_in',
            'check_in_at' => now(),
            'gdpr_consent_at' => isset($consentData['gdpr']) ? now() : null,
            'nda_consent_at' => isset($consentData['nda']) ? now() : null,
            'signature' => $consentData['signature'] ?? null,
            'photo_path' => $consentData['photo_path'] ?? null,
        ]);

        return $visit;
    }

    public function checkOut(Visit $visit): Visit
    {
        $visit->update([
            'status' => 'checked_out',
            'check_out_at' => now(),
        ]);

        return $visit;
    }

    public function findByQrCode(string $qrCode): ?Visit
    {
        return Visit::where('qr_code', $qrCode)->first();
    }

    public function findByCheckInCode(string $checkInCode): ?Visit
    {
        return Visit::where('check_in_code', strtoupper($checkInCode))
            ->with(['visitor', 'host', 'entrance.building', 'space'])
            ->first();
    }

    public function getActiveVisits(?Entrance $entrance = null)
    {
        $query = Visit::with(['visitor', 'entrance.building'])
            ->where('status', 'checked_in')
            ->orderBy('check_in_at', 'desc');

        if ($entrance) {
            $query->where('entrance_id', $entrance->id);
        }

        return $query->get();
    }

    public function getTodaysVisits(?Entrance $entrance = null)
    {
        $query = Visit::with(['visitor', 'entrance.building'])
            ->whereDate('check_in_at', today())
            ->orderBy('check_in_at', 'desc');

        if ($entrance) {
            $query->where('entrance_id', $entrance->id);
        }

        return $query->get();
    }
}