{{-- resources/views/livewire/facility/patient-list.blade.php --}}
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 py-8">
        
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Patients</h1>
                <p class="text-gray-600">Manage patient records and appointments</p>
            </div>
            <a href="{{ url('/facility/dashboard') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm">
                ← Back to Dashboard
            </a>
        </div>

        {{-- Quick Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-4">
                <p class="text-sm text-gray-500">Total Unique Patients</p>
                <p class="text-2xl font-bold text-gray-900">{{ $totalUniquePatients }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-4">
                <p class="text-sm text-gray-500">Today's Appointments</p>
                <p class="text-2xl font-bold text-blue-600">{{ $todayPatients }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-4">
                <p class="text-sm text-gray-500">Pending</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $pendingPatients }}</p>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
            <div class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[200px]">
                    <input type="text" wire:model.live.debounce.300ms="search" 
                           placeholder="Search by name, phone, or reason..."
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 px-4 py-2 text-sm">
                </div>
                <div>
                    <select wire:model.live="filterStatus" 
                            class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 text-sm px-4 py-2">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="arrived">Arrived</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Patients Table --}}
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th wire:click="sortBy('patient_name')" 
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:text-gray-700">
                            Patient
                            @if($sortField === 'patient_name')
                                <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                        <th wire:click="sortBy('reason')" 
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer">
                            Reason
                        </th>
                        <th wire:click="sortBy('scheduled_at')" 
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer">
                            Date
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($patients as $patient)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $patient->patient_name }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $patient->patient_phone ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $patient->reason ?? 'General' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $patient->scheduled_at ? $patient->scheduled_at->format('d M Y, H:i') : '—' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 text-xs rounded-full
                                    {{ $patient->status === 'confirmed' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $patient->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                    {{ $patient->status === 'arrived' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $patient->status === 'completed' ? 'bg-purple-100 text-purple-700' : '' }}
                                    {{ $patient->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}">
                                    {{ ucfirst($patient->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button wire:click="viewPatient({{ $patient->id }})" 
                                        class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                    View
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                No patients found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $patients->links() }}
        </div>

        {{-- Patient Detail Modal --}}
        @if($showPatientModal)
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full mx-4 p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $selectedPatient['patient_name'] ?? 'Unknown' }}</h3>
                            <p class="text-sm text-gray-500">{{ $selectedPatient['patient_phone'] ?? 'No phone' }}</p>
                        </div>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 text-xl">✕</button>
                    </div>

                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Email:</span>
                            <span>{{ $selectedPatient['patient_email'] ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Last Visit:</span>
                            <span>{{ $selectedPatient['scheduled_at'] ? \Carbon\Carbon::parse($selectedPatient['scheduled_at'])->format('d M Y') : '—' }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Status:</span>
                            <span class="font-medium">{{ ucfirst($selectedPatient['status'] ?? 'unknown') }}</span>
                        </div>
                    </div>

                    {{-- Appointment History --}}
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Appointment History</h4>
                    @if(count($patientAppointments) > 0)
                        <div class="space-y-2 max-h-48 overflow-y-auto">
                            @foreach($patientAppointments as $appointment)
                                <div class="border rounded-lg p-3 text-sm">
                                    <div class="flex justify-between">
                                        <span class="font-medium">{{ \Carbon\Carbon::parse($appointment['scheduled_at'])->format('d M Y, H:i') }}</span>
                                        <span class="px-2 py-0.5 text-xs rounded-full
                                            {{ $appointment['status'] === 'completed' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                            {{ ucfirst($appointment['status']) }}
                                        </span>
                                    </div>
                                    <p class="text-gray-600 mt-1">{{ $appointment['reason'] ?? 'No reason given' }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No previous appointments.</p>
                    @endif

                    <div class="mt-6 flex justify-end space-x-2">
                        @if($selectedPatient['patient_phone'])
                            <a href="tel:{{ $selectedPatient['patient_phone'] }}" 
                               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                                📞 Call
                            </a>
                        @endif
                        <button wire:click="closeModal" 
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>