<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 py-8">

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Escalation Dashboard</h1>
                <p class="text-sm text-gray-500">Cases that require human review</p>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r-lg">
                <p class="text-green-700">{{ session('message') }}</p>
            </div>
        @endif

        {{-- Filters --}}
        <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
            <select wire:model.live="filterLevel" class="rounded-lg border-gray-300 text-sm">
                <option value="">All Levels</option>
                <option value="immediate">Immediate</option>
                <option value="priority">Priority</option>
                <option value="standard">Standard</option>
            </select>
        </div>

        {{-- Escalated Cases --}}
        <div class="space-y-4">
            @forelse ($escalatedCases as $case)
                @php
                    $levelColors = [
                        'immediate' => 'border-red-500 bg-red-50',
                        'priority'  => 'border-orange-500 bg-orange-50',
                        'standard'  => 'border-yellow-500 bg-yellow-50',
                    ];
                    $borderColor = $levelColors[$case->escalation_level] ?? 'border-gray-300 bg-white';
                @endphp

                <div class="bg-white rounded-xl shadow-sm border-l-4 {{ $borderColor }} p-5">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="text-xs font-mono text-gray-400">#{{ substr($case->uuid, 0, 8) }}</span>
                                <span class="px-2 py-0.5 text-xs rounded-full 
                                    {{ $case->escalation_level === 'immediate' ? 'bg-red-100 text-red-700' : '' }}
                                    {{ $case->escalation_level === 'priority' ? 'bg-orange-100 text-orange-700' : '' }}
                                    {{ $case->escalation_level === 'standard' ? 'bg-yellow-100 text-yellow-700' : '' }}">
                                    {{ ucfirst($case->escalation_level) }}
                                </span>
                                @if ($case->escalation_handler_id)
                                    <span class="text-xs text-green-600">✓ Claimed by #{{ $case->escalation_handler_id }}</span>
                                @else
                                    <span class="text-xs text-red-500">⚠️ Unassigned</span>
                                @endif
                            </div>

                            <p class="text-sm text-gray-900 mb-2">{{ $case->anonymized_text }}</p>

                            {{-- Reasons --}}
                            @if (!empty($case->escalation_reasons))
                                <div class="flex flex-wrap gap-1 mb-2">
                                    @foreach ($case->escalation_reasons as $reason)
                                        <span class="px-2 py-0.5 text-xs bg-gray-100 text-gray-600 rounded-full">
                                            {{ str_replace('_', ' ', $reason) }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Risk & Decision --}}
                            <div class="grid grid-cols-3 gap-3 text-xs text-gray-500 mt-2">
                                <div>
                                    <span class="font-medium">Risk:</span>
                                    {{ ucfirst($case->risk_assessment['level'] ?? 'N/A') }}
                                </div>
                                <div>
                                    <span class="font-medium">AI Decision:</span>
                                    {{ ucwords(str_replace('_', ' ', $case->decision['action'] ?? 'N/A')) }}
                                </div>
                                <div>
                                    <span class="font-medium">Confidence:</span>
                                    {{ round(($case->decision['confidence'] ?? 0) * 100) }}%
                                </div>
                            </div>
                        </div>

                        <div class="ml-4">
                            @if (!$case->escalation_handler_id)
                                <button wire:click="claim({{ $case->id }})"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">
                                    Claim
                                </button>
                            @elseif ($case->escalation_handler_id === Auth::id() && !$case->escalation_resolved_at)
                                <button wire:click="$set('selectedId', {{ $case->id }})"
                                        class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700">
                                    Resolve
                                </button>
                            @elseif ($case->escalation_resolved_at)
                                <span class="text-sm text-green-600">✓ Resolved</span>
                            @else
                                <span class="text-sm text-gray-400">Handled by another</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl shadow-sm p-12 text-center text-gray-500">
                    No escalated cases at this time.
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $escalatedCases->links() }}
        </div>

        {{-- Resolve Modal --}}
        @if ($selectedId)
            <div class="fixed inset-0 bg-gray-900/50 flex items-center justify-center z-50"
                 x-data @click.self="$wire.set('selectedId', null)">
                <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 p-6">
                    <h3 class="text-lg font-semibold mb-3">Resolve Case</h3>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Resolution Notes</label>
                        <textarea wire:model="handlerNotes" rows="3"
                                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500"
                                  placeholder="What action was taken?"></textarea>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button wire:click="$set('selectedId', null)"
                                class="px-4 py-2 border rounded-lg text-sm hover:bg-gray-50">
                            Cancel
                        </button>
                        <button wire:click="resolve({{ $selectedId }})"
                                class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700">
                            Confirm Resolution
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>