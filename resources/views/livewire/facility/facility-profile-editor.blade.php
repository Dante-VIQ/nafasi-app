<div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 py-8">
        
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Facility Profile</h1>
                <p class="text-gray-600">{{ $facility->name }}</p>
            </div>
            <a href="{{ route('facility.dashboard') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm">
                ← Back to Dashboard
            </a>
        </div>

        @if (session()->has('message'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r-lg">
                <p class="text-green-700">{{ session('message') }}</p>
            </div>
        @endif

        <form wire:submit.prevent="saveProfile" class="space-y-6">
            
            {{-- Basic Info --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Facility Name *</label>
                        <input type="text" wire:model="name" class="mt-1 w-full rounded-lg border-gray-300">
                        @error('name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Phone *</label>
                        <input type="text" wire:model="phone" class="mt-1 w-full rounded-lg border-gray-300">
                        @error('phone') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Emergency Phone</label>
                        <input type="text" wire:model="emergency_phone" class="mt-1 w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" wire:model="email" class="mt-1 w-full rounded-lg border-gray-300">
                    </div>
                </div>
            </div>

            {{-- Location --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Location</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Address *</label>
                        <textarea wire:model="address" rows="2" class="mt-1 w-full rounded-lg border-gray-300"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Landmark</label>
                        <input type="text" wire:model="landmark" class="mt-1 w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">City</label>
                        <input type="text" wire:model="city" class="mt-1 w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">County</label>
                        <input type="text" wire:model="county" class="mt-1 w-full rounded-lg border-gray-300">
                    </div>
                </div>
            </div>

            {{-- Capabilities --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Services & Capabilities</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                    @foreach($availableCapabilities as $value => $label)
                        <label class="flex items-center p-2 border rounded hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" wire:model="capabilities" value="{{ $value }}" class="rounded border-gray-300 text-blue-600">
                            <span class="ml-2 text-sm">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Emergency Definition --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Emergency Definition</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Emergency Keywords</label>
                        <textarea wire:model="emergencyKeywordsInput" rows="2" 
                                  class="mt-1 w-full rounded-lg border-gray-300"
                                  placeholder="fire, burning, chest pain, snake bite..."></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Exclusion Keywords</label>
                        <textarea wire:model="exclusionKeywordsInput" rows="2" 
                                  class="mt-1 w-full rounded-lg border-gray-300"
                                  placeholder="minor cough, paper cut, prescription refill..."></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Emergency Definition *</label>
                        <textarea wire:model="emergency_definition" rows="4" 
                                  class="mt-1 w-full rounded-lg border-gray-300"
                                  placeholder="Describe what situations are emergencies at your facility..."></textarea>
                    </div>
                </div>
            </div>

            {{-- Operations --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Operations</h2>
                <div class="space-y-4">
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="is_24_hours" class="rounded border-gray-300 text-blue-600">
                        <span class="ml-2 text-sm">Open 24 Hours</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="accepts_referrals" class="rounded border-gray-300 text-blue-600">
                        <span class="ml-2 text-sm">Accepts Referrals from Other Facilities</span>
                    </label>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Languages</label>
                        <div class="space-x-4">
                            <label class="inline-flex items-center">
                                <input type="checkbox" wire:model="languages" value="sw" class="rounded">
                                <span class="ml-1 text-sm">Swahili</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="checkbox" wire:model="languages" value="en" class="rounded">
                                <span class="ml-1 text-sm">English</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="checkbox" wire:model="languages" value="sheng" class="rounded">
                                <span class="ml-1 text-sm">Sheng</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Public Description --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Public Description</h2>
                <textarea wire:model="public_description" rows="3" 
                          class="w-full rounded-lg border-gray-300"
                          placeholder="Short description shown to users searching for help..."></textarea>
            </div>

            {{-- License Document --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">License Document</h2>
                @if($facility->license_document_path)
                    <p class="text-sm text-gray-600 mb-2">Current: {{ basename($facility->license_document_path) }}</p>
                @endif
                <input type="file" wire:model="license_document" class="text-sm">
            </div>

            {{-- Submit --}}
            <div class="flex justify-end">
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                    Save Profile
                </button>
            </div>
        </form>
    </div>
</div>