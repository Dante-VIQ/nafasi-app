{{-- resources/views/livewire/facility/booking-manager.blade.php --}}
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 py-8">
        
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Booking Manager</h1>
                <p class="text-gray-600">{{ $facility->name ?? 'Your Facility' }}</p>
            </div>
            <a href="{{ url('/facility/dashboard') }}" 
               class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm">
                ← Back to Dashboard
            </a>
        </div>

        @if (session()->has('message'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r-lg">
                <p class="text-green-700">{{ session('message') }}</p>
            </div>
        @endif

        {{-- Quick Stats --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-4">
                <p class="text-xs text-gray-500">Today's Bookings</p>
                <p class="text-2xl font-bold text-gray-900">{{ $todayCount }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-4">
                <p class="text-xs text-gray-500">Pending</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $pendingCount }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-4">
                <p class="text-xs text-gray-500">Confirmed</p>
                <p class="text-2xl font-bold text-blue-600">{{ $confirmedCount }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-4">
                <p class="text-xs text-gray-500">Completed Today</p>
                <p class="text-2xl font-bold text-green-600">{{ $completedCount }}</p>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
            <div class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[200px]">
                    <input type="text" wire:model.live.debounce.300ms="search" 
                           placeholder="Search patient name, phone, reason..."
                           class="w-full rounded-lg border-gray-300 text-sm px-4 py-2">
                </div>
                <div>
                    <select wire:model.live="filterStatus" class="rounded-lg border-gray-300 text-sm px-4 py-2">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="arrived">Arrived</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div>
                    <input type="date" wire:model.live="filterDate" 
                           class="rounded-lg border-gray-300 text-sm px-4 py-2">
                </div>
            </div>
        </div>

        {{-- Bookings Table --}}
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th wire:click="sortBy('patient_name')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer">
                            Patient {{ $sortField === 'patient_name' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                        <th wire:click="sortBy('scheduled_at')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer">
                            Date & Time {{ $sortField === 'scheduled_at' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reason</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($appointments as $appointment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $appointment->patient_name }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                @if($appointment->patient_phone)
                                    <a href="tel:{{ $appointment->patient_phone }}" class="text-blue-600 hover:text-blue-800">
                                        📞 {{ $appointment->patient_phone }}
                                    </a>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $appointment->scheduled_at->format('d M Y') }}
                                <br>
                                <span class="text-xs text-gray-400">{{ $appointment->scheduled_at->format('H:i') }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $appointment->reason ?? 'General' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 text-xs rounded-full
                                    {{ $appointment->status === 'confirmed' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $appointment->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                    {{ $appointment->status === 'arrived' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $appointment->status === 'completed' ? 'bg-purple-100 text-purple-700' : '' }}
                                    {{ $appointment->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end space-x-2">
                                    @if($appointment->status === 'pending')
                                        <button wire:click="confirmBooking({{ $appointment->id }})" 
                                                class="text-xs text-green-600 hover:text-green-800 bg-green-50 px-2 py-1 rounded">
                                            ✓ Confirm
                                        </button>
                                    @endif
                                    @if(in_array($appointment->status, ['confirmed']))
                                        <button wire:click="markArrived({{ $appointment->id }})" 
                                                class="text-xs text-blue-600 hover:text-blue-800 bg-blue-50 px-2 py-1 rounded">
                                            🚶 Arrived
                                        </button>
                                    @endif
                                    @if(in_array($appointment->status, ['confirmed', 'arrived']))
                                        <button wire:click="markCompleted({{ $appointment->id }})" 
                                                class="text-xs text-purple-600 hover:text-purple-800 bg-purple-50 px-2 py-1 rounded">
                                            ✅ Complete
                                        </button>
                                    @endif
                                    @if(in_array($appointment->status, ['pending', 'confirmed']))
                                        <button wire:click="cancelBooking({{ $appointment->id }})" 
                                                wire:confirm="Cancel this booking?"
                                                class="text-xs text-red-600 hover:text-red-800 bg-red-50 px-2 py-1 rounded">
                                            ✕ Cancel
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                No bookings found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $appointments->links() }}
        </div>
    </div>
</div>