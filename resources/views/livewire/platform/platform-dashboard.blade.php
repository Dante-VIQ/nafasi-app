{{-- resources/views/livewire/platform/platform-dashboard.blade.php --}}
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 py-8">
        
        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Platform Dashboard</h1>
                <p class="text-gray-600">Nafasi Platform — System Overview</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.ai.dashboard') }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-sm">
                    🧠 AI Dashboard
                </a>
                <a href="{{ route('platform.tenants') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                    Manage Users
                </a>

                <a href="{{ route('platform.tenants.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                    + Add Tenants
                </a>
            </div>
        </div>

        {{-- System Health --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">ML Service</p>
                        <p class="text-lg font-bold {{ $mlStatus === 'healthy' ? 'text-green-600' : 'text-red-600' }}">
                            {{ ucfirst($mlStatus) }}
                        </p>
                    </div>
                    <div class="w-3 h-3 rounded-full {{ $mlStatus === 'healthy' ? 'bg-green-500' : 'bg-red-500' }}"></div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Database</p>
                        <p class="text-lg font-bold {{ $dbStatus === 'connected' ? 'text-green-600' : 'text-red-600' }}">
                            {{ ucfirst($dbStatus) }}
                        </p>
                    </div>
                    <div class="w-3 h-3 rounded-full {{ $dbStatus === 'connected' ? 'bg-green-500' : 'bg-red-500' }}"></div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Queue</p>
                        <p class="text-lg font-bold {{ $queueStatus === 'healthy' ? 'text-green-600' : 'text-orange-600' }}">
                            {{ ucfirst($queueStatus) }}
                        </p>
                    </div>
                    <div class="w-3 h-3 rounded-full {{ $queueStatus === 'healthy' ? 'bg-green-500' : 'bg-orange-500' }}"></div>
                </div>
            </div>
        </div>

        {{-- Key Metrics --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-sm text-gray-500">Total Tenants</p>
                <p class="text-3xl font-bold text-gray-900">{{ $totalTenants }}</p>
                <p class="text-xs text-green-600">{{ $activeTenants }} active</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-sm text-gray-500">Total Users</p>
                <p class="text-3xl font-bold text-gray-900">{{ $totalUsers }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-sm text-gray-500">Total Facilities</p>
                <p class="text-3xl font-bold text-gray-900">{{ $totalFacilities >= 0 ? $totalFacilities : '—' }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-sm text-gray-500">Users by Role</p>
                <div class="mt-2 space-y-1">
                    @foreach($usersByRole as $role => $count)
                        <div class="flex justify-between text-xs">
                            <span class="text-gray-600">{{ str_replace('-', ' ', $role) }}</span>
                            <span class="font-medium">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Tenant Growth Chart --}}
        <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Tenant Growth (6 Months)</h2>
            <div class="flex items-end space-x-2 h-40">
                @php $maxCount = max(array_column($tenantGrowth, 'count')) ?: 1; @endphp
                @foreach($tenantGrowth as $point)
                    <div class="flex-1 flex flex-col items-center">
                        <div class="w-full bg-blue-500 rounded-t" 
                             style="height: {{ ($point['count'] / $maxCount) * 100 }}%">
                        </div>
                        <span class="text-xs text-gray-500 mt-2 transform -rotate-45 origin-top-left whitespace-nowrap">
                            {{ $point['month'] }}
                        </span>
                        <span class="text-xs font-medium text-gray-700">{{ $point['count'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Recent Activity --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h2>
            <div class="space-y-4">
                @foreach($recentActivity as $activity)
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0 mt-1">
                            @if($activity['type'] === 'tenant_registered')
                                <span class="text-xl">🏢</span>
                            @elseif($activity['type'] === 'facility_verified')
                                <span class="text-xl">✅</span>
                            @elseif($activity['type'] === 'emergency_dispatched')
                                <span class="text-xl">🚨</span>
                            @elseif($activity['type'] === 'model_trained')
                                <span class="text-xl">🧠</span>
                            @else
                                <span class="text-xl">📋</span>
                            @endif
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-900">{{ $activity['message'] }}</p>
                            <p class="text-xs text-gray-400">{{ $activity['time'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>