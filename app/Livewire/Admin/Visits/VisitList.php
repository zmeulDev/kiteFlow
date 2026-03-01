<?php

namespace App\Livewire\Admin\Visits;

use App\Models\Building;
use App\Models\Company;
use App\Models\Entrance;
use App\Models\Space;
use App\Models\User;
use App\Models\Visit;
use App\Services\VisitSchedulingService;
use App\Services\VisitService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class VisitList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status_filter = '';
    public ?int $building_filter = null;
    public ?int $entrance_filter = null;
    public string $date_from = '';
    public string $date_to = '';

    public bool $showModal = false;
    public ?int $editingVisitId = null;

    // Scheduling modal properties
    public bool $showScheduleModal = false;
    public int $currentStep = 1;
    public ?string $schedule_company_id = null;
    public ?int $schedule_host_id = null;
    public ?int $schedule_entrance_id = null;
    public ?int $schedule_space_id = null;
    public string $schedule_purpose = '';
    public string $schedule_notes = '';
    public string $schedule_date = '';
    public string $schedule_time = '';
    public int $schedule_people_count = 1;
    
    public array $schedule_visitors = [];

    protected function rules(): array
    {
        $rules = [
            'schedule_company_id' => ['required', function ($attribute, $value, $fail) {
                if ($value !== 'main' && !\App\Models\Company::where('id', $value)->exists()) {
                    $fail('The selected company is invalid.');
                }
            }],
            'schedule_host_id' => 'nullable|exists:users,id',
            'schedule_entrance_id' => 'required|exists:entrances,id',
            'schedule_space_id' => 'nullable|exists:spaces,id',
            'schedule_purpose' => 'nullable|string|max:255',
            'schedule_notes' => 'nullable|string',
            'schedule_date' => 'required|date|after_or_equal:today',
            'schedule_time' => 'required',
            'schedule_people_count' => 'required|integer|min:1',
        ];

        if ($this->currentStep === 2) {
            $rules['schedule_visitors'] = 'required|array|min:' . $this->schedule_people_count;
            $rules['schedule_visitors.*.first_name'] = 'required|string|max:255';
            $rules['schedule_visitors.*.last_name'] = 'required|string|max:255';
            $rules['schedule_visitors.*.email'] = 'required|email|max:255';
            $rules['schedule_visitors.*.phone'] = 'nullable|string|max:50';
        }

        return $rules;
    }

    protected function messages(): array
    {
        return [
            'schedule_visitors.*.first_name.required' => 'First name is required.',
            'schedule_visitors.*.last_name.required' => 'Last name is required.',
            'schedule_visitors.*.email.required' => 'Email is required.',
            'schedule_visitors.*.email.email' => 'Please enter a valid email.',
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedBuildingFilter(): void
    {
        $this->entrance_filter = null;
        $this->resetPage();
    }

    public function updatedScheduleCompanyId(): void
    {
        // Reset host when company changes
        $this->schedule_host_id = null;
    }

    public function updatedScheduleEntranceId(): void
    {
        // Reset space when entrance changes
        $this->schedule_space_id = null;
    }

    public function editVisit(int $visitId): void
    {
        $this->editingVisitId = $visitId;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingVisitId = null;
    }

    public function openScheduleModal(): void
    {
        $this->resetScheduleForm();
        
        // Auto-select host and company for non-global managers
        $user = auth()->user();
        if (!$user->canManageAllTenants()) {
            $this->schedule_company_id = $user->company_id;
            
            // Viewers can only schedule for themselves
            if ($user->role === 'viewer') {
                $this->schedule_host_id = $user->id;
            }
        }
        
        $this->showScheduleModal = true;
    }

    public function closeScheduleModal(): void
    {
        $this->showScheduleModal = false;
        $this->resetScheduleForm();
    }

    public function resetScheduleForm(): void
    {
        $this->currentStep = 1;
        $this->schedule_company_id = null;
        $this->schedule_host_id = null;
        $this->schedule_entrance_id = null;
        $this->schedule_space_id = null;
        $this->schedule_purpose = '';
        $this->schedule_notes = '';
        $this->schedule_date = '';
        $this->schedule_time = '';
        $this->schedule_people_count = 1;
        $this->schedule_visitors = [];
        $this->resetErrorBag();
    }

    public function getEditingVisitProperty(): ?Visit
    {
        if (!$this->editingVisitId) {
            return null;
        }
        
        $user = auth()->user();
        $query = Visit::with(['visitor.company', 'entrance.building', 'host', 'space']);
        if (!$user->canManageAllTenants()) {
            if ($user->role === 'viewer') {
                 $query->where('host_id', $user->id);
            } else {
                 $managedCompanyIds = $user->getManagedCompanyIds();
                 $query->whereHas('host', function ($q) use ($managedCompanyIds) {
                     $q->whereIn('company_id', $managedCompanyIds);
                 });
            }
        }
        
        return $query->find($this->editingVisitId);
    }

    public function getHostUsersProperty()
    {
        if (!$this->schedule_company_id) {
            return collect();
        }
        
        $user = auth()->user();
        
        if ($this->schedule_company_id === 'main') {
            $query = User::whereNull('company_id')
                ->where('is_active', true)
                ->orderBy('name');
        } else {
            $query = User::where('company_id', $this->schedule_company_id)
                ->where('is_active', true)
                ->orderBy('name');
        }
            
        // If the user is a viewer, they should only see themselves in the host list
        if (!$user->isAdmin() && $user->role === 'viewer') {
            $query->where('id', $user->id);
        }
            
        return $query->get();
    }

    #[On('confirmCheckOut')]
    public function confirmCheckOut(int $visitId, \App\Services\VisitService $visitService): void
    {
        $this->checkOut($visitId, $visitService);
        $this->closeModal();
    }

    #[On('confirmCheckIn')]
    public function confirmCheckIn(int $visitId, \App\Services\VisitService $visitService): void
    {
        $this->checkIn($visitId, $visitService);
    }

    public function showCheckOutConfirm(int $visitId): void
    {
        $user = auth()->user();
        $query = Visit::with('visitor');
        if (!$user->canManageAllTenants()) {
            $managedCompanyIds = $user->getManagedCompanyIds();
            $query->whereHas('host', function ($q) use ($managedCompanyIds) {
                $q->whereIn('company_id', $managedCompanyIds);
            });
        }
        $visit = $query->findOrFail($visitId);
        $visitorName = $visit->visitor->full_name ?? 'Unknown';

        $this->dispatch('showConfirmModal', [
            'modalId' => 'checkout-confirm',
            'title' => 'Confirm Check Out',
            'message' => "Are you sure you want to check out {$visitorName}?",
            'confirmText' => 'Check Out',
            'cancelText' => 'Cancel',
            'confirmMethod' => 'confirmCheckOut',
            'confirmColor' => 'danger',
            'params' => ['visitId' => $visitId],
        ]);
    }

    public function showCheckInConfirm(int $visitId): void
    {
        $user = auth()->user();
        $query = Visit::with('visitor');
        if (!$user->canManageAllTenants()) {
            $managedCompanyIds = $user->getManagedCompanyIds();
            $query->whereHas('host', function ($q) use ($managedCompanyIds) {
                $q->whereIn('company_id', $managedCompanyIds);
            });
        }
        $visit = $query->findOrFail($visitId);
        $visitorName = $visit->visitor->full_name ?? 'Unknown';

        $this->dispatch('showConfirmModal', [
            'modalId' => 'checkin-confirm',
            'title' => 'Confirm Check In',
            'message' => "Are you sure you want to check in {$visitorName}?",
            'confirmText' => 'Check In',
            'cancelText' => 'Cancel',
            'confirmMethod' => 'confirmCheckIn',
            'confirmColor' => 'success',
            'params' => ['visitId' => $visitId],
        ]);
    }

    public function checkIn(int $visitId, VisitService $visitService): void
    {
        $user = auth()->user();
        $query = Visit::query();
        if (!$user->canManageAllTenants()) {
            if ($user->role === 'viewer') {
                $query->where('host_id', $user->id);
            } else {
                $managedCompanyIds = $user->getManagedCompanyIds();
                $query->whereHas('host', function ($q) use ($managedCompanyIds) {
                    $q->whereIn('company_id', $managedCompanyIds);
                });
            }
        }
        $visit = $query->findOrFail($visitId);
        $visitService->checkIn($visit);
        session()->flash('message', 'Visitor checked in successfully.');
        $this->closeModal();
    }

    public function checkOut(int $visitId, VisitService $visitService): void
    {
        $user = auth()->user();
        $query = Visit::query();
        if (!$user->canManageAllTenants()) {
            if ($user->role === 'viewer') {
                $query->where('host_id', $user->id);
            } else {
                $managedCompanyIds = $user->getManagedCompanyIds();
                $query->whereHas('host', function ($q) use ($managedCompanyIds) {
                    $q->whereIn('company_id', $managedCompanyIds);
                });
            }
        }
        $visit = $query->findOrFail($visitId);
        $visitService->checkOut($visit);
        session()->flash('message', 'Visitor checked out successfully.');
    }

    public function nextStep(): void
    {
        // Only validate step 1 fields
        $this->validate([
            'schedule_company_id' => ['required', function ($attribute, $value, $fail) {
                if ($value !== 'main' && !\App\Models\Company::where('id', $value)->exists()) {
                    $fail('The selected company is invalid.');
                }
            }],
            'schedule_entrance_id' => 'required|exists:entrances,id',
            'schedule_space_id' => 'nullable|exists:spaces,id',
            'schedule_date' => 'required|date|after_or_equal:today',
            'schedule_time' => 'required',
            'schedule_people_count' => 'required|integer|min:1',
        ]);

        $this->performStep1Validations();

        if ($this->getErrorBag()->isNotEmpty()) {
            return;
        }

        // Initialize visitors array dynamically based on count if empty or mismatched
        if (count($this->schedule_visitors) !== (int)$this->schedule_people_count) {
            $currentVisitors = $this->schedule_visitors;
            $this->schedule_visitors = [];
            for ($i = 0; $i < $this->schedule_people_count; $i++) {
                $this->schedule_visitors[] = [
                    'first_name' => $currentVisitors[$i]['first_name'] ?? '',
                    'last_name' => $currentVisitors[$i]['last_name'] ?? '',
                    'email' => $currentVisitors[$i]['email'] ?? '',
                    'phone' => $currentVisitors[$i]['phone'] ?? '',
                ];
            }
        }

        $this->currentStep = 2;
    }

    public function previousStep(): void
    {
        $this->currentStep = 1;
    }

    protected function performStep1Validations(): void
    {
        // 1. Check Company Contract
        $company = null;
        if ($this->schedule_company_id !== 'main') {
            $company = Company::find($this->schedule_company_id);
            if ($company) {
                if (!$company->is_active) {
                    $this->addError('schedule_company_id', 'The selected company is currently inactive.');
                    return;
                }

                $now = now()->startOfDay();
                if ($company->contract_start_date && $company->contract_start_date->startOfDay() > $now) {
                    $this->addError('schedule_company_id', 'The company contract has not started yet.');
                    return;
                }

                if ($company->contract_end_date && $company->contract_end_date->endOfDay() < $now) {
                    $this->addError('schedule_company_id', 'The company contract has expired.');
                    return;
                }
            }
        }

        // 2. Check Entrance and Building Status
        $entrance = Entrance::with('building')->find($this->schedule_entrance_id);
        if ($entrance) {
            if (!$entrance->is_active) {
                $this->addError('schedule_entrance_id', 'The selected entrance is currently inactive.');
                return;
            }

            if ($entrance->building && !$entrance->building->is_active) {
                $this->addError('schedule_entrance_id', 'The building for this entrance is currently inactive.');
                return;
            }
        }

        // 3. Check Space Status and Capacity
        $space = $this->schedule_space_id ? Space::find($this->schedule_space_id) : null;
        if ($space) {
            if (!$space->is_active) {
                $this->addError('schedule_space_id', 'The selected meeting room is currently inactive.');
                return;
            }

            if ($space->capacity > 0 && $this->schedule_people_count > $space->capacity) {
                $this->addError('schedule_people_count', "The selected room exceeds its capacity of {$space->capacity} people.");
                return;
            }
            
            // 4. Check Space Availability (Simple overlap check)
            try {
                $scheduledAt = \Carbon\Carbon::parse("{$this->schedule_date} {$this->schedule_time}");
                $overlap = Visit::where('space_id', $space->id)
                    ->where('status', '!=', 'cancelled')
                    ->where(function ($q) use ($scheduledAt) {
                        $q->whereBetween('scheduled_at', [
                            $scheduledAt->copy()->subMinutes(59),
                            $scheduledAt->copy()->addMinutes(59)
                        ]);
                    })->exists();

                if ($overlap) {
                     $this->addError('schedule_space_id', 'The selected room is already booked around this time.');
                     return;
                }
            } catch (\Exception $e) {}
        }
    }

    public function scheduleVisit(VisitSchedulingService $schedulingService): void
    {
        $user = auth()->user();
        
        // Enforce company ID strictly if not global manager
        if (!$user->canManageAllTenants()) {
            $managedIds = $user->getManagedCompanyIds();
            if (!in_array((int)$this->schedule_company_id, array_map('intval', $managedIds))) {
                $this->schedule_company_id = $user->company_id ? (string) $user->company_id : 'main';
            }
            
            // Viewers MUST be the host
            if ($user->role === 'viewer') {
                $this->schedule_host_id = $user->id;
            }
        }

        $this->validate();

        $this->performStep1Validations();

        if ($this->getErrorBag()->isNotEmpty()) {
            $this->currentStep = 1; // go back if someone tried to submit with invalid step 1
            return;
        }

        $entrance = Entrance::findOrFail($this->schedule_entrance_id);
        $space = $this->schedule_space_id ? Space::find($this->schedule_space_id) : null;
        $host = $this->schedule_host_id ? User::find($this->schedule_host_id) : null;

        $scheduledAt = \Carbon\Carbon::parse("{$this->schedule_date} {$this->schedule_time}");
        
        $visitData = [
            'purpose' => $this->schedule_purpose ?: null,
            'notes' => $this->schedule_notes ?: null,
            'scheduled_at' => $scheduledAt,
        ];

        $codes = [];

        try {
            // Schedule individual visit per person
            foreach ($this->schedule_visitors as $visitorInput) {
                $visitorData = [
                    'first_name' => $visitorInput['first_name'],
                    'last_name' => $visitorInput['last_name'],
                    'email' => $visitorInput['email'],
                    'phone' => $visitorInput['phone'] ?: null,
                ];

                $visit = $schedulingService->scheduleVisit(
                    $visitorData,
                    $visitData,
                    $entrance,
                    $host,
                    null, // removed visitor company
                    $space,
                    1 // Individual visit now, per requirement
                );
                
                $codes[] = $visit->check_in_code;
            }
        } catch (\Exception $e) {
            $this->addError('schedule_visitors', 'An error occurred while scheduling: ' . $e->getMessage());
            return;
        }

        $codeText = implode(', ', $codes);
        session()->flash('message', "Visit(s) scheduled successfully. Check-in codes: {$codeText}");
        $this->closeScheduleModal();
    }

    public function render()
    {
        $user = auth()->user();
        $query = Visit::with(['visitor.company', 'entrance.building', 'host', 'space']);
        
        if (!$user->canManageAllTenants()) {
            if ($user->role === 'viewer') {
                $query->where('host_id', $user->id);
            } else {
                $managedCompanyIds = $user->getManagedCompanyIds();
                $query->whereHas('host', function ($q) use ($managedCompanyIds) {
                    $q->whereIn('company_id', $managedCompanyIds);
                });
            }
        }

        $visits = $query->when($this->search, function ($q) {
                $q->where(function ($sq) {
                    $sq->whereHas('visitor', function ($q) {
                        $q->where('first_name', 'like', "%{$this->search}%")
                            ->orWhere('last_name', 'like', "%{$this->search}%")
                            ->orWhere('email', 'like', "%{$this->search}%");
                    })->orWhereHas('host', function ($q) {
                        $q->where('name', 'like', "%{$this->search}%");
                    })->orWhere('host_name', 'like', "%{$this->search}%")
                      ->orWhere('check_in_code', 'like', "%{$this->search}%");
                });
            })
            ->when($this->status_filter, fn($q) => $q->where('status', $this->status_filter))
            ->when($this->building_filter, function ($q) {
                $q->whereHas('entrance', fn($q) => $q->where('building_id', $this->building_filter));
            })
            ->when($this->entrance_filter, fn($q) => $q->where('entrance_id', $this->entrance_filter))
            ->when($this->date_from, fn($q) => $q->whereDate('check_in_at', '>=', $this->date_from))
            ->when($this->date_to, fn($q) => $q->whereDate('check_in_at', '<=', $this->date_to))
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $buildings = Building::where('is_active', true)->orderBy('name')->get();

        $entrances = collect();
        if ($this->building_filter) {
            $entrances = Entrance::where('building_id', $this->building_filter)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        }

        $editingVisit = $this->editingVisit;
        $hostUsers = $this->hostUsers;
        
        $companyUser = auth()->user();
        $companiesQuery = Company::where('is_active', true)->orderBy('name');
        if (!$companyUser->canManageAllTenants()) {
            $companiesQuery->whereIn('id', $companyUser->getManagedCompanyIds());
        }
        $companies = $companiesQuery->get();
        
        $allEntrances = Entrance::with('building')->where('is_active', true)->orderBy('name')->get();

        $availableSpaces = collect();
        if ($this->schedule_entrance_id) {
            $selectedEntrance = Entrance::find($this->schedule_entrance_id);
            if ($selectedEntrance) {
                $query = Space::where('building_id', $selectedEntrance->building_id)
                    ->where('is_active', true);

                if ($this->schedule_people_count > 0) {
                    $query->where(function ($q) {
                        $q->whereNull('capacity')->orWhere('capacity', '>=', (int)$this->schedule_people_count);
                    });
                }

                if ($this->schedule_date && $this->schedule_time) {
                    try {
                        $scheduledAt = \Carbon\Carbon::parse("{$this->schedule_date} {$this->schedule_time}");
                        $query->whereDoesntHave('visits', function ($q) use ($scheduledAt) {
                            $q->where('status', '!=', 'cancelled')
                              ->whereBetween('scheduled_at', [
                                  $scheduledAt->copy()->subMinutes(59),
                                  $scheduledAt->copy()->addMinutes(59)
                              ]);
                        });
                    } catch (\Exception $e) {
                        // Invalid date/time format, ignore filtering
                    }
                }

                $availableSpaces = $query->orderBy('name')->get();
            }
        }

        return view('livewire.admin.visits.visit-list', compact(
            'visits',
            'buildings',
            'entrances',
            'editingVisit',
            'hostUsers',
            'companies',
            'allEntrances',
            'availableSpaces'
        ));
    }
}