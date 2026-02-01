<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $appSettings = $appSettings ?? [];
        $appName = data_get($appSettings, 'general.app_name', config('app.name') ?: 'Elite Class Progress Report');
        $appTagline = data_get($appSettings, 'general.app_tagline', 'MA Unggul SABIRA');
        $appDescription = data_get($appSettings, 'general.app_description');
        $appLogo = data_get($appSettings, 'general.app_logo');
    @endphp
    <title>{{ $appName }} - {{ $appTagline }}</title>
    @if($appDescription)
        <meta name="description" content="{{ $appDescription }}">
    @endif
    @if($appLogo)
        <link rel="icon" href="{{ asset('storage/' . $appLogo) }}">
    @endif
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />

    <!-- Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Instrument Sans"', 'sans-serif'],
                        serif: ['"Playfair Display"', 'serif'],
                    },
                    colors: {
                        sabira: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            900: '#14532d', // Deep Green for Islamic School identity
                        },
                        elite: {
                            gold: '#d4af37', // Metallic Gold
                            blue: '#0f172a', // Slate 900
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .hero-pattern {
            background-color: #ffffff;
            opacity: 0.8;
            background-image:  linear-gradient(#f1f5f9 2px, transparent 2px), linear-gradient(90deg, #f1f5f9 2px, transparent 2px);
            background-size: 80px 80px;
        }
    </style>
</head>
<body class="antialiased text-slate-800 bg-white font-sans">

    <!-- 2. Header Section -->
    <header class="fixed top-0 w-full z-50 bg-white/90 backdrop-blur-md border-b border-slate-100 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center gap-3">
                    <!-- Logo -->
                    <div class="w-10 h-10 bg-sabira-900 rounded-lg flex items-center justify-center text-white font-serif font-bold text-xl shadow-lg overflow-hidden">
                        @if($appLogo)
                            <img src="{{ asset('storage/' . $appLogo) }}" alt="Logo" class="h-8 w-8 object-contain">
                        @else
                            S
                        @endif
                    </div>
                    <div class="flex flex-col">
                        <span class="font-bold text-slate-900 leading-tight tracking-tight">{{ $appName }}</span>
                        <span class="text-xs text-elite-gold font-medium uppercase tracking-widest">{{ $appTagline }}</span>
                    </div>
                </div>
                
                <a href="{{ route('sso.login') }}" class="group relative inline-flex items-center justify-center px-6 py-2.5 text-sm font-medium text-white transition-all duration-200 bg-slate-900 rounded-full hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-900 shadow-md hover:shadow-lg">
                    <span>Login with School Account</span>
                    <span class="material-symbols-outlined text-[1.25rem] ml-2 group-hover:translate-x-1 transition-transform">login</span>
                </a>
            </div>
        </div>
    </header>

    <!-- 3. Hero Section -->
    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
        <div class="absolute inset-0 -z-10 hero-pattern h-full w-full"></div>
        <div class="absolute inset-0 -z-10 bg-gradient-to-b from-transparent to-white"></div>
        
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-100 text-blue-800 text-xs font-semibold uppercase tracking-wider mb-8 animate-fade-in-up">
                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                Academic Year 2025/2026
            </div>
            
            <h1 class="text-5xl md:text-6xl lg:text-7xl font-serif font-bold text-slate-900 tracking-tight mb-6 leading-tight">
                Monitor Progress. <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-slate-800 to-slate-500 italic">Not Just Scores.</span>
            </h1>
            
            <p class="mt-6 text-xl text-slate-600 max-w-2xl mx-auto leading-relaxed">
                A comprehensive weekly academic progress monitoring system designed exclusively for elite classes at MA Unggul SABIRA.
            </p>
            
            <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="{{ route('sso.login') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 text-base font-bold text-white transition-all duration-200 bg-sabira-900 rounded-full hover:bg-green-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sabira-900 shadow-xl hover:shadow-2xl transform hover:-translate-y-1">
                    Login with School Account
                </a>
            </div>
        </div>
    </section>

    <!-- 4. System Value / Feature Highlights -->
    <section class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-serif font-bold text-slate-900">What This System Provides</h2>
                <div class="w-24 h-1 bg-elite-gold mx-auto mt-4 rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-8 max-w-5xl mx-auto">
                <!-- Feature 1 -->
                <div class="group p-8 rounded-2xl bg-slate-50 border border-slate-100 hover:border-blue-200 hover:shadow-xl transition-all duration-300">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-blue-700 text-3xl">calendar_month</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Weekly Progress Tracking</h3>
                    <p class="text-slate-600 leading-relaxed">Monitor academic performance week by week to identify consistency and gaps early.</p>
                </div>

                <!-- Feature 2 -->
                <div class="group p-8 rounded-2xl bg-slate-50 border border-slate-100 hover:border-purple-200 hover:shadow-xl transition-all duration-300">
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-purple-700 text-3xl">view_column</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Component-Based Evaluation</h3>
                    <p class="text-slate-600 leading-relaxed">Track progress per paper or sub-competency for granular academic insights.</p>
                </div>

                <!-- Feature 3 -->
                <div class="group p-8 rounded-2xl bg-slate-50 border border-slate-100 hover:border-amber-200 hover:shadow-xl transition-all duration-300">
                    <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-amber-700 text-3xl">timer</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Time & Question Metrics</h3>
                    <p class="text-slate-600 leading-relaxed">Analyze efficiency beyond raw scores by correlating time spent with accuracy.</p>
                </div>

                <!-- Feature 4 -->
                <div class="group p-8 rounded-2xl bg-slate-50 border border-slate-100 hover:border-emerald-200 hover:shadow-xl transition-all duration-300">
                    <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-emerald-700 text-3xl">trending_up</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Data-Driven Coaching</h3>
                    <p class="text-slate-600 leading-relaxed">Support academic decisions and interventions with clear, visualized trend data.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. Target Users Section -->
    <section class="py-24 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-serif font-bold text-slate-900">Who Is This System For</h2>
                <p class="mt-4 text-slate-600">Empowering every stakeholder in the academic journey.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- User 1 -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 text-center hover:-translate-y-2 transition-transform duration-300">
                    <div class="w-16 h-16 mx-auto bg-slate-100 rounded-full flex items-center justify-center mb-6">
                        <span class="material-symbols-outlined text-slate-600 text-3xl">school</span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Teachers & Coaches</h3>
                    <p class="text-sm text-slate-500">Input evaluations and monitor progress trends efficiently.</p>
                </div>

                <!-- User 2 -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 text-center hover:-translate-y-2 transition-transform duration-300">
                    <div class="w-16 h-16 mx-auto bg-slate-100 rounded-full flex items-center justify-center mb-6">
                        <span class="material-symbols-outlined text-slate-600 text-3xl">person</span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Students</h3>
                    <p class="text-sm text-slate-500">View personal academic progress securely and track improvements.</p>
                </div>

                <!-- User 3 -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 text-center hover:-translate-y-2 transition-transform duration-300">
                    <div class="w-16 h-16 mx-auto bg-slate-100 rounded-full flex items-center justify-center mb-6">
                        <span class="material-symbols-outlined text-slate-600 text-3xl">person</span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Parent</h3>
                    <p class="text-sm text-slate-500">View student academic progress securely and track improvements.</p>
                </div>

                <!-- User 4 -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 text-center hover:-translate-y-2 transition-transform duration-300">
                    <div class="w-16 h-16 mx-auto bg-slate-100 rounded-full flex items-center justify-center mb-6">
                        <span class="material-symbols-outlined text-slate-600 text-3xl">admin_panel_settings</span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Academic Management</h3>
                    <p class="text-sm text-slate-500">Monitor elite class performance across programs and cohorts.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 6. How It Works -->
    <section class="py-24 bg-white border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div>
                    <h2 class="text-3xl font-serif font-bold text-slate-900 mb-6">How It Works</h2>
                    <p class="text-lg text-slate-600 mb-8">A streamlined process designed to keep focus on academic growth.</p>
                    
                    <div class="space-y-8">
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold">1</div>
                            <div>
                                <h4 class="text-lg font-bold text-slate-900">Login via School SSO</h4>
                                <p class="text-slate-600">Use your official MA Unggul SABIRA account to access the secure portal.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold">2</div>
                            <div>
                                <h4 class="text-lg font-bold text-slate-900">Weekly Academic Evaluation</h4>
                                <p class="text-slate-600">Teachers record scores, time metrics, and performance data after each session.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold">3</div>
                            <div>
                                <h4 class="text-lg font-bold text-slate-900">Progress Visualization</h4>
                                <p class="text-slate-600">The system automatically generates trend lines and insight reports.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-tr from-blue-100 to-amber-100 rounded-2xl transform rotate-3 scale-95 opacity-50"></div>
                    <div class="relative bg-white p-8 rounded-2xl shadow-xl border border-slate-100">
                        <div class="flex items-center space-x-2 mb-6">
                            <div class="w-3 h-3 rounded-full bg-red-400"></div>
                            <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                            <div class="w-3 h-3 rounded-full bg-green-400"></div>
                        </div>
                        <div class="space-y-4">
                            <div class="h-4 bg-slate-100 rounded w-3/4"></div>
                            <div class="h-32 bg-slate-50 rounded border border-slate-100 flex items-end justify-around p-4">
                                <div class="w-8 bg-blue-200 rounded-t h-1/4"></div>
                                <div class="w-8 bg-blue-300 rounded-t h-2/4"></div>
                                <div class="w-8 bg-blue-400 rounded-t h-1/3"></div>
                                <div class="w-8 bg-blue-500 rounded-t h-3/4"></div>
                                <div class="w-8 bg-blue-600 rounded-t h-full"></div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="h-16 bg-slate-50 rounded border border-slate-100"></div>
                                <div class="h-16 bg-slate-50 rounded border border-slate-100"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 7. Footer -->
    <footer class="bg-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-slate-900 font-bold mb-2">MA Unggul SABIRA</p>
            <p class="text-sm text-slate-500 mb-1">Academic Information System</p>
            <p class="text-xs text-slate-400">&copy; {{ date('Y') }} Elite Class Progress Report. By: Ryand Arifriantoni.</p>
        </div>
    </footer>

</body>
</html>
