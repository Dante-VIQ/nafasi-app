<?php

namespace App\Livewire\Tenant;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;   // ← correct import

class TenantUserManager extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterRole = '';
    public bool $showCreateForm = false;
    
    public string $newName = '';
    public string $newEmail = '';
    public string $newPhone = '';
    public string $newRole = 'facility-staff';
    public string $newPassword = '';
    
    public ?int $editingUserId = null;
    public string $editName = '';
    public string $editPhone = '';
    public string $editRole = '';

    protected $rules = [
        'newName' => 'required|string|max:255',
        'newEmail' => 'required|email|unique:mysql.users,email',
        'newPhone' => 'nullable|string|max:20',
        'newRole' => 'required|string',
        'newPassword' => 'required|string|min:8',
    ];

    public function ensureTenant(): void
    {
        if (tenancy()->initialized) {
            return;
        }

        $host = request()->getHost();

        // This component is tenant-only. Never attempt resolution on the
        // central domain — CentralCommunityAlertFeed is what renders there.
        if (in_array($host, config('tenancy.central_domains', []), true)) {
            return;
        }

        $tenant = Tenant::whereHas('domains', fn ($q) => $q->where('domain', $host))->first();

        if ($tenant) {
            tenancy()->initialize($tenant);
        }
    }

public function mount(): void
{
    $user = Auth::user();
    if ($user && $user->tenant_id) {
        $database = 'nafasi_tenant_' . $user->tenant_id;
        config(["database.connections.tenant.database" => $database]);
        DB::purge('tenant');
        DB::reconnect('tenant');
    }

    // $this->loadStats();
    // $this->loadRecentActivity();
}

    /**
     * Get the tenant ID of the authenticated user.
     */
    protected function currentTenantId(): ?string
    {
        return auth()->user()?->tenant_id;
    }

    public function createUser(): void
    {
         $this->ensureTenant();
        $this->validate();
        
        $user = (new User())->setConnection('mysql')->create([
            'name'          => $this->newName,
            'email'         => $this->newEmail,
            'phone'         => $this->newPhone,
            'primary_role'  => $this->newRole,
            'password'      => bcrypt($this->newPassword),
            'is_active'     => true,
            'tenant_id'     => $this->currentTenantId(),
        ]);
        
        $user->assignRole($this->newRole);
        
        $this->reset(['newName', 'newEmail', 'newPhone', 'newRole', 'newPassword', 'showCreateForm']);
        session()->flash('message', "User {$user->name} created successfully.");
    }

    public function startEdit(int $userId): void
    {
        $user = User::on('mysql')->where('tenant_id', $this->currentTenantId())
            ->findOrFail($userId);
        $this->editingUserId = $userId;
        $this->editName = $user->name;
        $this->editPhone = $user->phone ?? '';
        $this->editRole = $user->primary_role;
    }

    public function saveEdit(): void
    {
        $user = User::on('mysql')->where('tenant_id', $this->currentTenantId())
            ->findOrFail($this->editingUserId);
        $user->update([
            'name'          => $this->editName,
            'phone'         => $this->editPhone,
            'primary_role'  => $this->editRole,
        ]);
        $user->syncRoles([$this->editRole]);
        
        $this->reset(['editingUserId', 'editName', 'editPhone', 'editRole']);
        session()->flash('message', 'User updated.');
    }

    public function cancelEdit(): void
    {
        $this->reset(['editingUserId', 'editName', 'editPhone', 'editRole']);
    }

    public function toggleActive(int $userId): void
    {
        $user = User::on('mysql')->where('tenant_id', $this->currentTenantId())
            ->findOrFail($userId);
        $user->update(['is_active' => !$user->is_active]);
        session()->flash('message', $user->name . ' ' . ($user->is_active ? 'activated' : 'deactivated') . '.');
    }

    public function render()
    {
        $this->ensureTenant();
        $tenantId = $this->currentTenantId();

        $users = User::query()
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                      ->orWhere('email', 'like', "%{$this->search}%")
                      ->orWhere('phone', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterRole, function ($query) {
                $query->where('primary_role', $this->filterRole);
            })
            ->orderBy('name')
            ->paginate(20);

$roles = \App\Models\Role::on('mysql')->whereIn('name', [
    'tenant-admin', 'facility-admin', 'facility-staff', 'coordinator', 'public-user',
])->get();

        return view('livewire.tenant.tenant-user-manager', [
            'users' => $users,
            'roles' => $roles,
        ])->layout('layouts.app');
    }
}