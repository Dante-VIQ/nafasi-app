<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 py-8">

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">AI Outcome Verification</h1>
                <p class="text-sm text-gray-500">Confirm whether the AI correctly routed each request</p>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r-lg">
                <p class="text-green-700">{{ session('message') }}</p>
            </div>
        @endif

        {{-- Filters --}}
        <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
            <div class="flex items-center gap-4">
                <label class="text-sm text-gray-600">Filter by outcome:</label>
                <select wire:model.live="filterOutcome" class="rounded-lg border-gray-300 text-sm">
                    <option value="">All Outcomes</option>
                    <option value="booked">Booked</option>
                    <option value="called">Called</option>
                    <option value="directions">Directions</option>
                    <option value="dispatched">Dispatched</option>
                    <option value="none">No Action</option>
                </select>
            </div>
        </div>

        {{-- Outcomes Table --}}
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 overflow-x-scroll">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User Text</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Language</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">AI Suggested</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Confidence</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Outcome</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Verified?</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Risk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">AI Decision</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Escalated?</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($outcomes as $outcome)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-xs text-gray-400 font-mono">
                                {{ substr($outcome->uuid, 0, 8) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 max-w-xs truncate">
                                {{ $outcome->anonymized_text }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="px-2 py-0.5 text-xs rounded-full bg-gray-100 text-gray-700">
                                    {{ strtoupper($outcome->language ?? '—') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ is_array($outcome->facility_hints) ? implode(', ', $outcome->facility_hints) : '—' }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center gap-1">
                                    <div class="w-12 bg-gray-200 rounded-full h-1.5">
                                        <div class="h-1.5 rounded-full {{ $outcome->confidence >= 0.7 ? 'bg-green-500' : ($outcome->confidence >= 0.4 ? 'bg-yellow-500' : 'bg-red-500') }}"
                                            style="width: {{ ($outcome->confidence ?? 0) * 100 }}%"></div>
                                    </div>
                                    <span
                                        class="text-xs text-gray-500">{{ round(($outcome->confidence ?? 0) * 100) }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if ($outcome->outcome_type === 'booked')
                                    <span
                                        class="px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-700">Booked</span>
                                @elseif ($outcome->outcome_type === 'called')
                                    <span
                                        class="px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-700">Called</span>
                                @elseif ($outcome->outcome_type === 'directions')
                                    <span
                                        class="px-2 py-0.5 text-xs rounded-full bg-purple-100 text-purple-700">Directions</span>
                                @elseif ($outcome->outcome_type === 'dispatched')
                                    <span
                                        class="px-2 py-0.5 text-xs rounded-full bg-red-100 text-red-700">Dispatched</span>
                                @else
                                    <span class="px-2 py-0.5 text-xs rounded-full bg-gray-100 text-gray-500">None</span>
                                @endif
                            </td>

                            <!-- Inside the foreach -->
                            <td class="px-6 py-4 text-sm">
                                @php
                                    $risk = $outcome->risk_assessment;
                                    $level = $risk['level'] ?? 'unknown';
                                    $colors = [
                                        'critical' => 'bg-red-100 text-red-700',
                                        'high' => 'bg-orange-100 text-orange-700',
                                        'medium' => 'bg-yellow-100 text-yellow-700',
                                        'low' => 'bg-green-100 text-green-700',
                                        'routine' => 'bg-gray-100 text-gray-600',
                                    ];
                                @endphp
                                <span
                                    class="px-2 py-0.5 text-xs rounded-full {{ $colors[$level] ?? 'bg-gray-100 text-gray-500' }}">
                                    {{ ucfirst($level) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ $outcome->decision['action'] ?? '—' }}
                            </td>
                            <!-- Inside the foreach -->
                            <td class="px-6 py-4 text-sm">
                                @if ($outcome->escalated)
                                    <span class="px-2 py-0.5 text-xs rounded-full bg-red-100 text-red-700">
                                        ⚡ Yes ({{ $outcome->escalation_level }})
                                    </span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if (is_null($outcome->was_correct))
                                    <span class="text-yellow-500">⚠️ Pending</span>
                                @elseif ($outcome->was_correct)
                                    <span class="text-green-600">✓ Correct</span>
                                @else
                                    <span class="text-red-600">✗ Incorrect</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button wire:click="selectOutcome({{ $outcome->id }})"
                                    class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                    {{ is_null($outcome->was_correct) ? 'Verify' : 'Review' }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                No outcomes recorded yet. User interactions will appear here.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $outcomes->links() }}
        </div>

        {{-- Verification Modal --}}
        @if ($selectedId)
            @php $selected = \App\Models\InteractionOutcome::find($selectedId); @endphp
            <div class="fixed inset-0 bg-gray-900/50 flex items-center justify-center z-50" x-data
                @click.self="$wire.closeModal()">
                <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full mx-4 p-6">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Verify Outcome #{{ substr($selected->uuid, 0, 8) }}
                        </h3>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 text-xl">×</button>
                    </div>

                    {{-- Details --}}
                    <dl class="space-y-3 text-sm mb-6">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <dt class="text-xs text-gray-500">Original Text</dt>
                            <dd class="text-gray-900">{{ $selected->anonymized_text }}</dd>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <dt class="text-xs text-gray-500">Language</dt>
                                <dd class="font-medium">{{ strtoupper($selected->language ?? '—') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-500">Confidence</dt>
                                <dd class="font-medium">{{ round(($selected->confidence ?? 0) * 100) }}%</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-500">AI Suggested</dt>
                                <dd class="font-medium">
                                    {{ is_array($selected->facility_hints) ? implode(', ', $selected->facility_hints) : '—' }}
                                </dd>
                            </div>
                            @if ($selected->decision)
                                <div class="bg-gray-50 rounded-lg p-3 mb-3">
                                    <dt class="text-xs text-gray-500">AI Decision</dt>
                                    <dd class="font-medium">
                                        {{ ucwords(str_replace('_', ' ', $selected->decision['action'] ?? 'N/A')) }}
                                    </dd>
                                    <dd class="text-xs text-gray-400">Confidence:
                                        {{ round(($selected->decision['confidence'] ?? 0) * 100) }}%</dd>
                                </div>
                            @endif
                            <div>
                                <dt class="text-xs text-gray-500">User Action</dt>
                                <dd class="font-medium">{{ $selected->outcome_type ?? 'None' }}</dd>
                            </div>
                        </div>
                    </dl>

                    {{-- Verification Form --}}
                    <form wire:submit="verify">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Was the AI correct?</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" wire:model="wasCorrect" value="1"
                                        class="text-green-600">
                                    <span class="text-sm">✅ Yes, correct</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" wire:model="wasCorrect" value="0"
                                        class="text-red-600">
                                    <span class="text-sm">❌ No, incorrect</span>
                                </label>
                            </div>
                            @error('wasCorrect')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Notes (optional)</label>
                            <textarea wire:model="verificationNotes" rows="2"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500"
                                placeholder="Any observations about this routing..."></textarea>
                            @error('verificationNotes')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end gap-3">
                            <button type="button" wire:click="closeModal"
                                class="px-4 py-2 border rounded-lg text-sm hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                                Save Verification
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
