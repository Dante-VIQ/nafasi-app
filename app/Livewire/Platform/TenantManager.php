<?php

namespace App\Livewire\Platform;

use App\Models\Tenant;
use App\Rules\ValidTenantDatabaseCredentials;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class TenantManager extends Component
{
    public array $tenants = [];
    public bool $showCreateForm = false;

    // Form fields
    public string $id = '';
    public string $name = '';

    // Multiple domains per tenant — e.g. the default subdomain plus a
    // later custom domain. Starts with one empty slot; the admin can
    // add more or remove down to a minimum of one.
    public array $domains = [''];

    public string $organization = '';
    public string $subscription_tier = 'government';
    public string $region = '';

    // Database credentials — pre-provisioned manually in hPanel before
    // this form is submitted. See the suggestedDbName() helper below,
    // which the view uses to show the admin exactly what to create.
    public string $tenancy_db_name = '';
    public string $tenancy_db_username = '';
    public string $tenancy_db_password = '';

    /**
     * Single source of truth for validation rules. Livewire checks for
     * a rules() method before falling back to a $rules property, so we
     * only define the method — keeping both invites them drifting apart.
     */
    protected function rules(): array
    {
        return [
            'id'                  => ['required', 'string', 'alpha_dash', 'max:50', 'unique:tenants,id'],
            'name'                => ['required', 'string', 'max:255'],
            'domains'             => ['required', 'array', 'min:1'],
            'domains.*'           => ['required', 'string', 'max:255', 'distinct', 'unique:domains,domain'],
            'organization'        => ['nullable', 'string', 'max:255'],
            'subscription_tier'   => ['required', 'in:chemist,clinic,hospital,government,enterprise'],
            'region'              => ['nullable', 'string', 'max:100'],
            'tenancy_db_username' => ['required', 'string'],
            'tenancy_db_password' => ['required', 'string'],
            'tenancy_db_name'     => ['required', 'string', new ValidTenantDatabaseCredentials(
                $this->tenancy_db_username,
                $this->tenancy_db_password,
            )],
        ];
    }

    public function mount()
    {
        $this->loadTenants();
        $this->syncSuggestedDbName();
    }

    /**
     * Computed property, available in the view as $this->suggestedDbName.
     * Keeps the naming convention (account prefix + fixed app prefix +
     * tenant id) in one place, so the admin never has to hand-type it
     * and risk the exact mismatch bug we hit earlier.
     */
    public function getSuggestedDbNameProperty(): string
    {
        $prefix = config('hosting.hostinger_account_prefix', 'u355928035_');
        $slug   = str_replace('-', '_', $this->id ?: '[id]');

        return $prefix.'nafasi_'.$slug;
    }

    /**
     * Called whenever `id` changes (wire:model.live) so the db_name
     * field auto-fills with the suggestion rather than staying blank
     * or holding a stale value from a previous id.
     */
    public function updatedId(): void
    {
        $this->syncSuggestedDbName();
    }

    protected function syncSuggestedDbName(): void
    {
        $this->tenancy_db_name = $this->getSuggestedDbNameProperty();
    }

    public function addDomainField(): void
    {
        $this->domains[] = '';
    }

    public function removeDomainField(int $index): void
    {
        // Always keep at least one input visible — nothing to remove
        // down to zero, since every tenant needs at least one domain.
        if (count($this->domains) <= 1) {
            return;
        }

        unset($this->domains[$index]);
        $this->domains = array_values($this->domains);
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

        $tenant = Tenant::create([
            'id'                  => $this->id,
            'name'                => $this->name,
            'organization'        => $this->organization ?: $this->name,
            'subscription_tier'   => $this->subscription_tier,
            'subscription_status' => 'active',
            'region'              => $this->region,
            'country'             => 'KE',
            'status'              => 'active',
            'tenancy_db_name'     => $this->tenancy_db_name,
            'tenancy_db_username' => $this->tenancy_db_username,
            'tenancy_db_password' => $this->tenancy_db_password,
        ]);

        // Filter out any blank slots left from add/remove interactions,
        // then attach every domain to this tenant.
        $tenant->domains()->createMany(
            collect($this->domains)
                ->filter(fn ($d) => trim($d) !== '')
                ->map(fn ($d) => ['domain' => trim($d)])
                ->all()
        );

        try {
            // --tenants (plural, array option) — stancl/tenancy's real
            // option name; --tenant silently migrates ALL tenants instead.
            $exitCode = Artisan::call('tenants:migrate', ['--tenants' => [$this->id]]);

            if ($exitCode !== 0) {
                throw new \RuntimeException('tenants:migrate returned a non-zero exit code.');
            }

            $message = "Tenant '{$this->name}' created and migrations completed.";
        } catch (\Throwable $e) {
            Log::error("TenantManager: migration failed for [{$this->id}]: {$e->getMessage()}");

            // Roll back the half-created tenant so the same id/domain
            // can be retried immediately instead of colliding on unique
            // constraints next attempt.
            $tenant->domains()->delete();
            $tenant->delete();

            session()->flash('error', "Tenant creation failed during migration: {$e->getMessage()}");
            $this->loadTenants();
            return;
        }

        $this->reset([
            'id', 'name', 'domains', 'organization', 'subscription_tier', 'region',
            'tenancy_db_name', 'tenancy_db_username', 'tenancy_db_password',
            'showCreateForm',
        ]);
        $this->domains = ['']; // reset() empties the array entirely; restore one blank slot

        session()->flash('message', $message);
        $this->loadTenants();
    }

    public function render()
    {
        return view('livewire.platform.tenant-manager')->layout('layouts.app');
    }
}