{{-- resources/views/livewire/facility/facility-list.blade.php --}}
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 py-8">
        
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Facilities</h1>
                <p class="text-gray-600">Manage all registered facilities</p>
            </div>
            <a href="{{ url('/tenant/facilities/register') }}" 
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                + Register Facility
            </a>
        </div>

        @if (session()->has('message'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r-lg">
                <p class="text-green-700">{{ session('message') }}</p>
            </div>
        @endif

        {{-- Filters --}}
        <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Search</label>
                    <input type="text" wire:model.live.debounce.300ms="search" 
                           placeholder="Name, city, phone..."
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 text-sm px-4 py-2">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Type</label>
                    <select wire:model.live="filterType" class="w-full rounded-lg border-gray-300 text-sm px-4 py-2">
                        <option value="">All Types</option>
                        @foreach($facilityTypes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Status</label>
                    <select wire:model.live="filterStatus" class="w-full rounded-lg border-gray-300 text-sm px-4 py-2">
                        <option value="">All</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="verified">Verified</option>
                        <option value="unverified">Unverified</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button wire:click="$refresh" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900 border rounded-lg">
                        Reset Filters
                    </button>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th wire:click="sortBy('name')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer hover:text-gray-700">
                            Name {{ $sortField === 'name' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($facilities as $facility)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $facility->name }}</div>
                                <div class="text-xs text-gray-500">{{ $facility->phone }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $facilityTypes[$facility->facility_type] ?? $facility->facility_type }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $facility->city ?? '' }}{{ $facility->city && $facility->county ? ', ' : '' }}{{ $facility->county ?? '' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-x-1">
                                    <span class="px-2 py-0.5 text-xs rounded-full {{ $facility->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $facility->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                    <span class="px-2 py-0.5 text-xs rounded-full {{ $facility->is_verified ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $facility->is_verified ? 'Verified' : 'Unverified' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right text-sm">
                                <a href="{{ url('/facility/profile/edit', $facility->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                <button wire:click="toggleActive({{ $facility->id }})" 
                                        class="{{ $facility->is_active ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900' }}">
                                    {{ $facility->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                No facilities found.
                                <a href="{{ url('/tenant/facilities/register') }}" class="text-blue-600 hover:text-blue-800 ml-1">Register one now →</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $facilities->links() }}
        </div>
    </div>
</div>