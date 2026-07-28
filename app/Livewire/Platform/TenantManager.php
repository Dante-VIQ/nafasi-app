<?php

namespace App\Livewire\Platform;

use Livewire\Component;
use App\Models\Tenant;
use Illuminate\Support\Facades\Artisan;

class TenantManager extends Component
{
    public array $tenants = [];
    public bool $showCreateForm = false;

    // Form fields
    public string $id = '';
    public string $name = '';
    public string $domain = '';
    public string $organization = '';
    public string $subscription_tier = 'government';
    public string $region = '';

    protected $rules = [
        'id'                => 'required|alpha_dash|max:50|unique:tenants,id',
        'name'              => 'required|string|max:255',
        'domain'            => 'required|string|max:255|unique:domains,domain',
        'organization'      => 'nullable|string|max:255',
        'subscription_tier' => 'required|in:chemist,clinic,hospital,government,enterprise',
        'region'            => 'nullable|string|max:100',
    ];

    public function mount()
    {
        $this->loadTenants();
    }

    public function loadTenants()
    {
        $this->tenants = Tenant::with('domains')
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    public function createTenant()
    {
        $this->validate();

        // Create tenant record
        $tenant = Tenant::create([
            'id'                  => $this->id,
            'name'                => $this->name,
            'organization'        => $this->organization ?: $this->name,
            'subscription_tier'   => $this->subscription_tier,
            'subscription_status' => 'active',
            'region'              => $this->region,
            'country'             => 'KE',
            'status'              => 'active',
        ]);

        // Attach domain
        $tenant->domains()->create(['domain' => $this->domain]);

        // Run migrations (database must already exist in hPanel + user must have access)
        try {
            Artisan::call('tenants:migrate', ['--tenant' => $this->id]);
            $message = "Tenant '{$this->name}' created and migrations completed.";
        } catch (\Exception $e) {
            $message = "Tenant created, but migrations failed: " . $e->getMessage();
        }

        $this->reset(['id', 'name', 'domain', 'organization', 'subscription_tier', 'region', 'showCreateForm']);
        session()->flash('message', $message);
        $this->loadTenants();
    }

    public function render()
    {
        return view('livewire.platform.tenant-manager')->layout('layouts.app');
    }
}