{{-- resources/views/livewire/facility/congestion-button.blade.php --}}
<div class="min-h-screen bg-gray-50">
    <div class="max-w-2xl mx-auto px-4 py-8">
        
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Update Congestion</h1>
                <p class="text-gray-600">{{ $facility->name }}</p>
            </div>
            <a href="{{ route('facility.dashboard') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm">
                ← Dashboard
            </a>
        </div>

        @if (session()->has('message'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r-lg">
                <p class="text-green-700">{{ session('message') }}</p>
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-lg p-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <p class="text-sm text-gray-500">Current Status</p>
                    <div class="flex items-center space-x-2 mt-1">
                        <span class="px-3 py-1 rounded-full text-sm font-medium
                            {{ $currentStatus === 'low' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $currentStatus === 'moderate' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $currentStatus === 'high' ? 'bg-orange-100 text-orange-800' : '' }}
                            {{ $currentStatus === 'at_capacity' ? 'bg-red-100 text-red-800' : '' }}
                            {{ $currentStatus === 'unknown' ? 'bg-gray-100 text-gray-800' : '' }}">
                            {{ ucfirst(str_replace('_', ' ', $currentStatus)) }}
                        </span>
                    </div>
                </div>
                <div class="text-right text-sm text-gray-500">
                    <p>Last updated: {{ $lastUpdated }}</p>
                    <p>{{ $isOpen ? '🟢 Open' : '🔴 Closed' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <button wire:click="updateStatus('low')"
                    class="p-6 rounded-xl text-center transition-all border-2
                        {{ $currentStatus === 'low' ? 'border-green-500 bg-green-50 ring-2 ring-green-200' : 'border-gray-200 hover:border-green-300 hover:bg-green-50' }}">
                    <div class="text-3xl mb-2">😊</div>
                    <div class="font-bold text-lg text-gray-900">Low</div>
                    <div class="text-sm text-gray-500">Minimal wait time</div>
                </button>

                <button wire:click="updateStatus('moderate')"
                    class="p-6 rounded-xl text-center transition-all border-2
                        {{ $currentStatus === 'moderate' ? 'border-yellow-500 bg-yellow-50 ring-2 ring-yellow-200' : 'border-gray-200 hover:border-yellow-300 hover:bg-yellow-50' }}">
                    <div class="text-3xl mb-2">😐</div>
                    <div class="font-bold text-lg text-gray-900">Moderate</div>
                    <div class="text-sm text-gray-500">Some wait expected</div>
                </button>

                <button wire:click="updateStatus('high')"
                    class="p-6 rounded-xl text-center transition-all border-2
                        {{ $currentStatus === 'high' ? 'border-orange-500 bg-orange-50 ring-2 ring-orange-200' : 'border-gray-200 hover:border-orange-300 hover:bg-orange-50' }}">
                    <div class="text-3xl mb-2">😟</div>
                    <div class="font-bold text-lg text-gray-900">Busy</div>
                    <div class="text-sm text-gray-500">Long wait times</div>
                </button>

                <button wire:click="updateStatus('at_capacity')"
                    class="p-6 rounded-xl text-center transition-all border-2
                        {{ $currentStatus === 'at_capacity' ? 'border-red-500 bg-red-50 ring-2 ring-red-200' : 'border-gray-200 hover:border-red-300 hover:bg-red-50' }}">
                    <div class="text-3xl mb-2">🚫</div>
                    <div class="font-bold text-lg text-gray-900">Full</div>
                    <div class="text-sm text-gray-500">No capacity available</div>
                </button>
            </div>

            <div class="mt-6 p-4 bg-blue-50 rounded-lg text-sm text-blue-700">
                <strong>💡 Tip:</strong> Update this regularly so patients can see accurate wait times.
                Facilities that don't update within 3 hours are automatically deprioritized in search results.
            </div>
        </div>
    </div>
</div>