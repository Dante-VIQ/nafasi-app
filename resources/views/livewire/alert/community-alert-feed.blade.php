<div class="mt-2" x-data="{ showModal: false, modalImage: '' }">
    @if (count($alerts) > 0)
        {{-- Section Title --}}
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-black text-slate-800 flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-rose-100 text-rose-600 text-base shadow-inner">🚨</span>
                <span>Community Alerts</span>
            </h2>
            <span class="px-2.5 py-1 bg-rose-50 border border-rose-200/60 text-rose-700 text-xs font-extrabold rounded-full shadow-sm">
                {{ count($alerts) }} Active
            </span>
        </div>

        <div class="grid grid-cols-1 gap-5">
            @foreach ($alerts as $alert)
                <div class="group bg-white/90 backdrop-blur-xl rounded-3xl shadow-[0_12px_30px_rgba(225,29,72,0.06)] hover:shadow-[0_18px_40px_rgba(225,29,72,0.12)] border border-rose-100/80 p-5 transition-all duration-300 relative overflow-hidden">
                    
                    {{-- Soft 3D Accent Line --}}
                    <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-rose-500 via-pink-500 to-rose-600"></div>

                    <div class="flex flex-col sm:flex-row items-start gap-4">
                        
                        {{-- Missing Person Photo (Soft 3D Frame & Hover Effects) --}}
                        @if ($alert['photo_path'])
                            <div class="relative shrink-0 mx-auto sm:mx-0">
                                <div class="w-24 h-24 rounded-2xl overflow-hidden p-1 bg-gradient-to-br from-rose-100 via-white to-slate-100 shadow-[0_8px_20px_rgba(0,0,0,0.08)] border border-rose-200/50 group-hover:scale-105 transition-transform duration-300 cursor-pointer"
                                     @click="modalImage = '{{ asset($alert['photo_path']) }}'; showModal = true">
                                    <img src="{{ asset($alert['photo_path']) }}"
                                        class="w-full h-full object-cover rounded-xl transition-opacity hover:opacity-90"
                                        alt="{{ $alert['name'] }}">
                                </div>
                                <span class="absolute -bottom-1 -right-1 bg-rose-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-md border border-white">
                                    MISSING
                                </span>
                            </div>
                        @endif

                        <div class="flex-1 w-full text-left">
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="font-extrabold text-lg text-slate-900 tracking-tight">{{ $alert['name'] }}</h3>
                            </div>

                            <p class="text-sm text-slate-600 mt-1 leading-relaxed">{{ $alert['description'] }}</p>

                            @if ($alert['last_seen_location'])
                                <div class="inline-flex items-center gap-1.5 mt-2.5 px-3 py-1 bg-slate-100/80 text-slate-700 rounded-xl text-xs font-semibold border border-slate-200/50">
                                    <span class="text-rose-500 text-sm">📍</span> 
                                    <span>Last seen: <strong>{{ $alert['last_seen_location'] }}</strong></span>
                                </div>
                            @endif

                            {{-- Suspect / Wanted Warning Box --}}
                            @if ($alert['suspect_photo_path'] || $alert['suspect_description'])
                                <div class="mt-4 p-3.5 bg-gradient-to-br from-amber-50 to-rose-50/60 rounded-2xl border border-rose-200/60 shadow-sm">
                                    <div class="flex items-center gap-1.5 text-xs font-black uppercase tracking-wider text-rose-700 mb-2">
                                        <span>⚠️</span>
                                        <span>Wanted / Suspect Info</span>
                                    </div>

                                    <div class="flex items-start gap-3">
                                        @if ($alert['suspect_photo_path'])
                                            <div class="w-14 h-14 rounded-xl overflow-hidden p-0.5 bg-white shadow-sm border border-rose-200 shrink-0 cursor-pointer hover:scale-105 transition-transform"
                                                 @click="modalImage = '{{ asset($alert['suspect_photo_path']) }}'; showModal = true">
                                                <img src="{{ asset($alert['suspect_photo_path']) }}"
                                                    class="w-full h-full object-cover rounded-lg"
                                                    alt="Suspect photo">
                                            </div>
                                        @endif

                                        @if ($alert['suspect_description'])
                                            <p class="text-xs text-rose-900 leading-relaxed font-medium">
                                                {{ $alert['suspect_description'] }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            {{-- Action Buttons --}}
                            <div class="flex flex-wrap items-center gap-2.5 mt-4 pt-3 border-t border-slate-100">
                                <button wire:click="reportSighting({{ $alert['id'] }})"
                                    class="flex-1 sm:flex-initial px-4 py-2.5 bg-gradient-to-r from-rose-600 to-red-600 text-white rounded-xl hover:from-rose-700 hover:to-red-700 text-xs font-extrabold shadow-[0_8px_16px_rgba(225,29,72,0.25)] hover:shadow-[0_12px_20px_rgba(225,29,72,0.35)] active:scale-95 transition-all flex items-center justify-center gap-1.5">
                                    <span>👁️</span>
                                    <span>I've Seen This Person</span>
                                </button>

                                <button x-data="{
                                    share() {
                                        const text = `🚨 Missing Person Alert: {{ addslashes($alert['name']) }}\n\n{{ addslashes($alert['description']) }}\n\nLast seen: {{ addslashes($alert['last_seen_location'] ?? 'Unknown') }}\n\nHelp find them! #Nafasi`;
                                        if (navigator.share) {
                                            navigator.share({ title: 'Missing Person Alert', text: text });
                                        } else {
                                            navigator.clipboard.writeText(text).then(() => {
                                                alert('Alert details copied to clipboard. Share them wherever you can.');
                                            });
                                        }
                                    }
                                }" @click="share()"
                                    class="px-4 py-2.5 bg-gradient-to-r from-slate-800 to-slate-900 text-white rounded-xl hover:from-slate-700 hover:to-slate-800 text-xs font-extrabold shadow-md active:scale-95 transition-all flex items-center justify-center gap-1.5">
                                    <span>📤</span>
                                    <span>Share</span>
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Success Notification Banner --}}
    @if (session()->has('sighting_message'))
        <div class="mt-4 p-4 bg-emerald-500/10 backdrop-blur-md border border-emerald-300 text-emerald-800 rounded-2xl shadow-lg flex items-center gap-3">
            <span class="text-xl">✅</span>
            <p class="text-xs font-bold">{{ session('sighting_message') }}</p>
        </div>
    @endif

    {{-- Glassmorphic Full-Screen Image Modal --}}
    <div x-show="showModal" 
        x-transition:enter="transition ease-out duration-200" 
        x-transition:enter-start="opacity-0 scale-95" 
        x-transition:enter-end="opacity-100 scale-100" 
        x-transition:leave="transition ease-in duration-150" 
        x-transition:leave-start="opacity-100 scale-100" 
        x-transition:leave-end="opacity-0 scale-95"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-md p-4"
        @click.self="showModal = false"
        x-cloak>

        <div class="relative max-w-3xl w-full mx-auto bg-slate-900/90 rounded-3xl p-2 shadow-[0_25px_60px_rgba(0,0,0,0.5)] border border-white/20 overflow-hidden">
            <button @click="showModal = false"
                class="absolute top-4 right-4 w-10 h-10 bg-black/50 hover:bg-black/80 text-white font-bold rounded-full backdrop-blur-md flex items-center justify-center shadow-lg transition-transform hover:scale-110 active:scale-95 z-10">
                ✕
            </button>
            <img :src="modalImage" class="max-h-[85vh] w-full object-contain rounded-2xl" alt="Full size photo">
        </div>
    </div>
</div>