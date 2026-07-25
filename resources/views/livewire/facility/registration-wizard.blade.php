{{-- resources/views/livewire/facility/registration-wizard.blade.php --}}
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Register Your Facility</h1>
            <p class="text-gray-600 mt-2">Join Nafasi to help people find your facility when they need it most.</p>
        </div>

        @if (!$registrationComplete)
            {{-- Progress --}}
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium text-gray-700">Step {{ $step }} of {{ self::TOTAL_STEPS }}</span>
                    <span class="text-sm font-medium text-blue-600">{{ $this->getStepLabel() }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                         style="width: {{ ($step / self::TOTAL_STEPS) * 100 }}%"></div>
                </div>
            </div>

            {{-- Step Content --}}
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <form wire:submit="submit">
                    
                    {{-- Step 1: Basic Info --}}
                    @if ($step === 1)
                        <div>
                            <h2 class="text-xl font-semibold mb-4">Basic Information</h2>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Facility Name *</label>
                                    <input type="text" wire:model="name" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Facility Type *</label>
                                    <select wire:model="facility_type" 
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Select type...</option>
                                        @foreach ($facilityTypes as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('facility_type') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Phone Number *</label>
                                    <input type="text" wire:model="phone" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           placeholder="+254700123456">
                                    @error('phone') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Emergency Phone</label>
                                    <input type="text" wire:model="emergency_phone" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           placeholder="Direct line for emergencies">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Email</label>
                                    <input type="email" wire:model="email" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Health System Level</label>
                                    <select wire:model="health_system_level" 
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Select level...</option>
                                        <option value="1">Level 1 — Community Health Worker</option>
                                        <option value="2">Level 2 — Dispensary</option>
                                        <option value="3">Level 3 — Health Centre</option>
                                        <option value="4">Level 4 — Sub-County Hospital</option>
                                        <option value="5">Level 5 — County Referral Hospital</option>
                                        <option value="6">Level 6 — National Teaching & Referral Hospital</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Step 2: Location --}}
                    @if ($step === 2)
                        <div>
                            <h2 class="text-xl font-semibold mb-4">Location</h2>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Address *</label>
                                    <textarea wire:model="address" rows="2" 
                                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                                    @error('address') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Landmark</label>
                                    <input type="text" wire:model="landmark" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           placeholder="E.g., Next to KCB Bank, Kenyatta Avenue">
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">City</label>
                                        <input type="text" wire:model="city" 
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">County</label>
                                        <select wire:model="county" 
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">Select county...</option>
                                            <option value="Kiambu">Kiambu</option>
                                            <option value="Nairobi">Nairobi</option>
                                            <option value="Mombasa">Mombasa</option>
                                            <option value="Kisumu">Kisumu</option>
                                            <option value="Nakuru">Nakuru</option>
                                            <option value="Machakos">Machakos</option>
                                            <option value="Kajiado">Kajiado</option>
                                            <option value="Meru">Meru</option>
                                            <option value="Nyeri">Nyeri</option>
                                            <option value="Muranga">Murang'a</option>
                                            <option value="Kirinyaga">Kirinyaga</option>
                                            <option value="Embu">Embu</option>
                                            <option value="Laikipia">Laikipia</option>
                                            <option value="Nyandarua">Nyandarua</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <button type="button" wire:click="requestLocation"
                                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                                        📍 Use My Current Location
                                    </button>
                                    @if ($latitude && $longitude)
                                        <p class="text-sm text-green-600 mt-1">✓ Location captured: {{ $latitude }}, {{ $longitude }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Step 3: Capabilities --}}
                    @if ($step === 3)
                        <div>
                            <h2 class="text-xl font-semibold mb-4">Services & Capabilities</h2>
                            <p class="text-gray-600 mb-4">Select all services your facility provides.</p>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-4">
                                @foreach ($availableCapabilities as $value => $label)
                                    <label class="flex items-center p-2 border rounded hover:bg-gray-50 cursor-pointer">
                                        <input type="checkbox" wire:model="capabilities" value="{{ $value }}"
                                               class="rounded border-gray-300 text-blue-600">
                                        <span class="ml-2 text-sm">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea wire:model="description" rows="3" 
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                          placeholder="Detailed description of your facility and services..."></textarea>
                            </div>

                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700">Public Description</label>
                                <textarea wire:model="public_description" rows="2" 
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                          placeholder="Short description shown to users searching for help..."></textarea>
                            </div>
                        </div>
                    @endif

                    {{-- Step 4: Emergency Definition --}}
                    @if ($step === 4)
                        <div>
                            <h2 class="text-xl font-semibold mb-2">Emergency Definition</h2>
                            <p class="text-gray-600 mb-4">Tell us what situations should be treated as emergencies at your facility. This helps us route people correctly.</p>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Emergency Level *</label>
                                    <select wire:model="emergency_level" 
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        @foreach ($emergencyLevels as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">What situations are emergencies for your facility? *</label>
                                    <textarea wire:model="emergency_definition" rows="4" 
                                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                              placeholder="E.g., Any fire, smoke sighting, gas leak. Chest pain, difficulty breathing, severe bleeding. Snake bites. Unconscious person."></textarea>
                                    @error('emergency_definition') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Emergency Keywords</label>
                                    <p class="text-xs text-gray-500 mb-1">Words people might use when they need your help. Separate with commas.</p>
                                    <textarea wire:model="emergencyKeywordsInput" rows="3" 
                                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                              placeholder="fire, burning, smoke, chest pain, bleeding, snake, unconscious, accident, drowning"></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">What should NOT be routed to you?</label>
                                    <textarea wire:model="exclusion_definition" rows="3" 
                                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                              placeholder="E.g., Minor coughs, routine checkups, non-emergency dental"></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Exclusion Keywords</label>
                                    <textarea wire:model="exclusionKeywordsInput" rows="2" 
                                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                              placeholder="minor cough, paper cut, routine checkup, prescription refill only"></textarea>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Step 5: Dispatch --}}
                    @if ($step === 5)
                        <div>
                            <h2 class="text-xl font-semibold mb-4">Dispatch Capability</h2>
                            <p class="text-gray-600 mb-4">Can your facility send help to a patient's location?</p>

                            <div class="space-y-4">
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model.live="can_dispatch_to_patient" 
                                           class="rounded border-gray-300 text-blue-600">
                                    <span class="ml-2 text-sm font-medium">We can dispatch help to patients</span>
                                </label>

                                @if ($can_dispatch_to_patient)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Dispatch Service Type</label>
                                        <select wire:model="dispatch_service_type" 
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">Select type...</option>
                                            @foreach ($dispatchServiceTypes as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Typical Response Time</label>
                                        <input type="text" wire:model="typical_response_time" 
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                               placeholder="E.g., 5-10 minutes, 15-30 minutes">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Maximum Dispatch Radius (km)</label>
                                        <input type="number" wire:model="dispatch_radius_km" 
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                               placeholder="E.g., 15">
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Step 6: Operations --}}
                    @if ($step === 6)
                        <div>
                            <h2 class="text-xl font-semibold mb-4">Operating Hours & Details</h2>
                            <div class="space-y-4">
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model="is_24_hours" 
                                           class="rounded border-gray-300 text-blue-600">
                                    <span class="ml-2 text-sm font-medium">Open 24 hours</span>
                                </label>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Languages</label>
                                    <div class="mt-2 space-x-4">
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" wire:model="languages" value="sw" 
                                                   class="rounded border-gray-300 text-blue-600">
                                            <span class="ml-1 text-sm">Swahili</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" wire:model="languages" value="en" 
                                                   class="rounded border-gray-300 text-blue-600">
                                            <span class="ml-1 text-sm">English</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" wire:model="languages" value="sheng" 
                                                   class="rounded border-gray-300 text-blue-600">
                                            <span class="ml-1 text-sm">Sheng</span>
                                        </label>
                                    </div>
                                </div>

                                <div>
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model="accepts_referrals" 
                                               class="rounded border-gray-300 text-blue-600">
                                        <span class="ml-2 text-sm font-medium">Accepts referrals from other facilities</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Step 7: Verification --}}
                    @if ($step === 7)
                        <div>
                            <h2 class="text-xl font-semibold mb-4">Verification</h2>
                            <p class="text-gray-600 mb-4">Upload your facility license or registration document for verification.</p>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">License Document</label>
                                    <input type="file" wire:model="license_document" 
                                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                    @error('license_document') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">License Expiry Date</label>
                                    <input type="date" wire:model="license_expiry" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Navigation --}}
                    <div class="flex justify-between mt-8 pt-6 border-t">
                        @if ($step > 1)
                            <button type="button" wire:click="previousStep" 
                                    class="px-6 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                                ← Back
                            </button>
                        @else
                            <div></div>
                        @endif

                        @if ($step < self::TOTAL_STEPS)
                            <button type="button" wire:click="nextStep" 
                                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                Next →
                            </button>
                        @else
                            <button type="submit" 
                                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                Submit Registration
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        @else
            {{-- Registration Complete --}}
            <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                <div class="text-5xl mb-4">✅</div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Registration Submitted!</h2>
                <p class="text-gray-600 mb-4">Your facility has been submitted for verification. You'll be notified once it's approved.</p>
                <p class="text-sm text-gray-500">Facility: {{ $registeredFacility->name }}</p>
                <p class="text-sm text-gray-500">Status: Pending Verification</p>
                <a href="{{ route('dashboard') }}" class="inline-block mt-6 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Go to Dashboard
                </a>
            </div>
        @endif

    </div>
</div>