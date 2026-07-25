{{-- resources/views/livewire/admin/partner-manager.blade.php --}}
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Partner Helpline Management</h1>

        {{-- Partner List --}}
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">Registered Partners</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($partners as $crisisType => $partnerGroup)
                    <div class="border rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 mb-2">
                            {{ ucfirst(str_replace('_', ' ', $crisisType)) }}
                        </h3>
                        @foreach($partnerGroup as $priority => $partner)
                            <div class="text-sm mb-2 {{ $priority === 'primary' ? 'border-l-2 border-green-500 pl-2' : 'border-l-2 border-gray-300 pl-2' }}">
                                <p class="font-medium">{{ $partner['name'] }} 
                                    <span class="text-xs text-gray-400">({{ ucfirst($priority) }})</span>
                                </p>
                                <p class="text-gray-500">📞 {{ $partner['phone'] }}</p>
                                <p class="text-gray-400">🕐 {{ $partner['hours'] }} | 🗣 {{ implode(', ', $partner['languages']) }}</p>
                                @if(isset($partner['note']))
                                    <p class="text-gray-400 text-xs">ℹ️ {{ $partner['note'] }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Test Partner Finder --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold mb-4">Test Partner Matching</h2>
            <div class="flex gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Crisis Type</label>
                    <select wire:model="testCrisisType" class="mt-1 rounded-lg border-gray-300">
                        <option value="suicide_self_harm">Suicide / Self-Harm</option>
                        <option value="sexual_assault">Sexual Assault</option>
                        <option value="violence_abuse">Violence / Abuse</option>
                        <option value="mental_health_distress">Mental Health Distress</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Language</label>
                    <select wire:model="testLanguage" class="mt-1 rounded-lg border-gray-300">
                        <option value="sw">Swahili</option>
                        <option value="en">English</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button wire:click="testFindPartner" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                        Test Match
                    </button>
                </div>
            </div>

            @if($testResult)
                <div class="bg-gray-50 rounded-lg p-4">
                    <pre class="text-sm text-gray-700">{{ json_encode($testResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            @endif
        </div>
    </div>
</div>