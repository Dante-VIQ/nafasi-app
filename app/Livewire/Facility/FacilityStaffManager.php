<?php
// app/Livewire/Facility/FacilityStaffManager.php

namespace App\Livewire\Facility;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class FacilityStaffManager extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showAddForm = false;
    
    // Add staff form
    public string $newName = '';
    public string $newEmail = '';
    public string $newPhone = '';
    public string $newRole = 'facility-staff';
    public string $newPassword = '';

    protected $rules = [
        'newName' => 'required|string|max:255',
        'newEmail' => 'required|email|unique:users,email',
        'newPhone' => 'nullable|string|max:20',
        'newRole' => 'required|string',
        'newPassword' => 'required|string|min:8',
    ];

    public function addStaff(): void
    {
        $this->validate();

        $user = User::create([
            'name' => $this->newName,
            'email' => $this->newEmail,
            'phone' => $this->newPhone,
            'primary_role' => $this->newRole,
            'facility_id' => Auth::user()->facility_id,
            'password' => bcrypt($this->newPassword),
            'is_active' => true,
        ]);

        $user->assignRole($this->newRole);

        $this->reset(['newName', 'newEmail', 'newPhone', 'newRole', 'newPassword', 'showAddForm']);
        session()->flash('message', "Staff member {$user->name} added successfully.");
    }

    public function removeStaff(int $userId): void
    {
        $user = User::findOrFail($userId);
        $user->update(['facility_id' => null, 'is_active' => false]);
        session()->flash('message', "{$user->name} removed from facility.");
    }

    public function render()
    {
        $facilityId = Auth::user()->facility_id;

        $staff = User::where('facility_id', $facilityId)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                      ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->orderBy('name')
            ->paginate(15);

        $roles = Role::whereIn('name', ['facility-admin', 'facility-staff'])->get();

        return view('livewire.facility.facility-staff-manager', [
            'staff' => $staff,
            'roles' => $roles,
        ])->layout('layouts.app');
    }
}