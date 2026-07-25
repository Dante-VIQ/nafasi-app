{{-- resources/views/livewire/tenant/tenant-dashboard.blade.php --}}
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 py-8">
        
        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">County Dashboard</h1>
                <p class="text-gray-600">{{ tenant('name') ?? 'Your Organization' }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ url('/tenant/facilities') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                    Manage Facilities
                </a>
                <a href="{{ url('/tenant/users') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-sm">
                    Manage Users
                </a>
            </div>
        </div>

        {{-- Quick Stats --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-sm text-gray-500">Total Facilities</p>
                <p class="text-3xl font-bold text-gray-900">{{ $totalFacilities }}</p>
                <p class="text-xs {{ $pendingVerifications > 0 ? 'text-yellow-600' : 'text-green-600' }}">
                    {{ $pendingVerifications }} pending verification
                </p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-sm text-gray-500">Active Facilities</p>
                <p class="text-3xl font-bold text-green-600">{{ $activeFacilities }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-sm text-gray-500">Users</p>
                <p class="text-3xl font-bold text-gray-900">{{ $totalUsers }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-sm text-gray-500">Today</p>
                <div class="space-y-1 mt-2">
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Appointments</span>
                        <span class="font-medium">{{ $appointmentsToday }}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Emergencies</span>
                        <span class="font-medium text-red-600">{{ $emergencyDispatchesToday }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Facilities Overview --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            
            {{-- Facilities by Type --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Facilities by Type</h2>
                @if(count($facilitiesByType) > 0)
                    <div class="space-y-3">
                        @php $maxType = max($facilitiesByType) ?: 1; @endphp
                        @foreach($facilitiesByType as $type => $count)
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">{{ ucfirst(str_replace('_', ' ', $type)) }}</span>
                                    <span class="font-medium">{{ $count }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full" 
                                         style="width: {{ ($count / $maxType) * 100 }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-sm">No facilities registered yet.</p>
                @endif
            </div>

            {{-- Congestion Overview --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Current Congestion</h2>
                @if(count($congestionSummary) > 0)
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-green-50 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-green-700">{{ $congestionSummary['low'] ?? 0 }}</p>
                            <p class="text-xs text-green-600">Low Wait</p>
                        </div>
                        <div class="bg-yellow-50 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-yellow-700">{{ $congestionSummary['moderate'] ?? 0 }}</p>
                            <p class="text-xs text-yellow-600">Moderate</p>
                        </div>
                        <div class="bg-orange-50 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-orange-700">{{ $congestionSummary['high'] ?? 0 }}</p>
                            <p class="text-xs text-orange-600">Busy</p>
                        </div>
                        <div class="bg-red-50 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-red-700">{{ $congestionSummary['at_capacity'] ?? 0 }}</p>
                            <p class="text-xs text-red-600">Full</p>
                        </div>
                    </div>
                @else
                    <p class="text-gray-500 text-sm">No congestion data yet.</p>
                @endif
            </div>
        </div>

        {{-- Pending Referrals --}}
        @if($pendingReferrals > 0)
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-8 rounded-r-lg">
                <p class="text-yellow-700">
                    <strong>{{ $pendingReferrals }} pending referrals</strong> need attention.
                    <a href="{{ route('facility.referrals') }}" class="underline font-medium">Review now →</a>
                </p>
            </div>
        @endif

        {{-- Recent Activity --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h2>
            @if(count($recentActivity) > 0)
                <div class="space-y-4">
                    @foreach($recentActivity as $activity)
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 mt-1">
                                @if($activity['type'] === 'facility')
                                    <span class="text-xl">🏥</span>
                                @elseif($activity['type'] === 'booking')
                                    <span class="text-xl">📅</span>
                                @else
                                    <span class="text-xl">📋</span>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm text-gray-900">{{ $activity['message'] }}</p>
                                <p class="text-xs text-gray-400">{{ $activity['time'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm">No recent activity.</p>
            @endif
        </div>
    </div>
</div>