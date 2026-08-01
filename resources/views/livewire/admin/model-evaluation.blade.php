<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Model Evaluation</h1>
                <p class="text-sm text-gray-500">AI performance metrics for the last {{ $days }} days</p>
            </div>
            <select wire:model.live="days" class="rounded-lg border-gray-300 text-sm">
                <option value="7">7 days</option>
                <option value="30">30 days</option>
                <option value="90">90 days</option>
            </select>
        </div>

        {{-- Key Metrics --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-sm text-gray-500">Overall Accuracy</p>
                <p class="text-3xl font-bold {{ $overallAccuracy >= 80 ? 'text-green-600' : 'text-yellow-600' }}">
                    {{ $overallAccuracy }}%
                </p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-sm text-gray-500">Total Verified</p>
                <p class="text-3xl font-bold text-gray-900">{{ $totalVerified }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-sm text-gray-500">Escalated Cases</p>
                <p class="text-3xl font-bold text-orange-600">
                    {{ collect($escalationStats)->sum('total') }}
                </p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-sm text-gray-500">Avg Resolution Time</p>
                <p class="text-3xl font-bold text-gray-900">
                    {{ collect($escalationStats)->avg('avg_resolution_minutes') ? round(collect($escalationStats)->avg('avg_resolution_minutes')) : '—' }} min
                </p>
            </div>
        </div>

        {{-- Accuracy Trend --}}
        <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Accuracy Trend</h2>
            <div class="h-64 flex items-end space-x-1">
                @php $maxTotal = max(array_column($accuracyTrend, 'total')) ?: 1; @endphp
                @foreach($accuracyTrend as $point)
                    <div class="flex-1 flex flex-col items-center" title="{{ $point['date'] }}: {{ $point['accuracy'] }}% ({{ $point['total'] }} verified)">
                        <div class="w-full {{ $point['accuracy'] >= 80 ? 'bg-green-400' : ($point['accuracy'] >= 60 ? 'bg-yellow-400' : 'bg-red-400') }} rounded-t"
                             style="height: {{ ($point['accuracy'] / 100) * 200 }}px"></div>
                        <span class="text-xs text-gray-400 mt-1 rotate-45 origin-top-left whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($point['date'])->format('M d') }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Confidence vs Accuracy --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Confidence vs Actual Accuracy</h2>
                <div class="space-y-4">
                    @foreach($confidenceDistribution as $bucket)
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span>{{ $bucket['bucket'] }}</span>
                                <span class="font-medium">{{ $bucket['accuracy'] }}% ({{ $bucket['correct'] }}/{{ $bucket['total'] }})</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="h-2 rounded-full {{ $bucket['accuracy'] >= 80 ? 'bg-green-500' : ($bucket['accuracy'] >= 60 ? 'bg-yellow-500' : 'bg-red-500') }}"
                                     style="width: {{ $bucket['accuracy'] }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Risk Distribution --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Risk Level Distribution</h2>
                <div class="space-y-3">
                    @foreach($riskLevelDistribution as $level)
                        <div class="flex items-center justify-between">
                            <span class="text-sm capitalize">{{ $level['level'] }}</span>
                            <span class="text-sm font-medium">{{ $level['total'] }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            @php $maxRisk = max(array_column($riskLevelDistribution, 'total')) ?: 1; @endphp
                            <div class="h-2 rounded-full
                                {{ $level['level'] === 'critical' ? 'bg-red-500' : '' }}
                                {{ $level['level'] === 'high' ? 'bg-orange-500' : '' }}
                                {{ $level['level'] === 'medium' ? 'bg-yellow-500' : '' }}
                                {{ $level['level'] === 'low' ? 'bg-green-500' : '' }}
                                {{ $level['level'] === 'routine' ? 'bg-gray-400' : '' }}"
                                 style="width: {{ ($level['total'] / $maxRisk) * 100 }}%"></div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Language Distribution --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Language Distribution</h2>
            <div class="flex flex-wrap gap-4">
                @foreach($languageDistribution as $lang)
                    <div class="bg-gray-50 rounded-lg p-4 min-w-[120px] text-center">
                        <p class="text-2xl font-bold text-gray-900">{{ strtoupper($lang['language']) }}</p>
                        <p class="text-sm text-gray-500">{{ $lang['total'] }} requests</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>