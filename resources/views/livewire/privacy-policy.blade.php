{{-- resources/views/livewire/privacy-policy.blade.php --}}
<div class="min-h-screen bg-white">
    
    {{-- Hero --}}
    <div class="relative h-48 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1557200134-90327ee9fafa?w=1200&q=80');">
        <div class="absolute inset-0 bg-green-900 bg-opacity-80 flex items-center">
            <div class="max-w-4xl mx-auto px-4">
                <h1 class="text-4xl font-bold text-white">Privacy Policy</h1>
                <p class="text-green-100 mt-2">How we protect your data and your anonymity.</p>
            </div>
        </div>
    </div>

    <div class="max-w-3xl mx-auto px-4 py-12">
        <p class="text-sm text-gray-400 mb-8">Last updated: {{ date('F d, Y') }}</p>

        <div class="space-y-10 text-gray-700 leading-relaxed">
            
            <section>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">1. Our Commitment to Privacy</h2>
                <p>Nafasi Technologies Ltd. operates the Nafasi platform. We collect the minimum data required to route you to help. We never ask for your medical history, national ID, or personal details unless you explicitly provide them for a booking.</p>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">2. What Data We Collect</h2>
                <div class="space-y-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900">Emergency Routing</h3>
                        <p class="text-sm mt-1">Your free-text description of the situation, general location if shared, and language preference. Temporary — not linked to your identity.</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900">Bookings</h3>
                        <p class="text-sm mt-1">Name, phone number, preferred appointment time, and optional reason. Stored in the facility's own database, not in Nafasi's central system.</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900">Crisis Support</h3>
                        <p class="text-sm mt-1">No personal data stored. Conversations encrypted and destroyed after the session.</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900">Anonymous Reporting</h3>
                        <p class="text-sm mt-1">No personal data collected. Reports stored with a random reference number only.</p>
                    </div>
                </div>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">3. How We Use Data</h2>
                <ul class="space-y-2">
                    <li class="flex items-start"><span class="text-green-500 mr-2 mt-1">✓</span> Route you to the nearest appropriate facility or service.</li>
                    <li class="flex items-start"><span class="text-green-500 mr-2 mt-1">✓</span> Send SMS confirmations for bookings and dispatches (only with your phone number).</li>
                    <li class="flex items-start"><span class="text-green-500 mr-2 mt-1">✓</span> Improve routing algorithms using anonymized patterns (never individual data).</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">4. Data Sharing</h2>
                <p>We share data only as necessary with facilities, emergency services, and partner helplines. <strong>We never sell your data.</strong></p>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">5. Data Retention</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="font-medium">Routing queries</p>
                        <p class="text-sm text-gray-500">Destroyed within 24 hours</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="font-medium">Crisis chat messages</p>
                        <p class="text-sm text-gray-500">Destroyed immediately on session end</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="font-medium">Anonymous reports</p>
                        <p class="text-sm text-gray-500">Auto-deleted after 30 days</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="font-medium">Assistance requests</p>
                        <p class="text-sm text-gray-500">Auto-deleted after 24 hours</p>
                    </div>
                </div>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">6. Your Rights</h2>
                <p>You may request access to or deletion of your data from Nafasi's central systems. Facility-held data must be requested from the facility directly. You may use core features anonymously without providing any personal information.</p>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">7. Security</h2>
                <p>All data is encrypted in transit (HTTPS) and at rest. Platform administrators cannot access facility patient records. Regular security audits are conducted.</p>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">8. Contact</h2>
                <p>Email: <a href="mailto:privacy@nafasi.health" class="text-blue-600">privacy@nafasi.health</a></p>
            </section>
        </div>
    </div>
</div>