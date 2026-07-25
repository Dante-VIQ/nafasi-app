{{-- resources/views/livewire/facility/dashboard.blade.php --}}
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">{{ $facility->name }}</h1>
            <p class="text-gray-600">
                @if ($isOpen)
                    <span class="text-green-600">● Open</span>
                @else
                    <span class="text-red-600">● Closed</span>
                @endif
                <span class="ml-2">Level {{ $facility->health_system_level ?? '—' }}</span>
                <span class="ml-2">|</span>
                <span class="ml-2">{{ ucfirst(str_replace('_', ' ', $facility->facility_type)) }}</span>
            </p>
        </div>

        @if (session()->has('message'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r-lg">
                <p class="text-green-700">{{ session('message') }}</p>
            </div>
        @endif

        {{-- Quick Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <p class="text-sm text-gray-500">Today's Patients</p>
                <p class="text-3xl font-bold text-gray-900">{{ $totalPatientsToday }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <p class="text-sm text-gray-500">Pending Referrals</p>
                <p class="text-3xl font-bold text-gray-900">{{ $pendingReferrals }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <p class="text-sm text-gray-500">Active Bookings</p>
                <p class="text-3xl font-bold text-gray-900">{{ $activeBookings }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <p class="text-sm text-gray-500">Registration Status</p>
                <p class="text-lg font-semibold">
                    @if ($facility->registration_status === 'approved')
                        <span class="text-green-600">✓ Approved</span>
                    @elseif ($facility->registration_status === 'submitted')
                        <span class="text-yellow-600">⏳ Pending Verification</span>
                    @else
                        <span class="text-gray-600">{{ ucfirst($facility->registration_status) }}</span>
                    @endif
                </p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Upcoming Appointments</h2>
            @if (count($upcomingBookings) > 0)
                <div class="space-y-3">
                    @foreach ($upcomingBookings as $booking)
                        <div class="flex justify-between items-center border-b pb-2">
                            <div>
                                <p class="font-medium text-gray-900">{{ $booking['patient_name'] }}</p>
                                <p class="text-sm text-gray-600">{{ $booking['reason'] ?? 'No reason given' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($booking['scheduled_at'])->format('d M, H:i') }}
                                </p>
                                <span
                                    class="px-2 py-0.5 text-xs rounded-full
                                            {{ $booking['status'] === 'confirmed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                    {{ ucfirst($booking['status']) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm">No upcoming appointments.</p>
            @endif
        </div>
        {{-- Congestion Control --}}
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Current Congestion</h2>
                    <p class="text-sm text-gray-500">Last updated: {{ $lastUpdated }}</p>
                </div>
                <div>
                    @php
                        $statusColors = [
                            'low' => 'bg-green-100 text-green-800',
                            'moderate' => 'bg-yellow-100 text-yellow-800',
                            'high' => 'bg-orange-100 text-orange-800',
                            'at_capacity' => 'bg-red-100 text-red-800',
                            'unknown' => 'bg-gray-100 text-gray-800',
                        ];
                    @endphp
                    <span class="px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$congestionStatus] }}">
                        {{ ucfirst(str_replace('_', ' ', $congestionStatus)) }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <button wire:click="updateCongestion('low')"
                    class="p-4 rounded-xl text-center transition-all border-2
                        {{ $congestionStatus === 'low'
                            ? 'border-green-500 bg-green-50 ring-2 ring-green-200'
                            : 'border-gray-200 hover:border-green-300 hover:bg-green-50' }}">
                    <div class="text-2xl mb-1">😊</div>
                    <div class="font-medium text-sm">Low</div>
                    <div class="text-xs text-gray-500">Minimal wait</div>
                </button>

                <button wire:click="updateCongestion('moderate')"
                    class="p-4 rounded-xl text-center transition-all border-2
                        {{ $congestionStatus === 'moderate'
                            ? 'border-yellow-500 bg-yellow-50 ring-2 ring-yellow-200'
                            : 'border-gray-200 hover:border-yellow-300 hover:bg-yellow-50' }}">
                    <div class="text-2xl mb-1">😐</div>
                    <div class="font-medium text-sm">Moderate</div>
                    <div class="text-xs text-gray-500">Some wait</div>
                </button>

                <button wire:click="updateCongestion('high')"
                    class="p-4 rounded-xl text-center transition-all border-2
                        {{ $congestionStatus === 'high'
                            ? 'border-orange-500 bg-orange-50 ring-2 ring-orange-200'
                            : 'border-gray-200 hover:border-orange-300 hover:bg-orange-50' }}">
                    <div class="text-2xl mb-1">😟</div>
                    <div class="font-medium text-sm">Busy</div>
                    <div class="text-xs text-gray-500">Long wait</div>
                </button>

                <button wire:click="updateCongestion('at_capacity')"
                    class="p-4 rounded-xl text-center transition-all border-2
                        {{ $congestionStatus === 'at_capacity'
                            ? 'border-red-500 bg-red-50 ring-2 ring-red-200'
                            : 'border-gray-200 hover:border-red-300 hover:bg-red-50' }}">
                    <div class="text-2xl mb-1">🚫</div>
                    <div class="font-medium text-sm">Full</div>
                    <div class="text-xs text-gray-500">No capacity</div>
                </button>
            </div>
        </div>

        {{-- Congestion History --}}
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Congestion History (24h)</h2>
            @if (count($congestionHistory) > 0)
                <div class="flex items-end space-x-1 h-32">
                    @foreach ($congestionHistory as $log)
                        @php
                            $height = match ($log['status']) {
                                'low' => 'h-8',
                                'moderate' => 'h-16',
                                'high' => 'h-24',
                                'at_capacity' => 'h-32',
                                default => 'h-4',
                            };
                            $color = match ($log['status']) {
                                'low' => 'bg-green-400',
                                'moderate' => 'bg-yellow-400',
                                'high' => 'bg-orange-400',
                                'at_capacity' => 'bg-red-500',
                                default => 'bg-gray-300',
                            };
                        @endphp
                        <div class="flex-1 flex flex-col items-center"
                            title="{{ \Carbon\Carbon::parse($log['created_at'])->format('H:i') }} — {{ ucfirst($log['status']) }}">
                            <div class="w-full {{ $height }} {{ $color }} rounded-t"></div>
                            <span class="text-xs text-gray-400 mt-1">
                                {{ \Carbon\Carbon::parse($log['created_at'])->format('H:i') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm">No congestion data yet. Start updating your status.</p>
            @endif
        </div>

        {{-- Quick Links --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ url('/facility/profile/edit') }}"
                class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow">
                <div class="text-2xl mb-2">📋</div>
                <h3 class="font-semibold text-gray-900">Edit Profile</h3>
                <p class="text-sm text-gray-500">Update capabilities, keywords, hours</p>
            </a>
            <a href="{{ url('/facility/patients') }}"
                class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow">
                <div class="text-2xl mb-2">👥</div>
                <h3 class="font-semibold text-gray-900">Patients</h3>
                <p class="text-sm text-gray-500">View and manage patient records</p>
            </a>
            <a href="{{ url('/facility/referrals') }}"
                class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow">
                <div class="text-2xl mb-2">🔄</div>
                <h3 class="font-semibold text-gray-900">Referrals</h3>
                <p class="text-sm text-gray-500">Manage incoming and outgoing referrals</p>
            </a>
            <a href="{{ url('/facility/bookings') }}"
                class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow">
                <div class="text-2xl mb-2">📅</div>
                <h3 class="font-semibold text-gray-900">Bookings</h3>
                <p class="text-sm text-gray-500">Manage appointments and schedules</p>
            </a>

            <a href="{{ url('/facility/staff') }}"
                class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow">
                <div class="text-2xl mb-2">👥</div>
                <h3 class="font-semibold text-gray-900">Staff</h3>
                <p class="text-sm text-gray-500">Manage facility staff</p>
            </a>
        </div>

    </div>
</div>
