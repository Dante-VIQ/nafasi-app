{{-- resources/views/livewire/coordinator/request-queue.blade.php --}}
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 py-8">
        
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Coordinator Dashboard</h1>
                <p class="text-gray-600">Manage incoming assistance requests</p>
            </div>
            <button wire:click="refreshQueue" class="px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200 text-sm">
                🔄 Refresh Queue
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Pending Queue --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm p-4">
                    <h2 class="font-semibold text-gray-900 mb-4">
                        Pending Requests 
                        <span class="bg-red-100 text-red-700 text-xs px-2 py-0.5 rounded-full ml-2">
                            {{ count($pendingRequests) }}
                        </span>
                    </h2>

                    @if(count($pendingRequests) === 0)
                        <p class="text-gray-500 text-sm text-center py-8">No pending requests</p>
                    @else
                        <div class="space-y-3">
                            @foreach($pendingRequests as $request)
                                <div class="border rounded-lg p-3 hover:border-blue-300 cursor-pointer transition-colors
                                    {{ $request['urgency'] === 'emergency' ? 'border-red-200 bg-red-50' : '' }}"
                                    wire:click="acceptRequest('{{ $request['id'] }}')">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-xs text-gray-500">
                                            {{ \Carbon\Carbon::parse($request['created_at'])->diffForHumans() }}
                                        </span>
                                        @if($request['urgency'] === 'emergency')
                                            <span class="px-2 py-0.5 bg-red-100 text-red-700 text-xs rounded-full">🚨 Emergency</span>
                                        @elseif($request['urgency'] === 'urgent')
                                            <span class="px-2 py-0.5 bg-orange-100 text-orange-700 text-xs rounded-full">⚠️ Urgent</span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-700 line-clamp-2">{{ $request['user_description'] ?? 'No description' }}</p>
                                    @if($request['location_description'])
                                        <p class="text-xs text-gray-400 mt-1">📍 {{ $request['location_description'] }}</p>
                                    @endif
                                    <p class="text-xs text-gray-400 mt-1">🗣 {{ strtoupper($request['preferred_language']) }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Active Request --}}
            <div class="lg:col-span-2">
                @if($activeRequest)
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-semibold text-gray-900">
                                Active Request #{{ substr($activeRequest['uuid'], 0, 8) }}
                            </h2>
                            <span class="px-3 py-1 rounded-full text-sm font-medium
                                {{ $activeRequest['status'] === 'accepted' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                {{ $activeRequest['status'] === 'dispatching' ? 'bg-blue-100 text-blue-700' : '' }}">
                                {{ ucfirst($activeRequest['status']) }}
                            </span>
                        </div>

                        {{-- Request Details --}}
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="block text-xs text-gray-500">Description</label>
                                <p class="text-sm text-gray-900">{{ $activeRequest['user_description'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500">Location</label>
                                <p class="text-sm text-gray-900">{{ $activeRequest['location_description'] ?? 'Not provided' }}</p>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500">Language</label>
                                <p class="text-sm text-gray-900">{{ strtoupper($activeRequest['preferred_language']) }}</p>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500">Urgency</label>
                                <p class="text-sm text-gray-900">{{ ucfirst($activeRequest['urgency']) }}</p>
                            </div>
                            @if($activeRequest['phone_number'])
                                <div>
                                    <label class="block text-xs text-gray-500">Callback Number</label>
                                    <a href="tel:{{ $activeRequest['phone_number'] }}" class="text-sm text-blue-600">
                                        📞 {{ $activeRequest['phone_number'] }}
                                    </a>
                                </div>
                            @endif
                        </div>

                        {{-- Detected Tags --}}
                        @if(!empty($activeRequest['detected_tags']))
                            <div class="mb-6">
                                <label class="block text-xs text-gray-500 mb-2">Detected Needs</label>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($activeRequest['detected_tags'] as $tag)
                                        <span class="px-2 py-0.5 bg-blue-50 text-blue-700 text-xs rounded-full">{{ $tag }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Coordinator Notes --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Coordinator Notes</label>
                            <textarea wire:model="coordinatorNotes" rows="3" 
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500"
                                placeholder="Notes about this request..."></textarea>
                        </div>

                        {{-- Dispatch Section --}}
                        @if($activeRequest['status'] !== 'dispatching')
                            <div class="border-t pt-6">
                                <h3 class="text-sm font-semibold text-gray-700 mb-4">Dispatch Service</h3>
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Facility/Service</label>
                                        <select wire:model="dispatchFacilityId" 
                                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 text-sm">
                                            <option value="">Select dispatch service...</option>
                                            @foreach($dispatchFacilities as $facility)
                                                <option value="{{ $facility->id }}">
                                                    {{ $facility->name }} ({{ $facility->dispatch_service_type }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Estimated Arrival</label>
                                        <input type="text" wire:model="estimatedArrival" 
                                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 text-sm"
                                            placeholder="e.g., 15-20 minutes">
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-xs text-gray-500 mb-1">Message to Facility</label>
                                    <textarea wire:model="dispatchMessage" rows="2" 
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 text-sm"
                                        placeholder="Instructions for the responding service..."></textarea>
                                </div>
                                <button wire:click="dispatchService" 
                                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                                    🚀 Dispatch Help
                                </button>
                            </div>
                        @else
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                                <p class="text-green-700 text-sm">✓ Dispatched — {{ $activeRequest['estimated_arrival'] }} ETA</p>
                            </div>
                        @endif

                        {{-- Actions --}}
                        <div class="border-t pt-4 flex justify-between">
                            <button wire:click="cancelRequest" 
                                class="px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg text-sm">
                                Cancel Request
                            </button>
                            <div class="space-x-2">
                                <button wire:click="resolveRequest('transferred')" 
                                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm">
                                    Transfer
                                </button>
                                <button wire:click="resolveRequest('resolved')" 
                                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                                    ✓ Resolve
                                </button>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-white rounded-xl shadow-sm p-12 text-center">
                        <div class="text-5xl mb-4">📋</div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No Active Request</h3>
                        <p class="text-gray-500">Select a pending request from the queue to begin.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>