{{-- resources/views/livewire/reporting/anonymous-report-form.blade.php --}}
<div class="min-h-screen bg-gradient-to-b from-gray-50 to-white">
    <div class="max-w-2xl mx-auto px-4 py-12">
        
        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="text-5xl mb-4">🛡️</div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Anonymous Report</h1>
            <p class="text-gray-600">You are completely anonymous. Your identity is never stored or shared.</p>
        </div>

        @if(!$submitted)
            {{-- Privacy Promise --}}
            <div class="bg-green-50 border-2 border-green-300 rounded-2xl p-6 mb-6">
                <div class="flex items-start">
                    <div class="text-2xl mr-3">🔒</div>
                    <div>
                        <h3 class="font-bold text-green-900 mb-2">Your Anonymity Is Guaranteed</h3>
                        <ul class="space-y-1 text-sm text-green-800">
                            <li>✓ No name collected</li>
                            <li>✓ No phone number collected</li>
                            <li>✓ No email collected</li>
                            <li>✓ Your IP is not stored</li>
                            <li>✓ Report is encrypted</li>
                            <li>✓ Report auto-deletes after 30 days</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Form --}}
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <form wire:submit="submit" class="space-y-6">
                    
                    {{-- Report Type --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">What type of incident? *</label>
                        <select wire:model="report_type" 
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500">
                            <option value="">Select type...</option>
                            @foreach($reportTypes as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('report_type') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">What happened? *</label>
                        <p class="text-xs text-gray-400 mb-2">Describe what you saw or experienced. Don't include your name or personal details.</p>
                        <textarea wire:model="description" rows="5" 
                                  class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500"
                                  placeholder="Describe the incident..."></textarea>
                        <div class="text-xs text-gray-400 mt-1">{{ strlen($description) }}/2000</div>
                        @error('description') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Location --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Where did it happen?</label>
                        <input type="text" wire:model="location_description" 
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500"
                               placeholder="E.g., Near Kenyatta Market, behind the blue building...">
                    </div>

                    {{-- Time --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">When did it happen?</label>
                        <input type="text" wire:model="time_description" 
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500"
                               placeholder="E.g., Last night around 10 PM, this morning...">
                    </div>

                    {{-- Additional Details --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Any other details?</label>
                        <textarea wire:model="additional_details" rows="3" 
                                  class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500"
                                  placeholder="Any other information that might help..."></textarea>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" 
                            class="w-full px-6 py-3 bg-red-600 text-white rounded-xl hover:bg-red-700 font-medium text-lg">
                        🛡️ Submit Anonymously
                    </button>
                </form>
            </div>
        @else
            {{-- Confirmation --}}
            <div class="bg-white rounded-2xl shadow-lg p-8 text-center">
                <div class="text-5xl mb-4">✅</div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Report Submitted</h2>
                <p class="text-gray-600 mb-4">Your anonymous report has been submitted.</p>
                
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <p class="text-sm text-gray-500">Report Reference</p>
                    <p class="text-2xl font-mono font-bold text-gray-900">#{{ $reportUuid }}</p>
                    <p class="text-xs text-gray-400 mt-1">Save this if you want to check status later</p>
                </div>

                @if($routedToName)
                    <p class="text-sm text-gray-600">Routed to: <strong>{{ $routedToName }}</strong></p>
                @endif

                <p class="text-xs text-gray-400 mt-4">The authorities have been notified. Thank you for your courage.</p>
                
                <button wire:click="resetForm" 
                        class="mt-6 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Submit Another Report
                </button>
            </div>
        @endif

        {{-- Emergency Note --}}
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mt-8 rounded-r-lg">
            <p class="text-sm text-red-700">
                <strong>If someone is in immediate danger</strong>, call <span class="font-bold">999</span> now.
                Anonymous reporting is for non-emergency situations.
            </p>
        </div>
    </div>
</div>