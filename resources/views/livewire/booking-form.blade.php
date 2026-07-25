{{-- resources/views/livewire/booking-form.blade.php --}}
<div>
    @if(!$booked)
        <button wire:click="$toggle('showForm')"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
            📅 Book Appointment
        </button>

        @if($showForm)
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Book at {{ $facility->name }}</h3>
                        <button wire:click="$toggle('showForm')" class="text-gray-500 hover:text-gray-700">✕</button>
                    </div>

                    <form wire:submit.prevent="book" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Your Name *</label>
                            <input type="text" wire:model="patient_name"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500">
                            @error('patient_name') <p class="text-red-600 text-xs">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Phone (optional)</label>
                            <input type="text" wire:model="patient_phone"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Preferred Date & Time *</label>
                            <input type="datetime-local" wire:model="scheduled_at"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500">
                            @error('scheduled_at') <p class="text-red-600 text-xs">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Reason (optional)</label>
                            <input type="text" wire:model="reason"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500"
                                   placeholder="e.g., general checkup, pharmacy refill">
                        </div>
                        <div class="flex justify-end gap-3 mt-4">
                            <button type="button" wire:click="$toggle('showForm')"
                                    class="px-4 py-2 text-gray-600 hover:text-gray-800 text-sm">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                                Confirm Booking
                            </button>
                        </div>
                    </form>

                    <p class="text-xs text-gray-400 mt-4">🔒 Your information is stored securely at the facility and never shared.</p>
                </div>
            </div>
        @endif
    @else
        <div class="bg-green-50 border border-green-200 rounded-lg p-3 text-center">
            <p class="text-green-700 font-medium">✓ Appointment Booked!</p>
            <p class="text-sm text-green-600">{{ $facility->name }} will expect you at {{ \Carbon\Carbon::parse($scheduled_at)->format('d M Y, H:i') }}</p>
        </div>
    @endif
</div>