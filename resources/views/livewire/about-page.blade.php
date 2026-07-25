{{-- resources/views/livewire/about-page.blade.php --}}
<div class="min-h-screen bg-white">
    
    {{-- Hero with Background Image --}}
    <div class="relative h-64 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=1200&q=80');">
        <div class="absolute inset-0 bg-blue-900 bg-opacity-70 flex items-center">
            <div class="max-w-4xl mx-auto px-4 text-center">
                <h1 class="text-4xl font-bold text-white mb-2">About Nafasi</h1>
                <p class="text-xl text-blue-100">Creating space for help to arrive.</p>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="max-w-4xl mx-auto px-4 py-12 space-y-16">
        
        {{-- Mission --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Our Mission</h2>
                <p class="text-gray-700 leading-relaxed text-lg">
                    Nafasi connects people in East Africa to the nearest and most appropriate 
                    health facility, emergency service, or community responder — in seconds, 
                    in their own language, on any phone.
                </p>
                <p class="text-gray-700 leading-relaxed mt-4">
                    We believe that knowing where to go in an emergency should not depend on 
                    who you know, where you live, or what kind of phone you own.
                </p>
            </div>
            <div class="rounded-xl overflow-hidden shadow-lg">
                <img src="https://images.unsplash.com/photo-1631815589968-fcf08b5c09cf?w=600&q=80" alt="Community health worker" class="w-full h-64 object-cover">
            </div>
        </div>

        {{-- What We Do --}}
        <div>
            <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">What We Do</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-blue-50 rounded-xl p-6 text-center">
                    <span class="text-4xl">🏥</span>
                    <h3 class="font-semibold text-gray-900 mt-3">Find Health Facilities</h3>
                    <p class="text-sm text-gray-600 mt-2">Locate the nearest hospital, clinic, pharmacy, or lab with real-time availability.</p>
                </div>
                <div class="bg-red-50 rounded-xl p-6 text-center">
                    <span class="text-4xl">🚨</span>
                    <h3 class="font-semibold text-gray-900 mt-3">Emergency Dispatch</h3>
                    <p class="text-sm text-gray-600 mt-2">Report emergencies and alert the nearest fire station, ambulance, or responder.</p>
                </div>
                <div class="bg-purple-50 rounded-xl p-6 text-center">
                    <span class="text-4xl">🤝</span>
                    <h3 class="font-semibold text-gray-900 mt-3">Crisis Support</h3>
                    <p class="text-sm text-gray-600 mt-2">Connect anonymously with trained counselors for mental health crises.</p>
                </div>
                <div class="bg-green-50 rounded-xl p-6 text-center">
                    <span class="text-4xl">🛡️</span>
                    <h3 class="font-semibold text-gray-900 mt-3">Anonymous Reporting</h3>
                    <p class="text-sm text-gray-600 mt-2">Report crimes or abuse safely. Your identity is never stored.</p>
                </div>
                <div class="bg-orange-50 rounded-xl p-6 text-center">
                    <span class="text-4xl">🏍️</span>
                    <h3 class="font-semibold text-gray-900 mt-3">Community Responders</h3>
                    <p class="text-sm text-gray-600 mt-2">Trained health workers on motorbikes reach patients in minutes.</p>
                </div>
                <div class="bg-teal-50 rounded-xl p-6 text-center">
                    <span class="text-4xl">📅</span>
                    <h3 class="font-semibold text-gray-900 mt-3">Book Appointments</h3>
                    <p class="text-sm text-gray-600 mt-2">Book appointments at nearby facilities directly through the platform.</p>
                </div>
            </div>
        </div>

        {{-- Where We Serve --}}
        <div class="bg-gray-50 rounded-2xl p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Where We Serve</h2>
            <p class="text-gray-700 leading-relaxed mb-6">
                Nafasi is designed for East Africa. We currently operate in Kenya, with 
                plans to expand across the region. Our platform works everywhere — from 
                city centers to rural villages — because it runs on basic phones (USSD), 
                smartphones (web app), and everything in between.
            </p>
            <div class="flex flex-wrap gap-2">
                <span class="px-4 py-2 bg-blue-600 text-white rounded-full text-sm font-medium">🇰🇪 Kenya</span>
                <span class="px-4 py-2 bg-gray-200 text-gray-600 rounded-full text-sm">🇹🇿 Tanzania (Coming Soon)</span>
                <span class="px-4 py-2 bg-gray-200 text-gray-600 rounded-full text-sm">🇺🇬 Uganda (Coming Soon)</span>
                <span class="px-4 py-2 bg-gray-200 text-gray-600 rounded-full text-sm">🇷🇼 Rwanda (Coming Soon)</span>
            </div>
        </div>

        {{-- How It Works --}}
        <div>
            <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">How It Works</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-2xl mx-auto mb-4">1</div>
                    <h3 class="font-semibold text-gray-900">Tell us what you need</h3>
                    <p class="text-sm text-gray-600 mt-2">Type what you're looking for in English, Swahili, or Sheng.</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-2xl mx-auto mb-4">2</div>
                    <h3 class="font-semibold text-gray-900">We find the right help</h3>
                    <p class="text-sm text-gray-600 mt-2">Nafasi searches all registered facilities near you in real time.</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-2xl mx-auto mb-4">3</div>
                    <h3 class="font-semibold text-gray-900">You choose and go</h3>
                    <p class="text-sm text-gray-600 mt-2">Call, get directions, or book an appointment — all from one screen.</p>
                </div>
            </div>
        </div>

        {{-- Contact --}}
        <div class="text-center py-8 border-t">
            <p class="text-gray-600">For partnerships, facility registration, or media inquiries:</p>
            <a href="mailto:hello@nafasi.health" class="text-blue-600 hover:text-blue-800 font-medium text-lg">
                hello@nafasi.health
            </a>
        </div>
    </div>
</div>