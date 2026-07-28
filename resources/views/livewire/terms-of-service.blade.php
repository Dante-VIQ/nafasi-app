{{-- resources/views/livewire/terms-of-service.blade.php --}}
<div class="min-h-screen bg-gradient-to-b from-blue-50/80 via-white to-sky-50/60 text-slate-700 font-sans selection:bg-teal-200/60 selection:text-teal-900 relative overflow-x-hidden">

    {{-- ============================================================
    AMBIENT DEPTH — expanded to cover full viewport
    ============================================================ --}}
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
        <div class="absolute -top-40 -left-40 w-[800px] h-[800px] bg-teal-200/20 rounded-full blur-[150px]"></div>
        <div class="absolute top-1/3 -right-20 w-[700px] h-[700px] bg-blue-200/15 rounded-full blur-[160px]"></div>
        <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-[1000px] h-[400px] bg-amber-100/20 rounded-full blur-[140px]"></div>
        <div class="absolute top-[15%] left-[8%] w-4 h-4 bg-teal-400/20 rounded-full blur-[2px] animate-pulse" style="animation-duration:6s"></div>
        <div class="absolute top-[30%] right-[12%] w-5 h-5 bg-blue-400/15 rounded-full blur-[3px] animate-pulse" style="animation-duration:8s;animation-delay:2s"></div>
        <div class="absolute bottom-[25%] left-[5%] w-3 h-3 bg-teal-400/20 rounded-full blur-[2px] animate-pulse" style="animation-duration:7s;animation-delay:4s"></div>
        <div class="absolute bottom-[40%] right-[6%] w-4 h-4 bg-amber-300/20 rounded-full blur-[2px] animate-pulse" style="animation-duration:9s;animation-delay:1s"></div>
    </div>

    {{-- ============================================================
    HERO SECTION — full width, with background image overlay
    ============================================================ --}}
    <header class="relative pt-20 pb-12 px-6 border-b border-slate-200/60 z-10 overflow-hidden">
        {{-- Subtle background image for depth --}}
        <div class="absolute inset-0 opacity-[0.04] bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=1400&q=80');"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-white/30 via-white/60 to-transparent"></div>

        <div class="relative max-w-7xl mx-auto text-center space-y-4">
            <span class="inline-flex items-center gap-2.5 px-5 py-2 rounded-full bg-white/70 border border-slate-200/60 text-teal-700/80 text-[11px] sm:text-xs font-medium tracking-[0.2em] uppercase backdrop-blur-xl shadow-[0_8px_20px_-4px_rgba(0,0,0,0.06)] hover:shadow-[0_12px_28px_-6px_rgba(20,120,120,0.15)] hover:-translate-y-0.5 transition-all duration-300 cursor-default">
                <span class="w-1.5 h-1.5 rounded-full bg-teal-400 animate-pulse"></span>
                Legal &amp; Compliance
            </span>
            <h1 class="text-4xl sm:text-5xl md:text-7xl font-light tracking-tight [text-shadow:0_4px_20px_rgba(0,0,0,0.04)]">
                <span class="text-slate-700/90">Terms of</span>
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-teal-600 to-blue-500 drop-shadow-[0_2px_6px_rgba(20,120,120,0.15)]">Use</span>
            </h1>
            <p class="text-sm text-slate-500/80 font-light">Last updated: {{ date('F d, Y') }}</p>
            <div class="flex items-center justify-center gap-4 pt-2">
                <span class="w-16 h-[1px] bg-gradient-to-r from-transparent to-teal-400/40"></span>
                <span class="w-2 h-2 rounded-full bg-teal-400/60"></span>
                <span class="w-16 h-[1px] bg-gradient-to-l from-transparent to-teal-400/40"></span>
            </div>
        </div>
    </header>

    {{-- ============================================================
    MAIN CONTENT — full-width card with 3D tilt
    ============================================================ --}}
    <main class="max-w-7xl mx-auto px-6 py-12 pb-20 relative z-10">

        <div class="group [perspective:800px]">
            <div class="relative bg-white/70 border border-slate-200/60 rounded-3xl p-8 sm:p-12 shadow-[0_8px_30px_-8px_rgba(0,0,0,0.06)] backdrop-blur-sm transition-all duration-500 [transform-style:preserve-3d] group-hover:[transform:rotateX(1.5deg)_rotateY(2deg)_translateY(-4px)] group-hover:shadow-[0_20px_50px_-12px_rgba(0,120,120,0.10)]">

                {{-- Soft glow on hover --}}
                <div class="absolute -inset-0.5 rounded-3xl bg-gradient-to-br from-teal-100/30 to-blue-100/20 opacity-0 group-hover:opacity-100 transition-opacity duration-700 pointer-events-none blur-xl"></div>

                <div class="relative space-y-10 text-slate-700 leading-relaxed text-base sm:text-lg">

                    {{-- Sections with larger text for readability --}}
                    <section>
                        <h2 class="text-2xl font-bold text-slate-800/90 mb-4 flex items-center gap-3">
                            <span class="w-1.5 h-8 rounded-full bg-teal-400/60"></span>
                            1. Introduction
                        </h2>
                        <p>These Terms of Use ("Terms") govern your use of the Nafasi platform ("Platform"), operated by Nafasi Technologies Ltd. ("Nafasi", "we", "us"). By accessing or using the Platform, you agree to be bound by these Terms.</p>
                    </section>

                    <section>
                        <h2 class="text-2xl font-bold text-slate-800/90 mb-4 flex items-center gap-3">
                            <span class="w-1.5 h-8 rounded-full bg-teal-400/60"></span>
                            2. The Platform
                        </h2>
                        <p>Nafasi is an emergency and health services routing platform. It connects users to nearby health facilities, emergency services, and community responders. It does not provide medical advice, diagnosis, or treatment. Always consult a qualified healthcare professional for medical concerns.</p>
                    </section>

                    <section>
                        <h2 class="text-2xl font-bold text-slate-800/90 mb-4 flex items-center gap-3">
                            <span class="w-1.5 h-8 rounded-full bg-teal-400/60"></span>
                            3. Software Licensing
                        </h2>
                        <p>The Nafasi platform, including all source code, algorithms, designs, and documentation, is the exclusive intellectual property of Nafasi Technologies Ltd.</p>
                        <p class="mt-4">The Platform is licensed to:</p>
                        <ul class="list-disc pl-6 space-y-2 mt-3 text-slate-600/90">
                            <li><strong class="text-slate-800">County Governments</strong> — For public health facility routing and emergency coordination within their jurisdiction.</li>
                            <li><strong class="text-slate-800">Healthcare Facilities</strong> — Hospitals, clinics, pharmacies, laboratories, and other registered facilities for patient routing, booking, and congestion management.</li>
                            <li><strong class="text-slate-800">Emergency Service Providers</strong> — Fire stations, ambulance services, and police posts for emergency dispatch coordination.</li>
                            <li><strong class="text-slate-800">Verification Partners</strong> — Authorized organizations that review and verify facility registrations.</li>
                        </ul>
                        <p class="mt-4">Each licensee operates the Platform under a separate license agreement. Licensees own their own data (facility information, patient records, operational data). Nafasi Technologies Ltd. retains all rights to the Platform software itself and may license it to multiple parties simultaneously.</p>
                    </section>

                    <section>
                        <h2 class="text-2xl font-bold text-slate-800/90 mb-4 flex items-center gap-3">
                            <span class="w-1.5 h-8 rounded-full bg-teal-400/60"></span>
                            4. User Accounts
                        </h2>
                        <ul class="list-disc pl-6 space-y-2 text-slate-600/90">
                            <li>Public users may access the core routing features without an account.</li>
                            <li>Facility staff, coordinators, and administrators must create accounts with accurate information.</li>
                            <li>You are responsible for maintaining the confidentiality of your account credentials.</li>
                            <li>Two-factor authentication is required for all staff and administrator accounts.</li>
                            <li>Nafasi reserves the right to suspend or terminate accounts that violate these Terms.</li>
                        </ul>
                    </section>

                    <section>
                        <h2 class="text-2xl font-bold text-slate-800/90 mb-4 flex items-center gap-3">
                            <span class="w-1.5 h-8 rounded-full bg-teal-400/60"></span>
                            5. Acceptable Use
                        </h2>
                        <p>You agree not to:</p>
                        <ul class="list-disc pl-6 space-y-2 mt-3 text-slate-600/90">
                            <li>Use the Platform for any unlawful purpose.</li>
                            <li>Submit false or misleading information about facilities or emergencies.</li>
                            <li>Attempt to access data belonging to other facilities or users without authorization.</li>
                            <li>Reverse-engineer, copy, or modify the Platform software.</li>
                            <li>Use the Platform to harass, harm, or intimidate others.</li>
                            <li>Interfere with the operation of the Platform or its underlying infrastructure.</li>
                        </ul>
                    </section>

                    <section>
                        <h2 class="text-2xl font-bold text-slate-800/90 mb-4 flex items-center gap-3">
                            <span class="w-1.5 h-8 rounded-full bg-teal-400/60"></span>
                            6. Disclaimer of Warranties
                        </h2>
                        <p>The Platform is provided "as is" and "as available." Nafasi does not guarantee that:</p>
                        <ul class="list-disc pl-6 space-y-2 mt-3 text-slate-600/90">
                            <li>The Platform will always be available or error-free.</li>
                            <li>Routing recommendations will always be accurate or appropriate for every situation.</li>
                            <li>Facilities listed on the Platform will have capacity or be able to treat every condition.</li>
                        </ul>
                        <p class="mt-4"><strong class="text-slate-800">Medical Disclaimer:</strong> Nafasi is a routing tool, not a medical device. It does not provide medical advice. In a life-threatening emergency, always call your local emergency number (999 in Kenya) immediately.</p>
                    </section>

                    <section>
                        <h2 class="text-2xl font-bold text-slate-800/90 mb-4 flex items-center gap-3">
                            <span class="w-1.5 h-8 rounded-full bg-teal-400/60"></span>
                            7. Limitation of Liability
                        </h2>
                        <p>To the fullest extent permitted by law, Nafasi Technologies Ltd. shall not be liable for any indirect, incidental, special, or consequential damages arising from your use of the Platform, including but not limited to delays in emergency response, incorrect routing, or unavailability of facilities.</p>
                    </section>

                    <section>
                        <h2 class="text-2xl font-bold text-slate-800/90 mb-4 flex items-center gap-3">
                            <span class="w-1.5 h-8 rounded-full bg-teal-400/60"></span>
                            8. Intellectual Property
                        </h2>
                        <p>All trademarks, service marks, logos, and trade names displayed on the Platform are the property of Nafasi Technologies Ltd. or their respective owners. The Platform software, including all code, databases, and documentation, is protected by copyright and other intellectual property laws.</p>
                        <p class="mt-4"><strong class="text-slate-800">Registered Copyright:</strong> Kenya Copyright Board (KECOBO). International protection under the Berne Convention.</p>
                    </section>

                    <section>
                        <h2 class="text-2xl font-bold text-slate-800/90 mb-4 flex items-center gap-3">
                            <span class="w-1.5 h-8 rounded-full bg-teal-400/60"></span>
                            9. Governing Law
                        </h2>
                        <p>These Terms are governed by the laws of the Republic of Kenya. Any disputes arising from these Terms shall be subject to the exclusive jurisdiction of the courts of Kenya.</p>
                    </section>

                    <section>
                        <h2 class="text-2xl font-bold text-slate-800/90 mb-4 flex items-center gap-3">
                            <span class="w-1.5 h-8 rounded-full bg-teal-400/60"></span>
                            10. Changes to These Terms
                        </h2>
                        <p>We may update these Terms from time to time. Continued use of the Platform after changes constitutes acceptance of the new Terms. Material changes will be communicated via the Platform or email where possible.</p>
                    </section>

                    <section>
                        <h2 class="text-2xl font-bold text-slate-800/90 mb-4 flex items-center gap-3">
                            <span class="w-1.5 h-8 rounded-full bg-teal-400/60"></span>
                            11. Contact
                        </h2>
                        <p>For legal inquiries:</p>
                        <div class="mt-4 space-y-2 text-slate-600/90">
                            <p><strong class="text-slate-800">Email:</strong> <a href="mailto:legal@nafasi.health" class="text-teal-600/80 hover:text-teal-700 transition-colors duration-200">legal@nafasi.health</a></p>
                            <p><strong class="text-slate-800">Address:</strong> Nafasi Technologies Ltd., Kenya</p>
                        </div>
                    </section>

                </div>
            </div>
        </div>

        {{-- ============================================================
        FOOTER — 3D email button
        ============================================================ --}}
        <footer class="mt-16 pt-8 border-t border-slate-200/60 text-center space-y-5">
            <p class="text-slate-500/70 text-sm font-light tracking-wide">For general inquiries or support:</p>
            <div>
                <a href="mailto:hello@nafasi.health"
                   class="inline-flex items-center gap-3 text-xl md:text-2xl font-light text-teal-600/80 hover:text-teal-700/90 transition-all duration-300 group relative [perspective:400px]">
                    <span class="text-slate-400/50 group-hover:text-teal-400/70 transition-colors duration-300">✉</span>
                    hello@nafasi.health
                    <span class="w-6 h-[1px] bg-teal-300/30 group-hover:bg-teal-400/60 transition-all duration-500"></span>
                    <span class="absolute inset-0 rounded-full bg-teal-100/0 group-hover:bg-teal-100/20 -z-10 transition-all duration-300 [transform:translateZ(-10px)] group-active:[transform:translateZ(0px)_scale(0.96)]"></span>
                </a>
            </div>
            <div class="pt-4 flex items-center justify-center gap-4 text-[10px] text-slate-400/60 tracking-[0.15em] uppercase">
                <span>© Nafasi Health</span>
                <span class="w-0.5 h-0.5 rounded-full bg-slate-300/40"></span>
                <span>East Africa</span>
            </div>
        </footer>

    </main>

    {{-- ============================================================
    CUSTOM 3D STYLES
    ============================================================ --}}
    <style>
        .group:active .group-active\:\[transform\:translateZ\(0px\)_scale\(0\.96\)\] {
            transform: translateZ(0px) scale(0.96);
        }
        [transform-style] {
            will-change: transform;
        }
    </style>
</div>