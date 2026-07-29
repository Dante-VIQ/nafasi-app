<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Verify AI Outcomes</h1>

    @if(session()->has('message'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('message') }}</div>
    @endif

    <div class="mb-4">
        <select wire:model.live="filterOutcome" class="rounded-lg border-gray-300">
            <option value="">All Outcomes</option>
            <option value="booked">Booked</option>
            <option value="called">Called</option>
            <option value="directions">Directions</option>
            <option value="dispatched">Dispatched</option>
        </select>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Text</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Language</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Intent</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Outcome</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Correct?</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($outcomes as $outcome)
                    <tr>
                        <td class="px-6 py-4 text-sm">{{ Str::limit($outcome->anonymized_text, 50) }}</td>
                        <td class="px-6 py-4 text-sm">{{ $outcome->language }}</td>
                        <td class="px-6 py-4 text-sm">{{ implode(', ', $outcome->facility_hints ?? []) }}</td>
                        <td class="px-6 py-4 text-sm">{{ $outcome->outcome_type }}</td>
                        <td class="px-6 py-4 text-sm">
                            @if(is_null($outcome->was_correct))
                                <span class="text-gray-400">—</span>
                            @elseif($outcome->was_correct)
                                <span class="text-green-600">✓</span>
                            @else
                                <span class="text-red-600">✗</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button wire:click="$set('selected', {{ $outcome->id }})" class="text-blue-600 hover:underline text-sm">Verify</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{ $outcomes->links() }}

    <!-- Verification Modal -->
    @if($selected)
        @php $outcome = \App\Models\InteractionOutcome::find($selected); @endphp
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full mx-4 p-6">
                <h3 class="text-lg font-semibold mb-2">Verify Outcome</h3>
                <p class="text-sm text-gray-500 mb-4">Original: "{{ $outcome->anonymized_text }}"</p>
                <p class="text-sm mb-4">AI suggested: {{ implode(', ', $outcome->facility_hints ?? []) }} ({{ $outcome->confidence * 100 }}%)</p>
                <p class="text-sm mb-4">User action: {{ $outcome->outcome_type }} at facility ID {{ $outcome->outcome_facility_id }}</p>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Was this correct?</label>
                    <select wire:model="wasCorrect" class="w-full rounded-lg border-gray-300">
                        <option value="">Select...</option>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                    @error('wasCorrect') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Notes</label>
                    <textarea wire:model="verificationNotes" rows="2" class="w-full rounded-lg border-gray-300"></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <button wire:click="$set('selected', null)" class="px-4 py-2 border rounded-lg">Cancel</button>
                    <button wire:click="verify({{ $outcome->id }})" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Save</button>
                </div>
            </div>
        </div>
    @endif
</div>