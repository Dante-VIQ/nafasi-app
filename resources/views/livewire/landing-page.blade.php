<div class="min-h-screen bg-gradient-to-b from-blue-50 to-white">
    {{-- Emergency Banner --}}
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg max-w-7xl mx-auto mt-4">
        <p class="text-sm text-red-700">
            <strong>Life-threatening emergency?</strong>
            Call <span class="font-bold text-xl">999</span> immediately.
        </p>
    </div>

    <div class="max-w-7xl mx-auto px-4 pb-12">
        {{-- Mobile: Community Alerts (collapsible) --}}
        <div class="lg:hidden mb-6" x-data="{ open: false }">
            <button @click="open = !open" class="w-full bg-red-50 text-red-700 font-semibold p-3 rounded-lg flex justify-between items-center">
                🚨 Community Alerts – Missing Persons
                <svg :class="open ? 'rotate-180' : ''" class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open" x-collapse>
                <div class="mt-2">
                    <livewire:alert.community-alert-feed />
                </div>
            </div>
        </div>

        <div class="lg:grid lg:grid-cols-3 lg:gap-8">
            {{-- Main Content (Search + Results) --}}
            <div class="lg:col-span-2">
                {{-- Header --}}
                <div class="text-center mb-8">
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">Nafasi</h1>
                    <p class="text-lg text-gray-600">Find the right help, right now.</p>
                </div>

                @if (!$result)
                    {{-- Input Section --}}
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">What do you need?</label>
                            <div class="flex gap-2">
                                <input type="text" wire:model="situation" wire:keydown.enter="submit"
                                    placeholder="e.g., pharmacy near me, I need a lab test, there's a fire…"
                                    class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-3 text-lg"
                                    autofocus>
                                <button wire:click="submit"
                                    class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-lg">
                                    Find Help
                                </button>
                            </div>
                            @error('situation')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Quick Select Chips --}}
                        <div class="flex flex-wrap gap-2 mb-4">
                            <button wire:click="$set('situation', 'pharmacy near me')"
                                class="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-full text-sm hover:bg-blue-100">💊 Pharmacy</button>
                            <button wire:click="$set('situation', 'lab test')"
                                class="px-3 py-1.5 bg-green-50 text-green-700 rounded-full text-sm hover:bg-green-100">🧪 Lab Test</button>
                            <button wire:click="$set('situation', 'dental clinic')"
                                class="px-3 py-1.5 bg-purple-50 text-purple-700 rounded-full text-sm hover:bg-purple-100">🦷 Dental</button>
                            <button wire:click="$set('situation', 'maternity care')"
                                class="px-3 py-1.5 bg-pink-50 text-pink-700 rounded-full text-sm hover:bg-pink-100">🤰 Maternity</button>
                            <button wire:click="$set('situation', 'hospital near me')"
                                class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200">🏥 Hospital</button>
                            <button wire:click.prevent="window.location='{{ route('emergency.dispatch') }}'"
                                class="px-3 py-1.5 bg-red-50 text-red-700 rounded-full text-sm hover:bg-red-100">🚨 Emergency Help</button>
                            <a href="{{ url('/report/anonymous') }}"
                                class="px-3 py-1.5 bg-red-50 text-red-700 rounded-full text-sm hover:bg-red-100">🛡️ Report Anonymously</a>
                        </div>

                        {{-- Location --}}
                        <div>
                            @if (!$locationGranted)
                                <button wire:click="requestLocation"
                                    class="text-sm text-blue-600 hover:text-blue-800 flex items-center">
                                    📍 Share my location for nearest results
                                </button>
                            @else
                                <span class="text-sm text-green-600">✓ Location shared</span>
                            @endif
                        </div>
                    </div>
                @else
                    {{-- Results Section --}}
                    <div>
                        <button wire:click="resetSearch"
                            class="text-blue-600 hover:text-blue-800 mb-4 flex items-center text-sm">
                            ← New search
                        </button>

                        @if ($result['type'] === 'crisis')
                            <div class="bg-purple-600 text-white rounded-2xl p-6 text-center">
                                <div class="text-4xl mb-3">🤝</div>
                                <h2 class="text-2xl font-bold mb-2">You Are Not Alone</h2>
                                <p class="mb-4">{{ $result['message'] }}</p>
                                <div class="space-y-3">
                                    <a href="tel:{{ $result['emergency_number'] }}"
                                        class="block w-full px-6 py-3 bg-white text-purple-700 rounded-xl font-bold text-lg hover:bg-purple-50">
                                        📞 Call {{ $result['emergency_number'] }}
                                    </a>
                                    <a href="{{ route('crisis.chat') }}"
                                        class="block w-full px-6 py-3 bg-purple-500 text-white rounded-xl font-bold text-lg hover:bg-purple-400 border border-purple-400">
                                        💬 Chat with Someone Now
                                    </a>
                                </div>
                            </div>
                        @elseif($result['type'] === 'emergency')
                            <div class="bg-red-600 text-white rounded-2xl p-6 text-center">
                                <div class="text-4xl mb-3">🚨</div>
                                <h2 class="text-2xl font-bold mb-2">Emergency</h2>
                                <p class="mb-4">{{ $result['message'] }}</p>
                                <a href="tel:{{ $result['emergency_number'] }}"
                                    class="inline-block px-6 py-3 bg-white text-red-700 rounded-xl font-bold text-lg hover:bg-red-50">
                                    📞 Call {{ $result['emergency_number'] }}
                                </a>
                            </div>
                        @elseif($result['type'] === 'dispatch' || $result['type'] === 'dispatch_created')
                            <div class="bg-green-600 text-white rounded-2xl p-6 text-center">
                                <div class="text-4xl mb-3">🚑</div>
                                <h2 class="text-2xl font-bold mb-2">Help Is Coming</h2>
                                <p>{{ $result['message'] }}</p>
                                <p class="text-sm mt-2">A coordinator will contact you shortly.</p>
                            </div>
                        @elseif($result['type'] === 'facilities')
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">
                                {{ count($result['facilities']) }} found
                            </h2>
                            <div class="space-y-4">
                                @foreach ($result['facilities'] as $facility)
                                    <div class="bg-white rounded-xl shadow p-4">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h3 class="font-semibold text-gray-900">{{ $facility['name'] }}</h3>
                                                <p class="text-sm text-gray-600">{{ $facility['address'] }}</p>
                                                @if (isset($facility['distance']))
                                                    <span class="text-xs text-gray-500">{{ round($facility['distance'], 1) }} km away</span>
                                                @endif
                                            </div>
                                            <div class="text-right">
                                                @if ($facility['congestion_status'])
                                                    <span class="px-2 py-0.5 text-xs rounded-full 
                                                        {{ $facility['congestion_status'] === 'low' ? 'bg-green-100 text-green-700' : '' }}
                                                        {{ $facility['congestion_status'] === 'moderate' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                                        {{ $facility['congestion_status'] === 'high' ? 'bg-red-100 text-red-700' : '' }}">
                                                        {{ ucfirst($facility['congestion_status']) }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        @if (!empty($facility['capabilities']))
                                            <div class="flex flex-wrap gap-1 mt-2">
                                                @foreach (array_slice($facility['capabilities'], 0, 5) as $cap)
                                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded">{{ $cap }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                        <div class="mt-3 flex gap-2 items-center">
                                            @if ($facility['phone'])
                                                <a href="tel:{{ $facility['phone'] }}" class="text-sm text-blue-600 hover:text-blue-800">📞 Call</a>
                                            @endif
                                            @if (isset($facility['distance']))
                                                <a href="https://www.google.com/maps/dir/?api=1&destination={{ urlencode($facility['name']) }}"
                                                    target="_blank" class="text-sm text-blue-600 hover:text-blue-800">🗺️ Directions</a>
                                            @endif
                                            <livewire:booking-form :facilityId="$facility['id']" :key="$facility['id']" />
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                {{-- PWA Install Prompt --}}
                <div id="install-prompt"
                    class="hidden fixed bottom-4 left-4 right-4 bg-white rounded-xl shadow-2xl p-4 z-50 border-2 border-blue-500 max-w-md mx-auto">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-gray-900">Install Nafasi</p>
                            <p class="text-sm text-gray-600">Add to home screen for quick access</p>
                        </div>
                        <div class="flex space-x-2">
                            <button onclick="document.getElementById('install-prompt').classList.add('hidden')"
                                class="px-3 py-1 text-gray-500 text-sm">Later</button>
                            <button id="install-button"
                                class="px-4 py-1 bg-blue-600 text-white rounded-lg text-sm font-medium">Install</button>
                        </div>
                    </div>
                </div>

                {{-- Privacy Note --}}
                <p class="text-center text-xs text-gray-400 mt-8">
                    🔒 We never store your medical information. You are anonymous.
                </p>
            </div>

            {{-- Desktop: Right Sidebar for Community Alerts --}}
            <div class="hidden lg:block">
                <div class="sticky top-4">
                    <livewire:alert.community-alert-feed />
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Alpine.js geolocation + PWA install --}}
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('request-geolocation', () => {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        Livewire.dispatch('set-location', {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        });
                    },
                    (error) => {
                        alert('Could not get location. You can still search by typing your area.');
                    }
                );
            } else {
                alert('Geolocation not supported by your browser.');
            }
        });
    });

    let deferredPrompt;
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        document.getElementById('install-prompt').classList.remove('hidden');
    });
    document.getElementById('install-button')?.addEventListener('click', () => {
        if (deferredPrompt) {
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then((result) => {
                document.getElementById('install-prompt').classList.add('hidden');
                deferredPrompt = null;
            });
        }
    });
</script>