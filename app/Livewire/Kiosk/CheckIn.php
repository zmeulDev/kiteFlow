<?php

namespace App\Livewire\Kiosk;

use Livewire\Component;
use App\Models\Visit;
use App\Models\Visitor;
use App\Models\Tenant;
use App\Jobs\SendHostArrivalNotification;
use App\Jobs\SendPostCheckInEmail;

class CheckIn extends Component
{
    public ?Tenant $tenant = null;
    public string $mode = 'code'; // code, qr, manual
    public string $step = 'search'; // search, details, sign, success
    
    // Search inputs
    public string $visitCode = '';
    public string $email = '';
    public string $phone = '';
    
    // Found data
    public ?Visitor $visitor = null;
    public ?Visit $visit = null;
    public bool $isReturningVisitor = false;
    
    // Signature
    public string $signatureData = '';
    public bool $agreedToNda = false;
    public bool $agreedToTerms = false;
    
    // UI state
    public string $error = '';
    public string $successMessage = '';

    public function mount(?int $tenantId = null)
    {
        if ($tenantId) {
            $this->tenant = Tenant::find($tenantId);
        } else {
            $this->tenant = Tenant::where('is_active', true)->first();
        }
    }

    public function render()
    {
        return view('livewire.kiosk.check-in');
    }

    // ========== SEARCH METHODS ==========

    public function searchByCode()
    {
        $this->clearMessages();
        
        if (empty($this->visitCode)) {
            $this->error = 'Please enter a visit code';
            return;
        }

        $this->findVisitByCode(strtoupper($this->visitCode));
    }

    public function searchByQr(string $qrData)
    {
        $this->clearMessages();
        
        try {
            $data = json_decode($qrData, true);
            $code = $data['code'] ?? $qrData;
            $this->findVisitByCode(strtoupper($code));
        } catch (\Exception $e) {
            $this->error = 'Invalid QR code';
        }
    }

    public function searchByContact()
    {
        $this->clearMessages();

        if (empty($this->email) && empty($this->phone)) {
            $this->error = 'Please enter email or phone number';
            return;
        }

        // Find upcoming visit by contact info
        $this->visit = Visit::whereHas('visitor', function ($q) {
            $q->where(function ($query) {
                if ($this->email) {
                    $query->orWhere('email', $this->email);
                }
                if ($this->phone) {
                    $query->orWhere('phone', $this->phone);
                }
            });
        })
        ->where('status', 'pre_registered')
        ->where('scheduled_start', '<=', now()->addHour())
        ->where('scheduled_end', '>=', now())
        ->with(['visitor', 'tenant', 'hostUser', 'meetingRoom', 'building'])
        ->first();

        if (!$this->visit) {
            $this->error = 'No active visit found. Please check in using your visit code.';
            return;
        }

        $this->prepareForCheckIn();
    }

    protected function findVisitByCode(string $code)
    {
        $this->visit = Visit::where('visit_code', $code)
            ->with(['visitor', 'tenant', 'hostUser', 'meetingRoom', 'building'])
            ->first();

        if (!$this->visit) {
            $this->error = 'No active visit found with this code';
            return;
        }

        if ($this->visit->status !== 'pre_registered') {
            $this->error = "Visit already {$this->visit->status}. Please contact reception.";
            return;
        }

        // Check if visit time is valid (within 1 hour before/after)
        $now = now();
        $windowStart = $this->visit->scheduled_start->subHour();
        $windowEnd = $this->visit->scheduled_end->addHour();

        if ($now < $windowStart || $now > $windowEnd) {
            $this->error = 'This visit is not within the check-in window';
            return;
        }

        $this->prepareForCheckIn();
    }

    protected function prepareForCheckIn()
    {
        $this->visitor = $this->visit->visitor;
        $this->isReturningVisitor = $this->visitor->last_visit_at !== null;
        
        // If returning visitor with existing signature, skip to details
        if ($this->isReturningVisitor && $this->visitor->signature_path) {
            $this->step = 'details';
        } else {
            $this->step = 'sign';
            $this->dispatch('signature-requested');
        }
    }

    // ========== CHECK-IN METHODS ==========

    public function saveSignature()
    {
        if (empty($this->signatureData)) {
            $this->error = 'Please provide your signature';
            return;
        }

        // Save signature to storage
        $folder = storage_path('app/signatures');
        if (!is_dir($folder)) {
            mkdir($folder, 0755, true);
        }

        $filename = 'sig_' . $this->visit->visit_code . '_' . time() . '.png';
        $path = $folder . '/' . $filename;
        
        // Decode base64 data URL and save
        $imageData = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $this->signatureData));
        file_put_contents($path, $imageData);

        // Update visitor with signature
        $this->visitor->update([
            'signature_path' => 'signatures/' . $filename,
            'signature_signed_at' => now(),
            'agreed_to_nda' => $this->agreedToNda,
            'agreed_to_terms' => $this->agreedToTerms,
        ]);

        $this->step = 'details';
        $this->clearMessages();
    }

    public function performCheckIn()
    {
        if (!$this->visit || !$this->visitor) {
            $this->error = 'No visit found';
            return;
        }

        // Update visit status
        $this->visit->update([
            'status' => 'checked_in',
            'checked_in_at' => now(),
        ]);

        // Create check-in record
        \App\Models\CheckIn::create([
            'visit_id' => $this->visit->id,
            'visitor_id' => $this->visitor->id,
            'meeting_room_id' => $this->visit->meeting_room_id,
            'checked_in_by' => 1, // Reception system user
            'check_in_time' => now(),
            'check_in_method' => $this->mode,
        ]);

        // Update visitor last visit
        $this->visitor->update(['last_visit_at' => now()]);

        // Dispatch notifications
        SendHostArrivalNotification::dispatch($this->visit);
        
        // Post check-in email (delayed slightly)
        SendPostCheckInEmail::dispatch($this->visit)->delay(now()->addSeconds(30));

        $this->step = 'success';
    }

    // ========== UTILITY METHODS ==========

    public function resetForm()
    {
        $this->visitCode = '';
        $this->email = '';
        $this->phone = '';
        $this->signatureData = '';
        $this->agreedToNda = false;
        $this->agreedToTerms = false;
        $this->visitor = null;
        $this->visit = null;
        $this->step = 'search';
        $this->error = '';
        $this->successMessage = '';
        $this->isReturningVisitor = false;
    }

    protected function clearMessages()
    {
        $this->error = '';
        $this->successMessage = '';
    }
}
