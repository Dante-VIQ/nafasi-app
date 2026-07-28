<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Tenant Management</h1>
        <button wire:click="$toggle('showCreateForm')" class="bg-blue-600 text-white px-4 py-2 rounded-lg">
            + New Tenant
        </button>
    </div>

    @if(session()->has('message'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('message') }}</div>
    @endif

    @if(session()->has('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">{{ session('error') }}</div>
    @endif

    <!-- Create Form -->
    @if($showCreateForm)
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">Create Tenant</h2>
            <div class="bg-yellow-50 text-yellow-800 p-3 rounded mb-4 text-sm">
                ⚠️ Before submitting, create this exact database in hPanel, with its own
                dedicated MySQL user (Hostinger gives each database its own user — don't
                reuse one across tenants):
                <div class="mt-2 font-mono bg-yellow-100 px-2 py-1 rounded inline-block">
                    {{ $this->suggestedDbName }}
                </div>
                <div class="mt-1">Then paste the username/password hPanel gives you below — they'll be verified before the tenant is created.</div>
            </div>
            <form wire:submit.prevent="createTenant" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">Tenant ID *</label>
                    <input wire:model.live="id" class="mt-1 w-full rounded-lg border-gray-300" placeholder="kiambu-county">
                    @error('id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">Display Name *</label>
                    <input wire:model="name" class="mt-1 w-full rounded-lg border-gray-300" placeholder="Kiambu County">
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">Domain *</label>
                    <input wire:model="domain" class="mt-1 w-full rounded-lg border-gray-300" placeholder="kiambu.vumbidna.com">
                    @error('domain') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">Organization</label>
                    <input wire:model="organization" class="mt-1 w-full rounded-lg border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium">Subscription Tier</label>
                    <select wire:model="subscription_tier" class="mt-1 w-full rounded-lg border-gray-300">
                        <option value="government">Government</option>
                        <option value="chemist">Chemist</option>
                        <option value="clinic">Clinic</option>
                        <option value="hospital">Hospital</option>
                        <option value="enterprise">Enterprise</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium">Region</label>
                    <input wire:model="region" class="mt-1 w-full rounded-lg border-gray-300">
                </div>

                <div class="md:col-span-2 border-t pt-4 mt-2">
                    <h3 class="text-sm font-semibold text-gray-700 mb-2">Database credentials (from hPanel)</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium">Database Name *</label>
                    <input wire:model="tenancy_db_name" class="mt-1 w-full rounded-lg border-gray-300 font-mono text-sm">
                    @error('tenancy_db_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">Database Username *</label>
                    <input wire:model="tenancy_db_username" class="mt-1 w-full rounded-lg border-gray-300 font-mono text-sm">
                    @error('tenancy_db_username') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">Database Password *</label>
                    <input type="password" wire:model="tenancy_db_password" class="mt-1 w-full rounded-lg border-gray-300 font-mono text-sm">
                    @error('tenancy_db_password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-2 flex justify-end space-x-3">
                    <button type="button" wire:click="$toggle('showCreateForm')" class="px-4 py-2 border rounded-lg">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg" wire:loading.attr="disabled" wire:target="createTenant">
                        <span wire:loading.remove wire:target="createTenant">Create Tenant</span>
                        <span wire:loading wire:target="createTenant">Creating & migrating…</span>
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Tenant List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tenant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Domain</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tier</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Region</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($tenants as $tenant)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="font-medium">{{ $tenant['name'] }}</div>
                            <div class="text-xs text-gray-500">{{ $tenant['id'] }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            {{ $tenant['domains'][0]['domain'] ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-sm capitalize">{{ $tenant['subscription_tier'] }}</td>
                        <td class="px-6 py-4 text-sm">{{ $tenant['region'] ?? '—' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full {{ $tenant['status'] === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ ucfirst($tenant['status']) }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>