{{-- resources/views/livewire/verification/facility-review-detail.blade.php --}}
<div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 py-8">
        
        <a href="{{ url('/verification/queue') }}" class="text-blue-600 hover:text-blue-800 text-sm mb-4 inline-block">
            ← Back to Queue
        </a>

        @if (session()->has('message'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r-lg">
                <p class="text-green-700">{{ session('message') }}</p>
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $facility->name }}</h1>
                    <p class="text-gray-600">
                        {{ ucfirst(str_replace('_', ' ', $facility->facility_type)) }}
                        @if($facility->health_system_level)
                            · Level {{ $facility->health_system_level }}
                        @endif
                    </p>
                </div>
                <span class="px-3 py-1 rounded-full text-sm font-medium
                    {{ $facility->registration_status === 'submitted' ? 'bg-yellow-100 text-yellow-700' : '' }}
                    {{ $facility->registration_status === 'under_review' ? 'bg-blue-100 text-blue-700' : '' }}
                    {{ $facility->registration_status === 'approved' ? 'bg-green-100 text-green-700' : '' }}
                    {{ $facility->registration_status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}">
                    {{ ucfirst(str_replace('_', ' ', $facility->registration_status)) }}
                </span>
            </div>

            {{-- Basic Info --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-3">Contact</h2>
                    <dl class="space-y-2 text-sm">
                        <div><span class="text-gray-500">Phone:</span> <span class="text-gray-900">{{ $facility->phone }}</span></div>
                        @if($facility->emergency_phone)
                            <div><span class="text-gray-500">Emergency:</span> <span class="text-red-600">{{ $facility->emergency_phone }}</span></div>
                        @endif
                        @if($facility->email)
                            <div><span class="text-gray-500">Email:</span> <span class="text-gray-900">{{ $facility->email }}</span></div>
                        @endif
                        @if($facility->website)
                            <div><span class="text-gray-500">Website:</span> <a href="{{ $facility->website }}" class="text-blue-600">{{ $facility->website }}</a></div>
                        @endif
                    </dl>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-3">Location</h2>
                    <dl class="space-y-2 text-sm">
                        <div><span class="text-gray-500">Address:</span> <span class="text-gray-900">{{ $facility->address }}</span></div>
                        @if($facility->landmark)
                            <div><span class="text-gray-500">Landmark:</span> <span class="text-gray-900">{{ $facility->landmark }}</span></div>
                        @endif
                        <div><span class="text-gray-500">Area:</span> <span class="text-gray-900">{{ $facility->city }}, {{ $facility->county }}</span></div>
                        @if($facility->latitude && $facility->longitude)
                            <div><span class="text-gray-500">Coordinates:</span> <span class="text-gray-900">{{ $facility->latitude }}, {{ $facility->longitude }}</span></div>
                        @endif
                    </dl>
                </div>
            </div>

            {{-- Capabilities --}}
            <div class="mb-6">
                <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-3">Capabilities</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach($facility->capabilities ?? [] as $cap)
                        <span class="px-3 py-1 bg-blue-50 text-blue-700 text-sm rounded-full">
                            {{ $availableCapabilities[$cap] ?? $cap }}
                        </span>
                    @endforeach
                </div>
            </div>

            {{-- Emergency Definition --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-3">Emergency Definition</h2>
                    <p class="text-sm text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $facility->emergency_definition }}</p>
                    <div class="mt-2 flex flex-wrap gap-1">
                        @foreach($facility->emergency_keywords ?? [] as $kw)
                            <span class="px-2 py-0.5 bg-red-50 text-red-700 text-xs rounded-full">{{ $kw }}</span>
                        @endforeach
                    </div>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-3">Exclusions</h2>
                    <p class="text-sm text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $facility->exclusion_definition ?? 'Not specified' }}</p>
                    <div class="mt-2 flex flex-wrap gap-1">
                        @foreach($facility->exclusion_keywords ?? [] as $kw)
                            <span class="px-2 py-0.5 bg-gray-200 text-gray-600 text-xs rounded-full">{{ $kw }}</span>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Operations --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div>
                    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-2">Hours</h2>
                    <p class="text-sm">{{ $facility->is_24_hours ? '24 Hours' : 'Scheduled' }}</p>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-2">Languages</h2>
                    <p class="text-sm">{{ implode(', ', $facility->languages ?? []) }}</p>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-2">Payment</h2>
                    <p class="text-sm">{{ implode(', ', $facility->accepted_payment ?? []) }}</p>
                </div>
            </div>

            {{-- License Document --}}
            @if($facility->license_document_path)
                <div class="mb-6">
                    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-2">License Document</h2>
                    <a href="{{ Storage::url($facility->license_document_path) }}" target="_blank"
                       class="text-blue-600 hover:text-blue-800 text-sm">📄 View Document</a>
                    @if($facility->license_expiry)
                        <span class="text-sm text-gray-500 ml-2">Expires: {{ $facility->license_expiry->format('d M Y') }}</span>
                    @endif
                </div>
            @endif
        </div>

        {{-- Verification Actions --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Verification Decision</h2>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Notes (optional)</label>
                <textarea wire:model="verificationNotes" rows="3" 
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500"
                    placeholder="Any notes about this facility verification..."></textarea>
            </div>

            @if($facility->registration_status === 'submitted')
                <button wire:click="markUnderReview" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                    Mark Under Review
                </button>
            @endif

            @if(in_array($facility->registration_status, ['submitted', 'under_review']))
                <div class="flex gap-3 mt-4">
                    <button wire:click="$toggle('showApproveConfirm')" 
                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                        ✓ Approve Facility
                    </button>
                    <button wire:click="$toggle('showRejectConfirm')" 
                        class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium">
                        ✗ Reject Facility
                    </button>
                </div>

                @if($showApproveConfirm)
                    <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <p class="text-green-800 mb-3">Confirm approval of <strong>{{ $facility->name }}</strong>? It will go live immediately.</p>
                        <button wire:click="approve" class="px-4 py-2 bg-green-600 text-white rounded-lg">Yes, Approve</button>
                        <button wire:click="$toggle('showApproveConfirm')" class="px-4 py-2 text-gray-600 ml-2">Cancel</button>
                    </div>
                @endif

                @if($showRejectConfirm)
                    <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-red-800 mb-3">Reject <strong>{{ $facility->name }}</strong>? This will prevent it from appearing in search results.</p>
                        <button wire:click="reject" class="px-4 py-2 bg-red-600 text-white rounded-lg">Yes, Reject</button>
                        <button wire:click="$toggle('showRejectConfirm')" class="px-4 py-2 text-gray-600 ml-2">Cancel</button>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>