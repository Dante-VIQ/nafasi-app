<div class="mt-8" x-data="{ showModal: false, modalImage: '' }">
    @if (count($alerts) > 0)
        <h2 class="text-xl font-bold text-red-600 mb-4">🚨 Community Alerts – Missing Persons</h2>
        <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
            @foreach ($alerts as $alert)
                <div class="bg-white rounded-xl shadow-lg p-5 border-l-4 border-red-500">
                    <div class="flex items-start space-x-4">
                        {{-- Missing person photo (clickable) --}}
                        @if ($alert['photo_path'])
                            <img src="{{ asset($alert['photo_path']) }}"
                                class="h-20 w-20 object-cover rounded-lg cursor-pointer hover:opacity-80 transition"
                                @click="modalImage = '{{ asset($alert['photo_path']) }}'; showModal = true">
                        @endif
                        <div class="flex-1">
                            <h3 class="font-bold text-lg">{{ $alert['name'] }}</h3>
                            <p class="text-sm text-gray-600">{{ $alert['description'] }}</p>
                            @if ($alert['last_seen_location'])
                                <p class="text-sm text-gray-500 mt-1">📍 Last seen: {{ $alert['last_seen_location'] }}
                                </p>
                            @endif
                            {{-- Suspect photo / description (clickable) --}}
                            @if ($alert['suspect_photo_path'] || $alert['suspect_description'])
                                <div class="mt-2 bg-red-50 p-2 rounded-lg">
                                    <p class="text-xs font-semibold text-red-700">⚠️ WANTED</p>
                                    @if ($alert['suspect_photo_path'])
                                        <img src="{{ asset($alert['suspect_photo_path']) }}"
                                            class="h-16 w-16 object-cover rounded mt-1 cursor-pointer hover:opacity-80 transition"
                                            @click="modalImage = '{{ asset($alert['suspect_photo_path']) }}'; showModal = true">
                                    @endif
                                    @if ($alert['suspect_description'])
                                        <p class="text-xs text-red-600 mt-1">{{ $alert['suspect_description'] }}</p>
                                    @endif
                                </div>
                            @endif
                            <div class="flex flex-wrap gap-2 mt-3">
                                <button wire:click="reportSighting({{ $alert['id'] }})"
                                    class="mt-3 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 text-sm font-medium">
                                    👁️ I've Seen This Person
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
                                    class="mt-2 ml-2 inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition">
                                    📤 Share
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if (session()->has('sighting_message'))
        <div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('sighting_message') }}
        </div>
    @endif

    {{-- Full‑screen image modal --}}
    <div x-show="showModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm"
        @click.self="showModal = false">

        <div class="relative max-w-4xl mx-4">
            <button @click="showModal = false"
                class="absolute -top-12 right-0 text-white text-2xl hover:text-gray-300 transition">
                ✕
            </button>
            <img :src="modalImage" class="max-h-[90vh] max-w-full rounded-lg shadow-2xl" alt="Full size photo">
        </div>
    </div>
</div>
