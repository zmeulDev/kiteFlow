<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Tenant;
use Illuminate\Support\Str;

class SubTenantManager extends Component
{
    public $subtenants;
    public $name;
    public $slug;
    public $editingId = null;

    public function mount()
    {
        $this->loadSubtenants();
    }

    public function loadSubtenants()
    {
        $this->subtenants = auth()->user()->tenant->children()->get();
    }

    public function updatedName($value)
    {
        if (!$this->editingId) {
            $this->slug = Str::slug($value);
        }
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|min:3',
            'slug' => 'required|string|unique:tenants,slug,' . $this->editingId,
        ]);

        if ($this->editingId) {
            Tenant::find($this->editingId)->update([
                'name' => $this->name,
                'slug' => $this->slug,
            ]);
        } else {
            auth()->user()->tenant->children()->create([
                'name' => $this->name,
                'slug' => $this->slug,
                'plan' => 'free',
                'status' => 'active',
            ]);
        }

        $this->reset(['name', 'slug', 'editingId']);
        $this->loadSubtenants();
        $this->dispatch('notify', type: 'success', message: 'Sub-tenant saved successfully.');
    }

    public function edit($id)
    {
        $sub = Tenant::findOrFail($id);
        $this->editingId = $sub->id;
        $this->name = $sub->name;
        $this->slug = $sub->slug;
    }

    public function delete($id)
    {
        try {
            $sub = Tenant::findOrFail($id);
            $name = $sub->name;
            $sub->delete(); // Triggers cascading delete in Model boot
            
            $this->loadSubtenants();
            $this->dispatch('notify', type: 'success', message: "✨ '{$name}' has been successfully removed from your Hub.");
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: "Could not delete sub-tenant. Please ensure all active bookings are cleared first.");
        }
    }

    public function render()
    {
        return view('livewire.dashboard.sub-tenant-manager');
    }
}
