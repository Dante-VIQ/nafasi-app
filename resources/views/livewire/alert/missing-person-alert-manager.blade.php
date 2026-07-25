<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Missing Person Alerts</h1>
        <button wire:click="$toggle('showCreateForm')" class="bg-blue-600 text-white px-4 py-2 rounded-lg">
            + New Alert
        </button>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('message') }}
        </div>
    @endif

    <!-- Create/Edit Form -->
    @if($showCreateForm)
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">Create Missing Person Alert</h2>
            <form wire:submit.prevent="createAlert" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium">Name / Nickname *</label>
                        <input type="text" wire:model="name" class="mt-1 w-full rounded-lg border-gray-300">
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Age Group</label>
                        <select wire:model="age_group" class="mt-1 w-full rounded-lg border-gray-300">
                            <option value="">Select...</option>
                            <option value="infant">Infant (0-1)</option>
                            <option value="child">Child (2-12)</option>
                            <option value="adult">Adult (13-64)</option>
                            <option value="elderly">Elderly (65+)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Gender</label>
                        <select wire:model="gender" class="mt-1 w-full rounded-lg border-gray-300">
                            <option value="">Select...</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Contact Phone (Authority)</label>
                        <input type="text" wire:model="contact_phone" class="mt-1 w-full rounded-lg border-gray-300">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium">Description *</label>
                    <textarea wire:model="description" rows="3" class="mt-1 w-full rounded-lg border-gray-300"
                              placeholder="Physical description, clothing, distinguishing features..."></textarea>
                    @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium">Last Seen Location</label>
                    <input type="text" wire:model="last_seen_location" class="mt-1 w-full rounded-lg border-gray-300"
                           placeholder="Where and when were they last seen?">
                </div>

                <div>
                    <label class="block text-sm font-medium">Suspect Description</label>
                    <textarea wire:model="suspect_description" rows="2" class="mt-1 w-full rounded-lg border-gray-300"
                              placeholder="Description of anyone involved in the disappearance..."></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium">Photo of Missing Person</label>
                        <input type="file" wire:model="photo" class="mt-1">
                        <p class="text-xs text-gray-400">Photo will be stripped of location data automatically.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Photo of Suspect (if available)</label>
                        <input type="file" wire:model="suspect_photo" class="mt-1">
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" wire:click="$toggle('showCreateForm')" class="px-4 py-2 border rounded-lg">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Create Alert</button>
                </div>
            </form>
        </div>
    @endif

    <!-- Alerts List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Age/Gender</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Last Seen</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sightings</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($alerts as $alert)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="font-medium">{{ $alert['name'] }}</div>
                            @if($alert['photo_path'])
                                <img src="{{ Storage::url($alert['photo_path']) }}" class="h-10 w-10 object-cover rounded mt-1">
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">
                            {{ ucfirst($alert['age_group'] ?? 'N/A') }} / {{ ucfirst($alert['gender'] ?? 'N/A') }}
                        </td>
                        <td class="px-6 py-4 text-sm">{{ $alert['last_seen_location'] ?? 'N/A' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full 
                                {{ $alert['status'] === 'active' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                {{ ucfirst($alert['status']) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <button wire:click="viewSightings({{ $alert['id'] }})" class="text-blue-600 hover:underline">
                                {{ count($alert['sighting_reports'] ?? []) }} sightings
                            </button>
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($alert['status'] === 'active')
                                <button wire:click="closeAlert({{ $alert['id'] }})" class="text-green-600 hover:underline text-sm">
                                    Mark Found
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Sightings Modal -->
    @if($selectedAlert)
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full mx-4 p-6">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-lg font-semibold">Sightings for {{ $selectedAlert->name }}</h3>
                    <button wire:click="$set('selectedAlert', null)" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @forelse($sightings as $sighting)
                        <div class="border rounded-lg p-3">
                            <div class="flex justify-between text-sm">
                                <span class="font-medium">{{ $sighting['reported_at'] ? \Carbon\Carbon::parse($sighting['reported_at'])->diffForHumans() : 'N/A' }}</span>
                            </div>
                            <p class="text-gray-600 mt-1">{{ $sighting['notes'] ?? 'No notes' }}</p>
                            @if($sighting['latitude'] && $sighting['longitude'])
                                <p class="text-xs text-gray-400 mt-1">
                                    Location: {{ $sighting['latitude'] }}, {{ $sighting['longitude'] }}
                                </p>
                            @endif
                        </div>
                    @empty
                        <p class="text-gray-500">No sightings reported yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif
</div>