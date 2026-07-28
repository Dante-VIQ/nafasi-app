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

    <!-- Create Form -->
    @if($showCreateForm)
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">Create Tenant</h2>
            <div class="bg-yellow-50 text-yellow-800 p-3 rounded mb-4 text-sm">
                ⚠️ Before creating, manually create the database <code>nafasi_{{$id ?? '[id]'}}</code> in hPanel and assign user <code>u355928035_nafasi</code> with all privileges.
            </div>
            <form wire:submit.prevent="createTenant" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">Tenant ID *</label>
                    <input wire:model.live="id" class="mt-1 w-full rounded-lg border-gray-300" placeholder="kiambu">
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
                <div class="md:col-span-2 flex justify-end space-x-3">
                    <button type="button" wire:click="$toggle('showCreateForm')" class="px-4 py-2 border rounded-lg">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Create Tenant</button>
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