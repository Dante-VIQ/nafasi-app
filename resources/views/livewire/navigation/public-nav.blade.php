{{-- resources/views/livewire/navigation/public-nav.blade.php --}}
<nav class="bg-white border-b border-gray-200 sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">


            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center space-x-2">
                <x-application-logo class="h-12 w-auto" />
                <span class="text-2xl font-bold text-blue-600">Nafasi</span>
                <span class="text-xs text-gray-400 hidden sm:inline">— creating space for help to arrive</span>
            </a>

            {{-- Desktop Links --}}
            <div class="hidden md:flex items-center space-x-6">
                <a href="{{ route('home') }}" class="text-sm text-gray-600 hover:text-gray-900">Find Help</a>
                <a href="{{ route('about') }}" class="text-sm text-gray-600 hover:text-gray-900">About Us</a>
                @auth
                    @if (auth()->user()->isFacilityStaff())
                        <a href="{{ url('/facility/dashboard') }}"
                            class="text-sm text-gray-600 hover:text-gray-900">Facility Dashboard</a>
                    @endif

                    @if (auth()->user()->isCoordinator())
                        <a href="{{ url('/coordinator/dashboard') }}"
                            class="text-sm text-gray-600 hover:text-gray-900">Coordinator</a>
                    @endif

                    @if (auth()->user()->isVerificationPartner() || auth()->user()->isPlatformAdmin())
                        <a href="{{ url('/verification/queue') }}"
                            class="text-sm text-gray-600 hover:text-gray-900">Verification</a>
                    @endif
                    @if (auth()->user()->isTenantAdmin())
                        <a href="{{ url('/tenant/dashboard') }}"
                            class="text-sm text-gray-600 hover:text-gray-900">Tenant</a>
                    @endif
                    @if (auth()->user()->isPlatformAdmin())
                        <a href="{{ url('/platform/dashboard') }}"
                            class="text-sm text-gray-600 hover:text-gray-900">Platform</a>

                        <a href="{{ url('/alerts/manage') }}" class="text-sm text-gray-600 hover:text-gray-900">
                            🚨 Missing Persons
                        </a>
                    @endif

                    {{-- User Dropdown --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="flex items-center space-x-1 text-sm text-gray-700 hover:text-gray-900">
                            <span>{{ auth()->user()->name }}</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" @click.outside="open = false"
                            class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border py-1 z-50">
                            <a href="{{ route('profile') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Profile</a>
                            <a href="{{ route('profile.security') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Security</a>
                            <hr class="my-1">
                            <button wire:click="logout"
                                class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                Sign Out
                            </button>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-900">Sign In</a>
                    <a href="{{ url('/tenant/facilities/register') }}"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                        Get Started
                    </a>
                @endauth
            </div>

            {{-- Mobile Hamburger --}}
            <div class="md:hidden" x-data="{ open: false }">
                <button @click="open = !open" class="text-gray-600 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <div x-show="open" @click.outside="open = false"
                    class="absolute left-0 right-0 top-16 bg-white border-b shadow-lg p-4 space-y-3">
                    <a href="{{ route('home') }}" class="block text-sm text-gray-700">Find Help</a>
                    @auth
                        <a href="{{ route('profile') }}" class="block text-sm text-gray-700">Profile</a>
                        <button wire:click="logout" class="block text-sm text-red-600">Sign Out</button>
                    @else
                        <a href="{{ route('login') }}" class="block text-sm text-gray-700">Sign In</a>
                        <a href="{{ url('/tenant/facilities/register') }}"
                            class="block text-sm text-blue-600 font-medium">Get Started</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</nav>
