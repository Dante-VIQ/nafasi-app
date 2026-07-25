{{-- resources/views/livewire/profile/two-factor-auth-manager.blade.php --}}
<div>
    @if(!$enabled)
        <p class="text-sm text-gray-600 mb-4">Add extra security to your account by enabling two-factor authentication.</p>
        
        @if(!$showSetup)
            <div class="space-y-3">
                <select wire:model="method" class="block w-full rounded-lg border-gray-300 text-sm">
                    <option value="app">Authenticator App</option>
                    <option value="sms">SMS</option>
                </select>
                <button wire:click="enable" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                    Enable 2FA
                </button>
            </div>
        @else
            <div class="space-y-3">
                <p class="text-sm text-gray-700">Enter the code from your {{ $method === 'app' ? 'authenticator app' : 'SMS' }}:</p>
                <input type="text" wire:model="confirmationCode" maxlength="6" 
                       class="block w-full text-center text-2xl tracking-widest rounded-lg border-gray-300">
                <button wire:click="confirm" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                    Confirm
                </button>
            </div>
        @endif
    @else
        <div class="flex items-center space-x-2 mb-4">
            <span class="w-2 h-2 bg-green-500 rounded-full"></span>
            <span class="text-sm text-green-700 font-medium">Two-factor authentication is active</span>
        </div>
        <button wire:click="disable" wire:confirm="Are you sure?"
                class="px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 text-sm">
            Disable 2FA
        </button>
    @endif
</div>