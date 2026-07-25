{{-- resources/views/livewire/referral/referral-manager.blade.php --}}
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 py-8">
        
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Referral Management</h1>
            <button wire:click="$toggle('showCreateForm')" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                + New Referral
            </button>
        </div>

        @if (session()->has('message'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r-lg">
                <p class="text-green-700">{{ session('message') }}</p>
            </div>
        @endif

        {{-- Create Referral Form --}}
        @if($showCreateForm)
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold mb-4">New Referral</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Referral Type *</label>
                        <select wire:model="referral_type" class="mt-1 block w-full rounded-lg border-gray-300">
                            <option value="">Select...</option>
                            @foreach($referralTypes as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Urgency</label>
                        <select wire:model="urgency" class="mt-1 block w-full rounded-lg border-gray-300">
                            <option value="routine">Routine (24h)</option>
                            <option value="urgent">Urgent (2h)</option>
                            <option value="immediate">Immediate</option>
                        </select>
                    </div>
                </div>
                <div class="mt-4">
                    <button wire:click="findFacilities" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-sm">
                        Find Receiving Facilities
                    </button>
                </div>
                @if(!empty($suggestedFacilities))
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Suggested Facilities</label>
                        <div class="space-y-2">
                            @foreach($suggestedFacilities as $facility)
                                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" wire:model="receiving_facility_id" value="{{ $facility['id'] }}" class="mr-3">
                                    <div>
                                        <p class="font-medium">{{ $facility['name'] }}</p>
                                        <p class="text-sm text-gray-500">{{ $facility['address'] ?? '' }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">Reason for Referral *</label>
                    <textarea wire:model="reason_for_referral" rows="3" class="mt-1 block w-full rounded-lg border-gray-300"></textarea>
                </div>
                <div class="mt-4 grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Age Group</label>
                        <select wire:model="patient_age_group" class="mt-1 block w-full rounded-lg border-gray-300">
                            <option value="">Select...</option>
                            <option value="infant">Infant (0-1)</option>
                            <option value="child">Child (2-12)</option>
                            <option value="adult">Adult (13-64)</option>
                            <option value="elderly">Elderly (65+)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Gender</label>
                        <select wire:model="patient_gender" class="mt-1 block w-full rounded-lg border-gray-300">
                            <option value="">Select...</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                    <div class="flex items-center space-x-4 mt-6">
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="patient_is_stable" class="rounded">
                            <span class="ml-1 text-sm">Stable</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="requires_ambulance" class="rounded">
                            <span class="ml-1 text-sm">Needs Ambulance</span>
                        </label>
                    </div>
                </div>
                <div class="mt-6">
                    <button wire:click="createReferral" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        Submit Referral
                    </button>
                    <button wire:click="$toggle('showCreateForm')" class="px-6 py-2 text-gray-600 ml-2">Cancel</button>
                </div>
            </div>
        @endif

        {{-- Tabs --}}
        <div class="flex space-x-4 mb-6">
            <button wire:click="$set('tab', 'incoming')" 
                    class="px-4 py-2 rounded-lg text-sm font-medium {{ $tab === 'incoming' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border' }}">
                Incoming Referrals
            </button>
            <button wire:click="$set('tab', 'outgoing')" 
                    class="px-4 py-2 rounded-lg text-sm font-medium {{ $tab === 'outgoing' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border' }}">
                Outgoing Referrals
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Referral List --}}
            <div>
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="divide-y divide-gray-200">
                        @forelse($referrals as $referral)
                            <div wire:click="selectReferral({{ $referral['id'] }})" 
                                 class="p-4 cursor-pointer hover:bg-gray-50 transition-colors
                                    {{ $selectedReferral && $selectedReferral->id === $referral['id'] ? 'bg-blue-50 border-l-4 border-blue-500' : '' }}">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $referral['referral_reference_code'] }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ $tab === 'incoming' ? 'From: ' . ($referral['referring_facility']['name'] ?? 'Unknown') : 'To: ' . ($referral['receiving_facility']['name'] ?? 'Unknown') }}
                                        </p>
                                    </div>
                                    <span class="px-2 py-0.5 text-xs rounded-full
                                        {{ $referral['status'] === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                        {{ $referral['status'] === 'accepted' ? 'bg-green-100 text-green-700' : '' }}
                                        {{ $referral['status'] === 'rejected' ? 'bg-red-100 text-red-700' : '' }}">
                                        {{ ucfirst($referral['status']) }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-gray-500">No referrals found.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Referral Detail --}}
            <div>
                @if($selectedReferral)
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h2 class="text-lg font-semibold mb-4">{{ $selectedReferral->referral_reference_code }}</h2>
                        
                        <dl class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Type:</span>
                                <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $selectedReferral->referral_type)) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Urgency:</span>
                                <span class="font-medium">{{ ucfirst($selectedReferral->urgency) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Status:</span>
                                <span class="font-medium">{{ ucfirst($selectedReferral->status) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">From:</span>
                                <span class="font-medium">{{ $selectedReferral->referringFacility->name ?? 'Unknown' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">To:</span>
                                <span class="font-medium">{{ $selectedReferral->receivingFacility->name ?? 'Unknown' }}</span>
                            </div>
                        </dl>

                        <div class="mt-4 p-3 bg-gray-50 rounded-lg text-sm">
                            <p class="font-medium text-gray-700">Reason:</p>
                            <p class="text-gray-600">{{ $selectedReferral->reason_for_referral }}</p>
                        </div>

                        @if($selectedReferral->status === 'pending' && $tab === 'incoming')
                            <div class="mt-6 flex space-x-3">
                                <button wire:click="acceptReferral" 
                                        class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                                    ✓ Accept
                                </button>
                                <button wire:click="openRejectModal" 
                                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">
                                    ✗ Reject
                                </button>
                            </div>

                            @if($showRejectModal)
                                <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                                    <label class="block text-sm font-medium text-red-700 mb-2">Reason for rejection:</label>
                                    <textarea wire:model="rejectionReason" rows="2" class="w-full rounded-lg border-red-200"></textarea>
                                    <button wire:click="rejectReferral" class="mt-2 px-4 py-2 bg-red-600 text-white rounded-lg text-sm">
                                        Confirm Rejection
                                    </button>
                                    <button wire:click="$toggle('showRejectModal')" class="mt-2 px-4 py-2 text-gray-600 text-sm">Cancel</button>
                                </div>
                            @endif
                        @endif
                    </div>
                @else
                    <div class="bg-white rounded-xl shadow-sm p-8 text-center text-gray-500">
                        Select a referral to view details.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>