{{-- resources/views/livewire/about-page.blade.php --}}
<div class="min-h-screen bg-slate-950 text-slate-100 font-sans selection:bg-cyan-500 selection:text-white relative overflow-hidden">
    
    {{-- Ambient Background Glows for 3D Depth --}}
    <div class="absolute top-0 left-1/4 w-96 h-96 bg-blue-600/20 rounded-full blur-[128px] pointer-events-none"></div>
    <div class="absolute top-1/3 right-10 w-[30rem] h-[30rem] bg-indigo-600/15 rounded-full blur-[140px] pointer-events-none"></div>
    <div class="absolute bottom-10 left-10 w-[25rem] h-[25rem] bg-teal-500/15 rounded-full blur-[120px] pointer-events-none"></div>

    {{-- Hero Section --}}
    <header class="relative min-h-[420px] flex items-center justify-center overflow-hidden py-20 px-4 border-b border-slate-800/60">
        {{-- Background Image with Modern Parallax Backdrop --}}
        <div class="absolute inset-0 bg-cover bg-center scale-105 transition-transform duration-1000 opacity-25" style="background-image: url('https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=1200&q=80');"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-slate-950/80 via-slate-950/90 to-slate-950"></div>

        <div class="relative max-w-5xl mx-auto text-center space-y-4 z-10">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-blue-500/10 border border-blue-400/30 text-blue-400 text-xs sm:text-sm font-semibold tracking-wide uppercase backdrop-blur-md shadow-[0_0_20px_rgba(59,130,246,0.3)]">
                <span class="w-2 h-2 rounded-full bg-blue-400 animate-pulse"></span> Healthcare Accessibility
            </span>
            <h1 class="text-4xl sm:text-6xl font-black tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-white via-slate-200 to-blue-300 drop-shadow-sm">
                About Nafasi
            </h1>
            <p class="text-xl sm:text-2xl text-slate-300 font-light max-w-2xl mx-auto leading-relaxed">
                Creating space for help to arrive.
            </p>
        </div>
    </header>

    {{-- Main Container --}}
    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-24 relative z-10">
        
        {{-- Our Mission --}}
        <section class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <div class="lg:col-span-7 space-y-6">
                <div class="inline-block px-3 py-1 rounded-lg bg-teal-500/10 border border-teal-500/20 text-teal-400 text-xs font-semibold uppercase tracking-wider">
                    Our Core Vision
                </div>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">
                    Connecting Communities to <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-400 to-blue-500">Lifesaving Care</span>
                </h2>
                <p class="text-slate-300 text-lg leading-relaxed font-normal">
                    Nafasi connects people in East Africa to the nearest and most appropriate health facility, emergency service, or community responder — <strong class="text-white font-semibold">in seconds, in their own language, on any phone.</strong>
                </p>
                <div class="p-4 rounded-xl bg-slate-900/60 border border-slate-800 text-slate-400 text-base leading-relaxed backdrop-blur-sm shadow-inner">
                    "We believe that knowing where to go in an emergency should not depend on who you know, where you live, or what kind of phone you own."
                </div>
            </div>

            <div class="lg:col-span-5 relative group">
                <div class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-teal-400 rounded-3xl blur opacity-30 group-hover:opacity-60 transition duration-500"></div>
                <div class="relative rounded-2xl overflow-hidden border border-slate-700/60 bg-slate-900 shadow-2xl">
                    <img src="https://images.unsplash.com/photo-1631815589968-fcf08b5c09cf?w=600&q=80" alt="Community health worker assisting patient" class="w-full h-80 sm:h-96 object-cover transform group-hover:scale-105 transition-transform duration-700 ease-out">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent opacity-60"></div>
                </div>
            </div>
        </section>

        {{-- What We Do (3D Glass Cards) --}}
        <section class="space-y-12">
            <div class="text-center space-y-3 max-w-2xl mx-auto">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">What We Do</h2>
                <p class="text-slate-400 text-base">Comprehensive digital health infrastructure tailored for real-time response.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                
                {{-- Card 1 --}}
                <div class="group relative rounded-2xl p-6 bg-slate-900/40 border border-slate-800/80 backdrop-blur-xl hover:border-blue-500/50 hover:bg-slate-900/80 transition-all duration-300 hover:-translate-y-2 hover:shadow-[0_20px_40px_rgba(59,130,246,0.15)] flex flex-col justify-between">
                    <div>
                        <div class="w-14 h-14 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-3xl mb-5 shadow-inner group-hover:scale-110 transition-transform duration-300">
                            🏥
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Find Health Facilities</h3>
                        <p class="text-slate-400 text-sm leading-relaxed">Locate the nearest hospital, clinic, pharmacy, or lab with real-time availability updates.</p>
                    </div>
                </div>

                {{-- Card 2 --}}
                <div class="group relative rounded-2xl p-6 bg-slate-900/40 border border-slate-800/80 backdrop-blur-xl hover:border-red-500/50 hover:bg-slate-900/80 transition-all duration-300 hover:-translate-y-2 hover:shadow-[0_20px_40px_rgba(239,68,68,0.15)] flex flex-col justify-between">
                    <div>
                        <div class="w-14 h-14 rounded-xl bg-red-500/10 border border-red-500/20 flex items-center justify-center text-3xl mb-5 shadow-inner group-hover:scale-110 transition-transform duration-300">
                            🚨
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Emergency Dispatch</h3>
                        <p class="text-slate-400 text-sm leading-relaxed">Report emergencies immediately and alert the nearest fire station, ambulance, or first responder.</p>
                    </div>
                </div>

                {{-- Card 3 --}}
                <div class="group relative rounded-2xl p-6 bg-slate-900/40 border border-slate-800/80 backdrop-blur-xl hover:border-purple-500/50 hover:bg-slate-900/80 transition-all duration-300 hover:-translate-y-2 hover:shadow-[0_20px_40px_rgba(168,85,247,0.15)] flex flex-col justify-between">
                    <div>
                        <div class="w-14 h-14 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-3xl mb-5 shadow-inner group-hover:scale-110 transition-transform duration-300">
                            🤝
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Crisis Support</h3>
                        <p class="text-slate-400 text-sm leading-relaxed">Connect anonymously with trained counselors for mental health crises whenever needed.</p>
                    </div>
                </div>

                {{-- Card 4 --}}
                <div class="group relative rounded-2xl p-6 bg-slate-900/40 border border-slate-800/80 backdrop-blur-xl hover:border-emerald-500/50 hover:bg-slate-900/80 transition-all duration-300 hover:-translate-y-2 hover:shadow-[0_20px_40px_rgba(16,185,129,0.15)] flex flex-col justify-between">
                    <div>
                        <div class="w-14 h-14 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-3xl mb-5 shadow-inner group-hover:scale-110 transition-transform duration-300">
                            🛡️
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Anonymous Reporting</h3>
                        <p class="text-slate-400 text-sm leading-relaxed">Report crimes or abuse safely. End-to-end encrypted; your personal identity is never stored.</p>
                    </div>
                </div>

                {{-- Card 5 --}}
                <div class="group relative rounded-2xl p-6 bg-slate-900/40 border border-slate-800/80 backdrop-blur-xl hover:border-amber-500/50 hover:bg-slate-900/80 transition-all duration-300 hover:-translate-y-2 hover:shadow-[0_20px_40px_rgba(245,158,11,0.15)] flex flex-col justify-between">
                    <div>
                        <div class="w-14 h-14 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-3xl mb-5 shadow-inner group-hover:scale-110 transition-transform duration-300">
                            🏍️
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Community Responders</h3>
                        <p class="text-slate-400 text-sm leading-relaxed">Rapid-response health workers on motorbikes reach critical patients within minutes.</p>
                    </div>
                </div>

                {{-- Card 6 --}}
                <div class="group relative rounded-2xl p-6 bg-slate-900/40 border border-slate-800/80 backdrop-blur-xl hover:border-teal-500/50 hover:bg-slate-900/80 transition-all duration-300 hover:-translate-y-2 hover:shadow-[0_20px_40px_rgba(20,184,166,0.15)] flex flex-col justify-between">
                    <div>
                        <div class="w-14 h-14 rounded-xl bg-teal-500/10 border border-teal-500/20 flex items-center justify-center text-3xl mb-5 shadow-inner group-hover:scale-110 transition-transform duration-300">
                            📅
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Book Appointments</h3>
                        <p class="text-slate-400 text-sm leading-relaxed">Book visits with healthcare specialists and medical facilities directly on the platform.</p>
                    </div>
                </div>

            </div>
        </section>

        {{-- Where We Serve --}}
        <section class="relative rounded-3xl p-8 sm:p-12 overflow-hidden bg-gradient-to-br from-slate-900/90 via-slate-900/60 to-slate-950 border border-slate-800 shadow-[0_20px_50px_rgba(0,0,0,0.5)]">
            <div class="absolute -right-12 -bottom-12 w-64 h-64 bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="relative z-10 max-w-3xl space-y-6">
                <h2 class="text-3xl font-bold text-white tracking-tight">Where We Serve</h2>
                <p class="text-slate-300 leading-relaxed text-lg">
                    Nafasi is engineered specifically for East Africa. Currently operating actively across Kenya, with ongoing initiatives to expand throughout the region. Works seamlessly across basic mobile phones via <span class="text-blue-400 font-semibold">USSD</span> and smartphones via our web application.
                </p>
                
                <div class="flex flex-wrap gap-3 pt-2">
                    <span class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-blue-600 text-white font-medium text-sm shadow-[0_0_20px_rgba(37,99,235,0.4)] border border-blue-400/40">
                        <span>🇰🇪</span> Kenya <span class="text-xs bg-blue-800 px-2 py-0.5 rounded-full uppercase tracking-wider">Active</span>
                    </span>
                    <span class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-slate-800/80 border border-slate-700 text-slate-400 text-sm font-medium">
                        <span>🇹🇿</span> Tanzania <span class="text-xs text-slate-500">(Coming Soon)</span>
                    </span>
                    <span class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-slate-800/80 border border-slate-700 text-slate-400 text-sm font-medium">
                        <span>🇺🇬</span> Uganda <span class="text-xs text-slate-500">(Coming Soon)</span>
                    </span>
                    <span class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-slate-800/80 border border-slate-700 text-slate-400 text-sm font-medium">
                        <span>🇷🇼</span> Rwanda <span class="text-xs text-slate-500">(Coming Soon)</span>
                    </span>
                </div>
            </div>
        </section>

        {{-- How It Works --}}
        <section class="space-y-12">
            <div class="text-center space-y-3 max-w-2xl mx-auto">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">How It Works</h2>
                <p class="text-slate-400 text-base">3 quick steps to get connected with medical aid instantly.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative">
                
                {{-- Step 1 --}}
                <div class="relative bg-slate-900/50 border border-slate-800 p-8 rounded-2xl text-center space-y-4 hover:border-blue-500/40 transition-all duration-300">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-blue-600 to-cyan-400 text-white font-black text-2xl flex items-center justify-center mx-auto shadow-[0_10px_25px_rgba(37,99,235,0.4)]">
                        1
                    </div>
                    <h3 class="text-xl font-bold text-white">Tell us what you need</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Specify what you're looking for in <span class="text-slate-200 font-medium">English</span>, <span class="text-slate-200 font-medium">Swahili</span>, or <span class="text-slate-200 font-medium">Sheng</span>.
                    </p>
                </div>

                {{-- Step 2 --}}
                <div class="relative bg-slate-900/50 border border-slate-800 p-8 rounded-2xl text-center space-y-4 hover:border-blue-500/40 transition-all duration-300">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-500 text-white font-black text-2xl flex items-center justify-center mx-auto shadow-[0_10px_25px_rgba(79,70,229,0.4)]">
                        2
                    </div>
                    <h3 class="text-xl font-bold text-white">We find the right help</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Nafasi instantly queries registered facilities and first responders near your real-time geolocation.
                    </p>
                </div>

                {{-- Step 3 --}}
                <div class="relative bg-slate-900/50 border border-slate-800 p-8 rounded-2xl text-center space-y-4 hover:border-blue-500/40 transition-all duration-300">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-blue-600 to-teal-400 text-white font-black text-2xl flex items-center justify-center mx-auto shadow-[0_10px_25px_rgba(20,184,166,0.4)]">
                        3
                    </div>
                    <h3 class="text-xl font-bold text-white">You choose and go</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Initiate a direct call, receive dynamic directions, or confirm an appointment right from your dashboard.
                    </p>
                </div>

            </div>
        </section>

        {{-- Contact Section --}}
        <footer class="pt-12 pb-8 border-t border-slate-800/80 text-center space-y-4">
            <p class="text-slate-400 text-base">For partnerships, facility registration, or media inquiries:</p>
            <div>
                <a href="mailto:hello@nafasi.health" class="inline-flex items-center gap-2 text-xl font-semibold text-blue-400 hover:text-blue-300 transition-colors duration-200 underline decoration-blue-500/30 underline-offset-8 hover:decoration-blue-400">
                    <span>✉️</span> hello@nafasi.health
                </a>
            </div>
        </footer>

    </main>
</div>