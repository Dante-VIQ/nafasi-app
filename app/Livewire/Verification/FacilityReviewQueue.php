<?php

namespace App\Livewire\Verification;

use Livewire\Component;
use App\Models\Tenant\Facility;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

class FacilityReviewQueue extends Component
{
    public string $filter = 'submitted';
    public string $search = '';

    public function render()
    {
        $allFacilities = collect();

        // Get all active tenants
        $tenants = Tenant::where('status', '=', 'active')->get();

        foreach ($tenants as $tenant) {
            // Switch the tenant connection to this tenant's database
            $database = 'nafasi_tenant_' . $tenant->id;
            config(["database.connections.tenant.database" => $database]);
            DB::purge('tenant');
            DB::reconnect('tenant');

            // Query facilities from this tenant
            $facilities = Facility::on('tenant')
                ->when($this->filter, fn($q) => $q->where('registration_status', $this->filter))
                ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                ->orderBy('created_at', 'desc')
                ->get();

            $allFacilities = $allFacilities->concat($facilities);
        }

        // Sort the combined results by created_at
        $allFacilities = $allFacilities->sortByDesc('created_at')->values();

        // Paginate manually (simple approach)
        $page = request()->get('page', 1);
        $perPage = 20;
        $paginated = $allFacilities->forPage($page, $perPage);

        return view('livewire.verification.facility-review-queue', [
            'facilities' => $paginated,
            'totalCount' => $allFacilities->count(),
        ])->layout('layouts.app');
    }
}