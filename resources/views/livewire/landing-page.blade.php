<div
    class="min-h-screen bg-gradient-to-br from-slate-50 via-indigo-50/40 to-blue-50 text-slate-800 antialiased selection:bg-indigo-500 selection:text-white pb-12">

    {{-- Emergency Banner (3D Pulsing Floating Card) --}}
    <div class="max-w-7xl mx-auto px-4 pt-4 mb-8">
        <div
            class="relative overflow-hidden bg-gradient-to-r from-red-500 via-rose-500 to-red-600 text-white p-4 rounded-2xl shadow-lg shadow-red-500/20 border-t border-white/30 transform transition-all hover:-translate-y-0.5">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center space-x-3">
                    <span class="flex h-3 w-3 relative">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-white"></span>
                    </span>
                    <p class="text-sm md:text-base font-medium tracking-wide text-white/95">
                        <strong class="font-bold text-white">Life-threatening emergency?</strong> Immediate response
                        needed.
                    </p>
                </div>
                <a href="tel:999"
                    class="inline-flex items-center gap-1.5 bg-white text-red-600 font-extrabold px-4 py-2 rounded-xl text-base shadow-md hover:bg-red-50 transition-all hover:scale-105 active:scale-95">
                    <span>📞 Call</span>
                    <span class="text-xl tracking-tight">999</span>
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4">
        {{-- Mobile: Community Alerts (3D Soft Collapsible Accordion) --}}
        <div class="lg:hidden mb-8" x-data="{ open: false }">
            <button @click="open = !open"
                class="w-full bg-white/80 backdrop-blur-md text-slate-800 font-bold p-4 rounded-2xl shadow-[0_8px_20px_rgba(0,0,0,0.06)] border border-slate-100 flex justify-between items-center transition-all active:scale-[0.99]">
                <span class="flex items-center gap-2 text-rose-600">
                    <span class="text-xl">🚨</span>
                    <span>Community Alerts – Missing Persons</span>
                </span>
                <svg :class="open ? 'rotate-180 text-rose-600' : 'text-slate-400'"
                    class="w-5 h-5 transition-transform duration-300" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="open" x-collapse>
                <div class="mt-3 p-2 bg-white/60 backdrop-blur-sm rounded-2xl border border-slate-100 shadow-inner">
                    <livewire:alert.community-alert-feed />
                </div>
            </div>
        </div>

        <div class="lg:grid lg:grid-cols-3 lg:gap-10">
            {{-- Main Content (Search + Results) --}}
            <div class="lg:col-span-2">
                {{-- Header with soft 3D typography --}}
                <div class="text-center mb-10">
                    <div class="inline-block mb-2">
                        <span
                            class="px-4 py-1.5 rounded-full text-xs font-bold tracking-widest uppercase bg-indigo-100/80 text-indigo-700 shadow-sm border border-indigo-200/50">
                            Community Outreach Platform
                        </span>
                    </div>
                    <h1 class="text-5xl font-black text-slate-900 tracking-tight mb-3 drop-shadow-sm">
                        Nafasi
                    </h1>
                    <p class="text-xl font-medium text-slate-500 max-w-md mx-auto">
                        Find the right help, right now.
                    </p>
                </div>

                @if (!$result)
                    {{-- Input Section (Neomorphic Floating Card) --}}
                    <div
                        class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-[0_20px_50px_rgba(8,112,184,0.07)] border border-slate-100/80 p-6 md:p-8 transition-all">
                        <div class="mb-6">
                            <label class="block text-sm font-bold uppercase tracking-wider text-slate-500 mb-3">
                                What do you need assistance with?
                            </label>
                            <div class="flex flex-col sm:flex-row gap-3">
                                <div class="relative flex-1">
                                    <input type="text" wire:model="situation" wire:keydown.enter="submit"
                                        placeholder="e.g., pharmacy near me, lab test, fire emergency..."
                                        class="w-full rounded-2xl border-2 border-slate-200/80 bg-slate-50/50 px-5 py-4 text-lg text-slate-800 placeholder-slate-400 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 shadow-inner transition-all outline-none"
                                        autofocus>
                                </div>
                                <button wire:click="submit"
                                    class="px-8 py-4 bg-gradient-to-r from-indigo-600 to-blue-600 text-white rounded-2xl hover:from-indigo-700 hover:to-blue-700 font-bold text-lg shadow-[0_10px_20px_rgba(79,70,229,0.3)] hover:shadow-[0_15px_25px_rgba(79,70,229,0.4)] active:scale-95 transition-all flex items-center justify-center gap-2">
                                    <span>Find Help</span>
                                    <span>➔</span>
                                </button>
                            </div>
                            @error('situation')
                                <p class="text-rose-600 text-sm font-medium mt-2 flex items-center gap-1">
                                    <span>⚠️</span> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Quick Select 3D Tactile Chips --}}
                        <div class="mb-6">
                            <span class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Quick
                                Categories</span>
                            <div class="flex flex-wrap gap-2.5">
                                <button wire:click="$set('situation', 'pharmacy near me')"
                                    class="px-4 py-2 bg-blue-50/80 text-blue-700 border border-blue-200/60 rounded-xl text-sm font-semibold hover:bg-blue-100 hover:border-blue-300 shadow-sm active:translate-y-0.5 transition-all flex items-center gap-1.5">
                                    💊 Pharmacy
                                </button>
                                <button wire:click="$set('situation', 'lab test')"
                                    class="px-4 py-2 bg-emerald-50/80 text-emerald-700 border border-emerald-200/60 rounded-xl text-sm font-semibold hover:bg-emerald-100 hover:border-emerald-300 shadow-sm active:translate-y-0.5 transition-all flex items-center gap-1.5">
                                    🧪 Lab Test
                                </button>
                                <button wire:click="$set('situation', 'dental clinic')"
                                    class="px-4 py-2 bg-purple-50/80 text-purple-700 border border-purple-200/60 rounded-xl text-sm font-semibold hover:bg-purple-100 hover:border-purple-300 shadow-sm active:translate-y-0.5 transition-all flex items-center gap-1.5">
                                    🦷 Dental
                                </button>
                                <button wire:click="$set('situation', 'maternity care')"
                                    class="px-4 py-2 bg-pink-50/80 text-pink-700 border border-pink-200/60 rounded-xl text-sm font-semibold hover:bg-pink-100 hover:border-pink-300 shadow-sm active:translate-y-0.5 transition-all flex items-center gap-1.5">
                                    🤰 Maternity
                                </button>
                                <button wire:click="$set('situation', 'hospital near me')"
                                    class="px-4 py-2 bg-slate-100 text-slate-700 border border-slate-200 rounded-xl text-sm font-semibold hover:bg-slate-200 shadow-sm active:translate-y-0.5 transition-all flex items-center gap-1.5">
                                    🏥 Hospital
                                </button>
                                <button wire:click.prevent="window.location='{{ route('emergency.dispatch') }}'"
                                    class="px-4 py-2 bg-rose-50 text-rose-700 border border-rose-200 rounded-xl text-sm font-bold hover:bg-rose-100 shadow-sm active:translate-y-0.5 transition-all flex items-center gap-1.5">
                                    🚨 Emergency Help
                                </button>
                                <a href="{{ url('/report/anonymous') }}"
                                    class="px-4 py-2 bg-amber-50 text-amber-800 border border-amber-200 rounded-xl text-sm font-semibold hover:bg-amber-100 shadow-sm active:translate-y-0.5 transition-all flex items-center gap-1.5">
                                    🛡️ Report Anonymously
                                </a>
                            </div>
                        </div>

                        {{-- Geolocation Toggle --}}
                        <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                            @if (!$locationGranted)
                                <button wire:click="requestLocation"
                                    class="group text-sm font-semibold text-indigo-600 hover:text-indigo-800 flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-indigo-50 transition-all">
                                    <span class="text-base group-hover:scale-125 transition-transform">📍</span>
                                    <span>Share my location for accurate results</span>
                                </button>
                            @else
                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-700 border border-emerald-200/60 rounded-xl text-sm font-semibold">
                                    <span>✓</span> Location enabled
                                </span>
                            @endif
                        </div>
                    </div>
                @else
                    {{-- Results Section --}}
                    <div class="space-y-6">
                        <button wire:click="resetSearch"
                            class="group inline-flex items-center gap-2 text-sm font-bold text-indigo-600 hover:text-indigo-800 bg-white/80 px-4 py-2 rounded-xl border border-slate-100 shadow-sm hover:shadow-md transition-all">
                            <span class="group-hover:-translate-x-1 transition-transform">←</span> Start New Search
                        </button>

                        @if ($result['type'] === 'crisis')
                            <div
                                class="bg-gradient-to-br from-purple-700 via-indigo-700 to-purple-800 text-white rounded-3xl p-8 text-center shadow-[0_20px_50px_rgba(126,34,206,0.25)] border-t border-white/20">
                                <div
                                    class="w-16 h-16 bg-white/10 rounded-2xl backdrop-blur-md flex items-center justify-center text-4xl mx-auto mb-4 shadow-inner">
                                    🤝
                                </div>
                                <h2 class="text-3xl font-black mb-3 tracking-tight">You Are Not Alone</h2>
                                <p class="text-purple-100 mb-6 max-w-lg mx-auto text-base leading-relaxed">
                                    {{ $result['message'] }}</p>
                                <div class="grid sm:grid-cols-2 gap-4 max-w-md mx-auto">
                                    <a href="tel:{{ $result['emergency_number'] }}"
                                        class="flex items-center justify-center gap-2 px-6 py-4 bg-white text-purple-800 rounded-2xl font-extrabold text-lg shadow-lg hover:bg-purple-50 active:scale-95 transition-all">
                                        <span>📞 Call</span>
                                        <span>{{ $result['emergency_number'] }}</span>
                                    </a>
                                    <a href="{{ route('crisis.chat') }}"
                                        class="flex items-center justify-center gap-2 px-6 py-4 bg-purple-600/60 text-white rounded-2xl font-bold text-lg hover:bg-purple-600 border border-purple-400/40 backdrop-blur-md shadow-lg active:scale-95 transition-all">
                                        <span>💬 Chat Now</span>
                                    </a>
                                </div>
                            </div>
                        @elseif($result['type'] === 'emergency')
                            <div
                                class="bg-gradient-to-br from-rose-600 via-red-600 to-rose-700 text-white rounded-3xl p-8 text-center shadow-[0_20px_50px_rgba(225,29,72,0.3)] border-t border-white/20">
                                <div
                                    class="w-16 h-16 bg-white/10 rounded-2xl backdrop-blur-md flex items-center justify-center text-4xl mx-auto mb-4 shadow-inner animate-bounce">
                                    🚨
                                </div>
                                <h2 class="text-3xl font-black mb-2 tracking-tight">Emergency Assistance Needed</h2>
                                <p class="text-rose-100 mb-6 max-w-md mx-auto">{{ $result['message'] }}</p>
                                <a href="tel:{{ $result['emergency_number'] }}"
                                    class="inline-flex items-center gap-2 px-8 py-4 bg-white text-rose-700 rounded-2xl font-black text-xl shadow-xl hover:bg-rose-50 active:scale-95 transition-all">
                                    <span>📞 Call Now:</span>
                                    <span>{{ $result['emergency_number'] }}</span>
                                </a>
                            </div>
                        @elseif($result['type'] === 'dispatch' || $result['type'] === 'dispatch_created')
                            <div
                                class="bg-gradient-to-br from-emerald-600 to-teal-700 text-white rounded-3xl p-8 text-center shadow-[0_20px_50px_rgba(13,148,136,0.25)] border-t border-white/20">
                                <div
                                    class="w-16 h-16 bg-white/10 rounded-2xl backdrop-blur-md flex items-center justify-center text-4xl mx-auto mb-4 shadow-inner">
                                    🚑
                                </div>
                                <h2 class="text-3xl font-black mb-2 tracking-tight">Help Is On The Way</h2>
                                <p class="text-emerald-100 text-lg font-medium">{{ $result['message'] }}</p>
                                <p
                                    class="text-emerald-200 text-sm mt-3 bg-black/10 inline-block px-4 py-1.5 rounded-full backdrop-blur-sm">
                                    A response coordinator will contact you shortly.</p>
                            </div>
                        @elseif($result['type'] === 'facilities')
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-xl font-bold text-slate-800">
                                    Nearby Facilities <span
                                        class="ml-2 px-2.5 py-0.5 bg-indigo-100 text-indigo-700 text-sm rounded-full">{{ count($result['facilities']) }}</span>
                                </h2>
                            </div>
                            <div class="space-y-4">
                                @foreach ($result['facilities'] as $facility)
                                    <div
                                        class="bg-white/90 backdrop-blur-md rounded-2xl shadow-[0_10px_30px_rgba(0,0,0,0.04)] border border-slate-100 p-5 hover:shadow-[0_15px_35px_rgba(0,0,0,0.07)] transition-all">
                                        <div class="flex justify-between items-start gap-4">
                                            <div>
                                                <h3 class="font-bold text-lg text-slate-900">{{ $facility['name'] }}
                                                </h3>
                                                <p class="text-sm text-slate-500 mt-0.5">{{ $facility['address'] }}
                                                </p>
                                                @if (isset($facility['distance']))
                                                    <span
                                                        class="inline-flex items-center gap-1 text-xs font-semibold text-slate-400 mt-1">
                                                        <span>📍</span> {{ round($facility['distance'], 1) }} km away
                                                    </span>
                                                @endif
                                            </div>
                                            <div>
                                                @if ($facility['congestion_status'])
                                                    <span
                                                        class="px-3 py-1 text-xs font-bold rounded-full border shadow-sm
                                                        {{ $facility['congestion_status'] === 'low' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : '' }}
                                                        {{ $facility['congestion_status'] === 'moderate' ? 'bg-amber-50 text-amber-700 border-amber-200' : '' }}
                                                        {{ $facility['congestion_status'] === 'high' ? 'bg-rose-50 text-rose-700 border-rose-200' : '' }}">
                                                        ● {{ ucfirst($facility['congestion_status']) }} Activity
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        @if (!empty($facility['capabilities']))
                                            <div class="flex flex-wrap gap-1.5 mt-3">
                                                @foreach (array_slice($facility['capabilities'], 0, 5) as $cap)
                                                    <span
                                                        class="px-2.5 py-1 bg-slate-100 text-slate-600 text-xs font-medium rounded-lg">{{ $cap }}</span>
                                                @endforeach
                                            </div>
                                        @endif

                                        <div
                                            class="mt-4 pt-3 border-t border-slate-100 flex flex-wrap gap-3 items-center justify-between">
                                            <div class="flex gap-2">
                                                @if ($facility['phone'])
                                                    <a href="tel:{{ $facility['phone'] }}"
                                                        wire:click.prevent="trackAction('called', {{ $facility['id'] }})"
                                                        onclick="setTimeout(() => window.location.href='tel:{{ $facility['phone'] }}', 100)"
                                                        class="px-3.5 py-2 bg-indigo-50 text-indigo-700 font-bold text-xs rounded-xl hover:bg-indigo-100 transition-all flex items-center gap-1">
                                                        📞 Call
                                                    </a>
                                                @endif
                                                @if (isset($facility['distance']))
                                                    <a href="https://www.google.com/maps/dir/?api=1&destination={{ urlencode($facility['name']) }}"
                                                        target="_blank"
                                                        wire:click.prevent="trackAction('directions', {{ $facility['id'] }})"
                                                        onclick="setTimeout(() => window.open(this.href,'_blank'), 100)"
                                                        class="px-3.5 py-2 bg-slate-100 text-slate-700 font-bold text-xs rounded-xl hover:bg-slate-200 transition-all flex items-center gap-1">
                                                        🗺️ Directions
                                                    </a>
                                                @endif
                                            </div>
                                            <livewire:booking-form :facilityId="$facility['id']" :key="$facility['id']" />
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                {{-- PWA Install Prompt Card --}}
                <div id="install-prompt"
                    class="hidden fixed bottom-6 left-4 right-4 md:left-auto md:right-6 bg-slate-900/95 backdrop-blur-xl text-white rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] p-4 z-50 border border-slate-700 max-w-md transition-all">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center font-black text-xl shadow-md">
                                N</div>
                            <div>
                                <p class="font-bold text-sm">Install Nafasi App</p>
                                <p class="text-xs text-slate-400">Add to home screen for fast access</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button onclick="document.getElementById('install-prompt').classList.add('hidden')"
                                class="px-3 py-1.5 text-slate-400 hover:text-white text-xs font-semibold">Later</button>
                            <button id="install-button"
                                class="px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white rounded-xl text-xs font-bold shadow-lg transition-all active:scale-95">
                                Install
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Subtle Privacy Note --}}
                <div class="text-center mt-12">
                    <span
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-white/60 backdrop-blur-md rounded-full text-xs font-medium text-slate-400 border border-slate-100 shadow-sm">
                        🔒 We never store your medical information. You remain completely anonymous.
                    </span>
                </div>
            </div>

            {{-- Desktop: Right Sidebar for Community Alerts (3D Card Container) --}}
            <div class="hidden lg:block">
                <div
                    class="sticky top-6 bg-white/80 backdrop-blur-xl rounded-3xl shadow-[0_15px_35px_rgba(0,0,0,0.05)] border border-slate-100/80 p-5">
                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                        <h3 class="font-bold text-slate-800 flex items-center gap-2">
                            <span class="text-lg">🚨</span>
                            <span>Community Alerts</span>
                        </h3>
                        <span class="flex h-2.5 w-2.5 relative">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-rose-500"></span>
                        </span>
                    </div>
                    <livewire:alert.community-alert-feed />
                </div>
            </div>
        </div>
    </div>
</div>
