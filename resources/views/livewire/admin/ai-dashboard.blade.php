{{-- resources/views/livewire/admin/ai-dashboard.blade.php --}}
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 py-8">

        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">AI Dashboard</h1>
                <p class="text-gray-600">Model Management & Communication</p>
            </div>
            <div class="flex items-center space-x-2">
                <span
                    class="w-3 h-3 rounded-full {{ $modelHealth === 'healthy' ? 'bg-green-500' : 'bg-red-500' }}"></span>
                <span class="text-sm text-gray-600">{{ ucfirst($modelHealth) }}</span>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r-lg">
                <p class="text-green-700">{{ session('message') }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- Test Classification --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Test Model</h2>
                <div class="flex gap-2 mb-4">
                    <input type="text" wire:model="testText" wire:keydown.enter="testClassification"
                        class="flex-1 rounded-lg border-gray-300 px-4 py-2 text-sm"
                        placeholder="Enter text to classify...">
                    <button wire:click="testClassification"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                        Classify
                    </button>
                </div>
                @if (!empty($classificationResult))
                    <div class="bg-gray-50 rounded-lg p-4">
                        <pre class="text-xs text-gray-700 overflow-x-auto">{{ json_encode($classificationResult, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                @endif
            </div>

            {{-- Training --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Training</h2>
                <select wire:model="trainingDays" class="rounded-lg border-gray-300 text-sm mb-4">
                    <option value="1">1 day</option>
                    <option value="7">7 days</option>
                    <option value="30">30 days</option>
                </select>
                <button wire:click="triggerTraining" wire:loading.attr="disabled"
                    class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-sm">
                    <span wire:loading.remove>Train Model</span>
                    <span wire:loading>Training...</span>
                </button>
                @if ($trainingStatus)
                    <div class="mt-4 p-3 bg-gray-50 rounded-lg text-sm text-gray-700">{{ $trainingStatus }}</div>
                @endif

                <button wire:click="triggerLearning"
                    class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-sm">
                    🧠 Train from Outcomes
                </button>

                @if (session()->has('learning_result'))
                    <div class="mt-4 p-3 bg-gray-50 rounded-lg text-sm">
                        <p><strong>Training complete:</strong></p>
                        <p>Correct: {{ session('learning_result')['correct'] }}</p>
                        <p>Incorrect: {{ session('learning_result')['incorrect'] }}</p>
                        <p>Updates: {{ session('learning_result')['updates'] }}</p>
                    </div>
                @endif
            </div>

            {{-- Predictions --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Predictions</h2>
                <p>Volume: <strong>{{ ucfirst($predictions['predicted_volume'] ?? 'Unknown') }}</strong></p>
                <p>Emergencies: {{ implode(', ', $predictions['likely_emergency_types'] ?? []) }}</p>
                <p>Coordinators needed: {{ $predictions['recommended_staffing']['coordinators_needed'] ?? '—' }}</p>
            </div>

            {{-- Seasonal Risks --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Seasonal Risks</h2>
                <p>{{ ucfirst($seasonalRisks['season'] ?? 'Unknown') }} season</p>
                <div class="flex flex-wrap gap-1 mt-2">
                    @foreach ($seasonalRisks['active_risks'] ?? [] as $risk)
                        <span
                            class="px-2 py-0.5 bg-orange-100 text-orange-700 text-xs rounded-full">{{ $risk }}</span>
                    @endforeach
                </div>
            </div>

            {{-- Communication --}}
            <div class="bg-white rounded-xl shadow-sm p-6 lg:col-span-2">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Communicate with Model</h2>
                <div class="space-y-3 max-h-40 overflow-y-auto mb-4">
                    @foreach ($adminMessages as $msg)
                        <div class="p-2 bg-blue-50 rounded text-sm">
                            <strong>{{ $msg['from'] ?? 'Admin' }}:</strong> {{ $msg['message'] }}
                            <span class="text-xs text-gray-400 ml-2">{{ $msg['timestamp'] ?? '' }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="flex gap-2">
                    <input type="text" wire:model="adminMessage" wire:keydown.enter="sendMessageToModel"
                        class="flex-1 rounded-lg border-gray-300 px-4 py-2 text-sm"
                        placeholder="Send context to the model...">
                    <button wire:click="sendMessageToModel"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                        Send
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
