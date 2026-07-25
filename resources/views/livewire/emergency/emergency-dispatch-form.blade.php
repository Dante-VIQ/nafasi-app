{{-- resources/views/livewire/emergency/emergency-dispatch-form.blade.php --}}
<div class="min-h-screen bg-gradient-to-b from-red-50 to-white">
    <div class="max-w-2xl mx-auto px-4 py-12">
        
        <div class="text-center mb-8">
            <div class="text-6xl mb-4">🚨</div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Emergency Dispatch</h1>
            <p class="text-gray-600">We'll send a trained responder to you immediately.</p>
        </div>

        @if(!$dispatched)
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type of Emergency *</label>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($emergencyTypes as $value => $label)
                            <button wire:click="$set('emergency_type', '{{ $value }}')"
                                class="p-4 text-left rounded-xl border-2 transition-all
                                    {{ $emergency_type === $value ? 'border-red-500 bg-red-50 ring-2 ring-red-200' : 'border-gray-200 hover:border-red-300' }}">
                                <div class="font-medium text-sm">{{ $label }}</div>
                            </button>
                        @endforeach
                    </div>
                    @error('emergency_type') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Where are you?</label>
                    <textarea wire:model="location_description" rows="3"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500"
                        placeholder="Describe your location with landmarks..."></textarea>
                    
                    @if(!$locationGranted)
                        <button wire:click="requestLocation" class="mt-2 text-sm text-blue-600 hover:text-blue-800">
                            📍 Share GPS location instead
                        </button>
                    @else
                        <p class="text-sm text-green-600 mt-2">✓ GPS location captured</p>
                    @endif
                </div>

                <button wire:click="dispatchHelp"
                    class="w-full px-6 py-4 bg-red-600 text-white rounded-xl hover:bg-red-700 font-bold text-lg">
                    🚨 Dispatch Help Now
                </button>
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-lg p-8">
                @if($dispatchResult['success'])
                    <div class="text-center mb-6">
                        <div class="text-5xl mb-4">🏍️</div>
                        <h2 class="text-2xl font-bold text-green-700 mb-2">Help Is On The Way!</h2>
                        <p class="text-3xl font-black text-green-600">{{ $dispatchResult['eta_to_patient_minutes'] }} min ETA</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-blue-50 rounded-lg p-4">
                            <p class="text-xs text-blue-600 font-medium">RESPONDER</p>
                            <p class="font-bold text-gray-900">{{ $dispatchResult['responder']['name'] }}</p>
                            <p class="text-sm text-gray-600">{{ $dispatchResult['responder']['qualification'] }}</p>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4">
                            <p class="text-xs text-green-600 font-medium">RIDER</p>
                            <p class="font-bold text-gray-900">{{ $dispatchResult['rider']['name'] }}</p>
                            <p class="text-sm text-gray-600">{{ $dispatchResult['rider']['motorbike_reg'] }}</p>
                        </div>
                    </div>

                    @if($dispatchResult['facility'])
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <p class="text-xs text-gray-500 font-medium">DESTINATION FACILITY</p>
                            <p class="font-bold text-gray-900">{{ $dispatchResult['facility']['name'] }}</p>
                        </div>
                    @endif

                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                        <p class="text-sm font-semibold text-yellow-800 mb-2">While You Wait:</p>
                        <ul class="space-y-1">
                            @foreach($dispatchResult['instructions'] as $instruction)
                                <li class="text-sm text-yellow-700">• {{ $instruction }}</li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="text-center">
                        <p class="text-red-700 font-bold text-lg">{{ $dispatchResult['message'] }}</p>
                        <a href="tel:{{ $dispatchResult['emergency_number'] }}"
                            class="inline-block mt-4 px-6 py-3 bg-red-600 text-white rounded-xl font-bold">
                            📞 Call {{ $dispatchResult['emergency_number'] }}
                        </a>
                    </div>
                @endif
            </div>
        @endif

        <div class="bg-red-50 border-l-4 border-red-500 p-4 mt-8 rounded-r-lg">
            <p class="text-sm text-red-700">
                <strong>Life-threatening?</strong> Call <span class="font-bold">999</span> immediately.
            </p>
        </div>
    </div>
</div>