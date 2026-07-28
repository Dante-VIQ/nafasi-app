{{-- resources/views/livewire/about-page.blade.php --}}
<div class="min-h-screen bg-gradient-to-b from-blue-50/80 via-white to-sky-50/60 text-slate-700 font-sans selection:bg-teal-200/60 selection:text-teal-900 relative overflow-x-hidden">

    {{-- ============================================================
    AMBIENT DEPTH — soft, light glows for 3D feel
    ============================================================ --}}
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
        <div class="absolute -top-40 -left-40 w-[600px] h-[600px] bg-teal-200/20 rounded-full blur-[150px]"></div>
        <div class="absolute top-1/3 -right-20 w-[500px] h-[500px] bg-blue-200/15 rounded-full blur-[160px]"></div>
        <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-[800px] h-[300px] bg-amber-100/20 rounded-full blur-[140px]"></div>
        <div class="absolute top-[15%] left-[8%] w-3 h-3 bg-teal-400/20 rounded-full blur-[2px] animate-pulse" style="animation-duration:6s"></div>
        <div class="absolute top-[30%] right-[12%] w-4 h-4 bg-blue-400/15 rounded-full blur-[3px] animate-pulse" style="animation-duration:8s;animation-delay:2s"></div>
        <div class="absolute bottom-[25%] left-[5%] w-2 h-2 bg-teal-400/20 rounded-full blur-[2px] animate-pulse" style="animation-duration:7s;animation-delay:4s"></div>
        <div class="absolute bottom-[40%] right-[6%] w-3 h-3 bg-amber-300/20 rounded-full blur-[2px] animate-pulse" style="animation-duration:9s;animation-delay:1s"></div>
    </div>

    {{-- ============================================================
    HERO — with 3D depth on badge and headline
    ============================================================ --}}
    <header class="relative min-h-[440px] flex items-center justify-center overflow-hidden px-4 pt-12 pb-20 border-b border-slate-200/60 z-10">

        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-cover bg-center opacity-15 scale-105 transition-transform duration-[2s]"
                 style="background-image: url('https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=1400&q=80');">
            </div>
            <div class="absolute inset-0 bg-gradient-to-b from-white/70 via-white/80 to-white/90"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[400px] bg-teal-200/15 rounded-full blur-[120px]"></div>
        </div>

        <div class="relative max-w-6xl mx-auto text-center space-y-5 z-10">
            {{-- 3D badge with perspective and shadow --}}
            <span class="inline-flex items-center gap-2.5 px-5 py-2 rounded-full bg-white/70 border border-slate-200/60 text-teal-700/80 text-[11px] sm:text-xs font-medium tracking-[0.2em] uppercase backdrop-blur-xl shadow-[0_8px_20px_-4px_rgba(0,0,0,0.06)] hover:shadow-[0_12px_28px_-6px_rgba(20,120,120,0.15)] hover:-translate-y-0.5 transition-all duration-300 cursor-default">
                <span class="w-1.5 h-1.5 rounded-full bg-teal-400 animate-pulse"></span>
                Healthcare Accessibility
            </span>

            {{-- Headline with 3D text shadow --}}
            <h1 class="text-5xl sm:text-7xl md:text-8xl font-light tracking-tight leading-[1.05] [text-shadow:0_4px_20px_rgba(0,0,0,0.04)]">
                <span class="text-slate-700/90">About</span>
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-teal-600 to-blue-500 drop-shadow-[0_2px_6px_rgba(20,120,120,0.15)]">Nafasi</span>
            </h1>

            <p class="text-lg sm:text-xl md:text-2xl text-slate-500/80 font-light max-w-xl mx-auto leading-relaxed tracking-wide">
                Creating space for help to arrive.
            </p>

            <div class="flex items-center justify-center gap-4 pt-2">
                <span class="w-12 h-[1px] bg-gradient-to-r from-transparent to-teal-400/40"></span>
                <span class="w-1.5 h-1.5 rounded-full bg-teal-400/60"></span>
                <span class="w-12 h-[1px] bg-gradient-to-l from-transparent to-teal-400/40"></span>
            </div>
        </div>
    </header>

    {{-- ============================================================
    MAIN CONTENT
    ============================================================ --}}
    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-20 space-y-28 relative z-10">

        {{-- ------------------------------------------------------------
        MISSION — with 3D tilt on image card
        ------------------------------------------------------------ --}}
        <section class="grid grid-cols-1 lg:grid-cols-12 gap-12 xl:gap-16 items-center">

            <div class="lg:col-span-6 space-y-7">
                <div class="inline-block px-4 py-1.5 rounded-full bg-teal-50/80 border border-teal-200/50 text-teal-700/80 text-[11px] font-medium tracking-[0.2em] uppercase backdrop-blur-sm shadow-[0_2px_8px_rgba(0,0,0,0.02)]">
                    Our Core Vision
                </div>
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-light text-slate-800/95 tracking-tight leading-[1.15]">
                    Connecting Communities to
                    <span class="block bg-clip-text text-transparent bg-gradient-to-r from-teal-600 to-blue-500 font-medium">
                        Lifesaving Care
                    </span>
                </h2>
                <p class="text-slate-600/80 text-lg leading-relaxed font-light max-w-lg">
                    Nafasi connects people in East Africa to the nearest and most appropriate health facility, emergency service, or community responder —
                    <span class="text-slate-800/90 font-medium">in seconds, in their own language, on any phone.</span>
                </p>
                <blockquote class="relative pl-6 border-l-2 border-teal-400/40 py-1 text-slate-500/80 text-base leading-relaxed font-light italic">
                    “We believe that knowing where to go in an emergency should not depend on who you know, where you live, or what kind of phone you own.”
                </blockquote>
            </div>

            {{-- 3D tilt image card with perspective --}}
            <div class="lg:col-span-6 relative group [perspective:800px]">
                <div class="absolute -inset-6 bg-gradient-to-tr from-teal-200/30 to-blue-200/20 rounded-3xl blur-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>

                <div class="relative rounded-2xl overflow-hidden border border-slate-200/60 bg-white shadow-[0_8px_30px_-8px_rgba(0,0,0,0.08)] transition-all duration-700 group-hover:shadow-[0_20px_50px_-12px_rgba(0,120,120,0.15)] group-hover:-translate-y-1.5 [transform-style:preserve-3d] group-hover:[transform:rotateX(2deg)_rotateY(-2deg)_scale(1.02)]">
                    <img src="https://images.unsplash.com/photo-1631815589968-fcf08b5c09cf?w=700&q=80"
                         alt="Community health worker assisting patient"
                         class="w-full h-72 sm:h-96 object-cover transition-transform duration-[1.2s] ease-out group-hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-t from-white/40 via-transparent to-transparent opacity-70"></div>
                </div>
            </div>
        </section>

        {{-- ------------------------------------------------------------
        WHAT WE DO — 3D glass cards with tilt and depth
        ------------------------------------------------------------ --}}
        <section class="space-y-14">
            <div class="text-center space-y-3 max-w-2xl mx-auto">
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-light text-slate-800/95 tracking-tight">What We Do</h2>
                <p class="text-slate-500/70 text-base font-light">Comprehensive digital health infrastructure tailored for real-time response.</p>
                <div class="w-12 h-[1px] bg-teal-400/30 mx-auto"></div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

                @php
                $cards = [
                    ['icon' => '🏥', 'title' => 'Find Health Facilities', 'desc' => 'Locate the nearest hospital, clinic, pharmacy, or lab with real-time availability updates.', 'accent' => 'teal'],
                    ['icon' => '🚨', 'title' => 'Emergency Dispatch', 'desc' => 'Report emergencies immediately and alert the nearest fire station, ambulance, or first responder.', 'accent' => 'rose'],
                    ['icon' => '🤝', 'title' => 'Crisis Support', 'desc' => 'Connect anonymously with trained counselors for mental health crises whenever needed.', 'accent' => 'indigo'],
                    ['icon' => '🛡️', 'title' => 'Anonymous Reporting', 'desc' => 'Report crimes or abuse safely. End-to-end encrypted; your personal identity is never stored.', 'accent' => 'emerald'],
                    ['icon' => '🏍️', 'title' => 'Community Responders', 'desc' => 'Rapid-response health workers on motorbikes reach critical patients within minutes.', 'accent' => 'orange'],
                    ['icon' => '📅', 'title' => 'Book Appointments', 'desc' => 'Book visits with healthcare specialists and medical facilities directly on the platform.', 'accent' => 'blue'],
                ];
                @endphp

                @foreach ($cards as $card)
                <div class="group [perspective:600px]">
                    <div class="relative rounded-2xl p-7 bg-white/70 border border-slate-200/60 backdrop-blur-sm transition-all duration-500 [transform-style:preserve-3d] group-hover:[transform:rotateX(2deg)_rotateY(3deg)_translateY(-6px)] group-hover:bg-white/90 group-hover:border-{{ $card['accent'] }}-300/70 group-hover:shadow-[0_20px_40px_-12px_rgba(0,0,0,0.12)] flex flex-col h-full">

                        {{-- Soft glow on hover --}}
                        <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-{{ $card['accent'] }}-100/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>

                        {{-- Icon with 3D lift --}}
                        <div class="relative w-14 h-14 rounded-xl bg-white/80 border border-slate-200/60 flex items-center justify-center text-2xl mb-5 transition-all duration-500 group-hover:scale-110 group-hover:border-{{ $card['accent'] }}-300/60 group-hover:bg-{{ $card['accent'] }}-50/80 shadow-[0_2px_8px_rgba(0,0,0,0.02)] group-hover:shadow-[0_8px_20px_-6px_rgba(0,0,0,0.06)]">
                            {{ $card['icon'] }}
                        </div>

                        <h3 class="relative text-lg font-medium text-slate-800/90 mb-2 tracking-tight">{{ $card['title'] }}</h3>
                        <p class="relative text-slate-500/80 text-sm leading-relaxed font-light flex-1">{{ $card['desc'] }}</p>

                        {{-- Decorative corner dot --}}
                        <div class="absolute top-3 right-3 w-1.5 h-1.5 rounded-full bg-{{ $card['accent'] }}-300/30 group-hover:bg-{{ $card['accent'] }}-400/70 transition-colors duration-500"></div>
                    </div>
                </div>
                @endforeach

            </div>
        </section>

        {{-- ------------------------------------------------------------
        WHERE WE SERVE — light panel with 3D inset shadow
        ------------------------------------------------------------ --}}
        <section class="relative rounded-3xl p-8 sm:p-12 overflow-hidden bg-white/60 border border-slate-200/60 shadow-[0_8px_32px_-8px_rgba(0,0,0,0.04)] backdrop-blur-sm hover:shadow-[0_16px_48px_-12px_rgba(0,120,120,0.08)] transition-shadow duration-500">

            <div class="absolute -right-20 -bottom-20 w-72 h-72 bg-teal-200/20 rounded-full blur-[100px] pointer-events-none"></div>
            <div class="absolute -left-20 -top-20 w-56 h-56 bg-blue-200/20 rounded-full blur-[100px] pointer-events-none"></div>

            <div class="relative z-10 max-w-3xl space-y-7">
                <h2 class="text-3xl md:text-4xl font-light text-slate-800/95 tracking-tight">Where We Serve</h2>
                <p class="text-slate-600/80 leading-relaxed text-lg font-light">
                    Nafasi is engineered specifically for East Africa. Currently operating actively across Kenya, with ongoing initiatives to expand throughout the region. Works seamlessly across basic mobile phones via <span class="text-teal-600/80 font-medium">USSD</span> and smartphones via our web application.
                </p>

                {{-- 3D tags with lift --}}
                <div class="flex flex-wrap gap-3 pt-2">
                    <span class="inline-flex items-center gap-2.5 px-5 py-2.5 rounded-full bg-teal-50/80 border border-teal-200/60 text-teal-700/90 text-sm font-medium shadow-[0_2px_8px_rgba(0,0,0,0.02)] backdrop-blur-sm transition-all duration-300 hover:-translate-y-0.5 hover:shadow-[0_8px_20px_-6px_rgba(20,120,120,0.12)] cursor-default">
                        <span>🇰🇪</span> Kenya
                        <span class="text-[10px] bg-teal-100/60 px-2.5 py-0.5 rounded-full uppercase tracking-[0.1em] text-teal-600/80 font-medium">Active</span>
                    </span>
                    <span class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-white/50 border border-slate-200/50 text-slate-500/80 text-sm font-light transition-all duration-300 hover:-translate-y-0.5 hover:shadow-[0_8px_20px_-6px_rgba(0,0,0,0.06)] cursor-default">
                        <span>🇹🇿</span> Tanzania <span class="text-[10px] text-slate-400/60">Coming Soon</span>
                    </span>
                    <span class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-white/50 border border-slate-200/50 text-slate-500/80 text-sm font-light transition-all duration-300 hover:-translate-y-0.5 hover:shadow-[0_8px_20px_-6px_rgba(0,0,0,0.06)] cursor-default">
                        <span>🇺🇬</span> Uganda <span class="text-[10px] text-slate-400/60">Coming Soon</span>
                    </span>
                    <span class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-white/50 border border-slate-200/50 text-slate-500/80 text-sm font-light transition-all duration-300 hover:-translate-y-0.5 hover:shadow-[0_8px_20px_-6px_rgba(0,0,0,0.06)] cursor-default">
                        <span>🇷🇼</span> Rwanda <span class="text-[10px] text-slate-400/60">Coming Soon</span>
                    </span>
                </div>
            </div>
        </section>

        {{-- ------------------------------------------------------------
        HOW IT WORKS — 3D step cards with tilt
        ------------------------------------------------------------ --}}
        <section class="space-y-14">
            <div class="text-center space-y-3 max-w-2xl mx-auto">
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-light text-slate-800/95 tracking-tight">How It Works</h2>
                <p class="text-slate-500/70 text-base font-light">3 quick steps to get connected with medical aid instantly.</p>
                <div class="w-12 h-[1px] bg-teal-400/30 mx-auto"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 relative">

                {{-- Connector line (desktop) with 3D depth --}}
                <div class="hidden md:block absolute top-1/2 left-[16.66%] right-[16.66%] h-[2px] bg-gradient-to-r from-teal-200/60 via-teal-300/80 to-teal-200/60 -translate-y-1/2 shadow-[0_1px_4px_rgba(0,0,0,0.04)]"></div>

                @php
                $steps = [
                    ['num' => '01', 'title' => 'Tell us what you need', 'desc' => 'Specify what you\'re looking for in <span class="text-slate-700/80 font-medium">English</span>, <span class="text-slate-700/80 font-medium">Swahili</span>, or <span class="text-slate-700/80 font-medium">Sheng</span>.', 'grad' => 'from-teal-100/60 to-teal-50/40'],
                    ['num' => '02', 'title' => 'We find the right help', 'desc' => 'Nafasi instantly queries registered facilities and first responders near your real-time geolocation.', 'grad' => 'from-blue-100/60 to-blue-50/40'],
                    ['num' => '03', 'title' => 'You choose and go', 'desc' => 'Initiate a direct call, receive dynamic directions, or confirm an appointment right from your dashboard.', 'grad' => 'from-indigo-100/60 to-indigo-50/40'],
                ];
                @endphp

                @foreach ($steps as $step)
                <div class="group [perspective:600px]">
                    <div class="relative bg-white/70 border border-slate-200/60 rounded-2xl p-8 text-center space-y-4 transition-all duration-500 [transform-style:preserve-3d] group-hover:[transform:rotateX(2deg)_rotateY(3deg)_translateY(-8px)] group-hover:bg-white/90 group-hover:border-teal-300/50 group-hover:shadow-[0_20px_40px_-12px_rgba(0,0,0,0.10)] backdrop-blur-sm">

                        {{-- Step number with 3D lift --}}
                        <div class="relative w-16 h-16 rounded-2xl bg-gradient-to-br {{ $step['grad'] }} border border-slate-200/60 flex items-center justify-center mx-auto text-2xl font-light text-slate-600/70 tracking-wider transition-all duration-500 group-hover:scale-110 group-hover:border-teal-300/60 group-hover:text-teal-600 shadow-[0_2px_8px_rgba(0,0,0,0.02)] group-hover:shadow-[0_8px_24px_-8px_rgba(0,0,0,0.08)]">
                            {{ $step['num'] }}
                        </div>

                        <h3 class="text-lg font-medium text-slate-800/90 tracking-tight">{{ $step['title'] }}</h3>
                        <p class="text-slate-500/80 text-sm leading-relaxed font-light">{!! $step['desc'] !!}</p>
                    </div>
                </div>
                @endforeach

            </div>
        </section>

        {{-- ------------------------------------------------------------
        CONTACT / FOOTER — 3D button with press effect
        ------------------------------------------------------------ --}}
        <footer class="pt-14 pb-6 border-t border-slate-200/60 text-center space-y-5">
            <p class="text-slate-500/70 text-sm font-light tracking-wide">For partnerships, facility registration, or media inquiries:</p>
            <div>
                <a href="mailto:hello@nafasi.health"
                   class="inline-flex items-center gap-3 text-xl md:text-2xl font-light text-teal-600/80 hover:text-teal-700/90 transition-all duration-300 group relative [perspective:400px]">
                    <span class="text-slate-400/50 group-hover:text-teal-400/70 transition-colors duration-300">✉</span>
                    hello@nafasi.health
                    <span class="w-6 h-[1px] bg-teal-300/30 group-hover:bg-teal-400/60 transition-all duration-500"></span>
                    {{-- 3D press effect on click via active --}}
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
    CUSTOM 3D STYLES — optional extra polish
    ============================================================ --}}
    <style>
        /* 3D button press simulation */
        .group:active .group-active\:\[transform\:translateZ\(0px\)_scale\(0\.96\)\] {
            transform: translateZ(0px) scale(0.96);
        }
        /* Extra smoothness for 3D transforms */
        .group [transform-style] {
            will-change: transform;
        }
        /* Glow enhancement on 3D hover */
        .group:hover .group-hover\:shadow-\[0_20px_40px_-12px_rgba\(0\,0\,0\,0\.10\)\] {
            transition: box-shadow 0.4s ease;
        }
    </style>
</div>