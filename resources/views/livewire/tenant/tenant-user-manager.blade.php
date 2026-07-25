{{-- resources/views/livewire/tenant/tenant-user-manager.blade.php --}}
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 py-8">
        
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">User Management</h1>
                <p class="text-gray-600">Manage users in your organization</p>
            </div>
            <button wire:click="$toggle('showCreateForm')" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                + Add User
            </button>
        </div>

        @if (session()->has('message'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r-lg">
                <p class="text-green-700">{{ session('message') }}</p>
            </div>
        @endif

        {{-- Create User Form --}}
        @if($showCreateForm)
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold mb-4">Add New User</h2>
                <form wire:submit="createUser" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Name *</label>
                        <input type="text" wire:model="newName" class="mt-1 w-full rounded-lg border-gray-300">
                        @error('newName') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email *</label>
                        <input type="email" wire:model="newEmail" class="mt-1 w-full rounded-lg border-gray-300">
                        @error('newEmail') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Phone</label>
                        <input type="text" wire:model="newPhone" class="mt-1 w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Role *</label>
                        <select wire:model="newRole" class="mt-1 w-full rounded-lg border-gray-300">
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ str_replace('-', ' ', $role->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Password *</label>
                        <input type="password" wire:model="newPassword" class="mt-1 w-full rounded-lg border-gray-300">
                        @error('newPassword') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex items-end space-x-2">
                        <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                            Create User
                        </button>
                        <button type="button" wire:click="$toggle('showCreateForm')" class="px-6 py-2 text-gray-600 text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        @endif

        {{-- Filters --}}
        <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
            <div class="flex gap-4">
                <div class="flex-1">
                    <input type="text" wire:model.live.debounce.300ms="search" 
                           placeholder="Search by name, email, phone..."
                           class="w-full rounded-lg border-gray-300 text-sm px-4 py-2">
                </div>
                <div>
                    <select wire:model.live="filterRole" class="rounded-lg border-gray-300 text-sm px-4 py-2">
                        <option value="">All Roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ str_replace('-', ' ', $role->name) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Users Table --}}
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                @if($editingUserId === $user->id)
                                    <div class="space-y-2">
                                        <input type="text" wire:model="editName" class="w-full rounded border-gray-300 text-sm">
                                        <input type="text" wire:model="editPhone" class="w-full rounded border-gray-300 text-sm">
                                        <select wire:model="editRole" class="w-full rounded border-gray-300 text-sm">
                                            @foreach($roles as $role)
                                                <option value="{{ $role->name }}">{{ str_replace('-', ' ', $role->name) }}</option>
                                            @endforeach
                                        </select>
                                        <div class="flex space-x-2">
                                            <button wire:click="saveEdit" class="text-green-600 text-xs">Save</button>
                                            <button wire:click="cancelEdit" class="text-gray-500 text-xs">Cancel</button>
                                        </div>
                                    </div>
                                @else
                                    <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                    @if($user->phone)
                                        <div class="text-xs text-gray-500">{{ $user->phone }}</div>
                                    @endif
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ str_replace('-', ' ', $user->primary_role) }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 text-xs rounded-full {{ $user->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-sm">
                                <button wire:click="startEdit({{ $user->id }})" class="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
                                <button wire:click="toggleActive({{ $user->id }})" 
                                        class="{{ $user->is_active ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900' }}">
                                    {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                No users found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
</div>