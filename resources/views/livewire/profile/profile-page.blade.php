{{-- resources/views/livewire/profile/profile-page.blade.php --}}
<div class="min-h-screen bg-gray-50">
    <div class="max-w-3xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Your Profile</h1>

        @if (session()->has('message'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r-lg">
                <p class="text-green-700">{{ session('message') }}</p>
            </div>
        @endif

        {{-- Profile Info --}}
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Personal Information</h2>
            <form wire:submit="updateProfile" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" wire:model="name" 
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500">
                    @error('name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" value="{{ $email }}" disabled
                           class="mt-1 block w-full rounded-lg border-gray-200 bg-gray-50 text-gray-500 cursor-not-allowed">
                    <p class="text-xs text-gray-400 mt-1">Email cannot be changed.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Phone</label>
                    <input type="text" wire:model="phone" 
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500"
                           placeholder="+254700123456">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Language Preference</label>
                    <select wire:model="language_preference" 
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500">
                        <option value="sw">Swahili</option>
                        <option value="en">English</option>
                        <option value="sheng">Sheng</option>
                    </select>
                </div>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                    Save Changes
                </button>
            </form>
        </div>

        {{-- Change Password --}}
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Change Password</h2>
            <form wire:submit="updatePassword" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Current Password</label>
                    <input type="password" wire:model="current_password" 
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500">
                    @error('current_password') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">New Password</label>
                    <input type="password" wire:model="new_password" 
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500">
                    @error('new_password') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                    <input type="password" wire:model="new_password_confirmation" 
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500">
                </div>
                <button type="submit" class="px-6 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-800 text-sm font-medium">
                    Update Password
                </button>
            </form>
        </div>

        {{-- Two-Factor Authentication --}}
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Two-Factor Authentication</h2>
            <livewire:profile.two-factor-auth-manager />
        </div>
    </div>
</div>