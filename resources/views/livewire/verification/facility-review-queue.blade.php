{{-- resources/views/livewire/verification/facility-review-queue.blade.php --}}
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Facility Verification Queue</h1>

        <div class="bg-white rounded-lg shadow-sm p-4 mb-6 flex flex-wrap gap-4 items-center">
            <div>
                <label class="text-sm text-gray-600 mr-2">Status:</label>
                <select wire:model.live="filter" class="rounded-md border-gray-300 text-sm">
                    <option value="submitted">Pending</option>
                    <option value="under_review">Under Review</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
            <div>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by name..."
                    class="rounded-md border-gray-300 text-sm px-3 py-2">
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Facility</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Submitted</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($facilities as $facility)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $facility->name }}</div>
                                <div class="text-sm text-gray-500">{{ $facility->phone }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                {{ ucfirst(str_replace('_', ' ', $facility->facility_type)) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $facility->city }}, {{ $facility->county }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $facility->created_at->diffForHumans() }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full
                                    {{ $facility->registration_status === 'approved' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $facility->registration_status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}
                                    {{ $facility->registration_status === 'submitted' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                    {{ $facility->registration_status === 'under_review' ? 'bg-blue-100 text-blue-700' : '' }}">
                                    {{ ucfirst(str_replace('_', ' ', $facility->registration_status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ url('/verification/review', $facility) }}"
                                   class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                    Review →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">No facilities found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

@if($totalCount > 20)
    <div class="mt-4">
        {{-- simple previous/next links --}}
        @php $page = request('page', 1); @endphp
        @if($page > 1)
            <a href="?page={{ $page - 1 }}" class="text-blue-600">← Previous</a>
        @endif
        @if(($page * 20) < $totalCount)
            <a href="?page={{ $page + 1 }}" class="text-blue-600 float-right">Next →</a>
        @endif
    </div>
@endif
    </div>
</div>