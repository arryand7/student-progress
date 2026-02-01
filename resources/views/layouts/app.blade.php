<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $appSettings = $appSettings ?? [];
        $appName = data_get($appSettings, 'general.app_name', config('app.name') ?: 'Elite Class Progress Report');
        $appTagline = data_get($appSettings, 'general.app_tagline', 'MA Unggul SABIRA');
        $appDescription = data_get($appSettings, 'general.app_description');
        $appLogo = data_get($appSettings, 'general.app_logo');
    @endphp
    <title>@yield('title', $appName) - {{ $appTagline }}</title>
    @if($appDescription)
        <meta name="description" content="{{ $appDescription }}">
    @endif
    @if($appLogo)
        <link rel="icon" href="{{ asset('storage/' . $appLogo) }}">
    @endif
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Material Symbols -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        [x-cloak] { display: none !important; }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen">
    <div x-data="{ sidebarOpen: false }" class="flex min-h-[100dvh] lg:h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside 
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
            class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-primary-700 to-primary-900 transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0"
        >
            <div class="flex items-center justify-between h-16 px-6 bg-primary-800">
                <div class="flex items-center space-x-3">
                    @if($appLogo)
                        <img src="{{ asset('storage/' . $appLogo) }}" alt="Logo" class="h-10 w-10 object-contain rounded">
                    @else
                        <span class="material-symbols-outlined text-white text-3xl">school</span>
                    @endif
                    <div>
                        <h1 class="text-white font-bold text-lg leading-tight">{{ $appName }}</h1>
                        <p class="text-primary-200 text-xs">{{ $appTagline }}</p>
                    </div>
                </div>
                <button @click="sidebarOpen = false" class="lg:hidden text-white">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            
            <nav class="mt-6 px-4">
                @auth
                    @include('layouts.partials.sidebar-menu')
                @endauth
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navigation -->
            <header class="bg-white shadow-sm border-b border-gray-200 min-h-16 flex items-center justify-between px-4 sm:px-6">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden text-gray-600 mr-4">
                        <span class="material-symbols-outlined">menu</span>
                    </button>
                    <h2 class="text-lg font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h2>
                </div>
                
                <div class="flex items-center space-x-4">
                    @auth
                        <div x-data="{ dropdownOpen: false }" class="relative">
                            <button @click="dropdownOpen = !dropdownOpen" class="flex items-center space-x-3 focus:outline-none">
                                <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center">
                                    <span class="text-primary-700 font-semibold text-sm">
                                        {{ substr(auth()->user()->name, 0, 2) }}
                                    </span>
                                </div>
                                <div class="hidden md:block text-left">
                                    <p class="text-sm font-medium text-gray-700">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500">{{ auth()->user()->roles->first()?->display_name ?? 'User' }}</p>
                                </div>
                                <span class="material-symbols-outlined text-gray-400">expand_more</span>
                            </button>
                            
                            <div 
                                x-show="dropdownOpen" 
                                @click.away="dropdownOpen = false"
                                x-transition
                                class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-100 py-2 z-50"
                            >
                                <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <span class="material-symbols-outlined mr-3 text-gray-400">person</span>
                                    Profile
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        <span class="material-symbols-outlined mr-3">logout</span>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endauth
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6">
                @if(session('impersonator_id'))
                    <div class="mb-6 bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-lg flex items-center">
                        <span class="material-symbols-outlined mr-2 text-amber-600">person_alert</span>
                        Anda sedang impersonasi.
                        <form method="POST" action="{{ route('superadmin.impersonate.stop') }}" class="ml-auto">
                            @csrf
                            <button type="submit" class="px-3 py-1 text-sm bg-amber-200 text-amber-900 rounded hover:bg-amber-300">
                                Hentikan
                            </button>
                        </form>
                    </div>
                @endif

                <!-- Alert Messages -->
                @if(session('success'))
                    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center" x-data="{ show: true }" x-show="show">
                        <span class="material-symbols-outlined mr-2 text-green-500">check_circle</span>
                        {{ session('success') }}
                        <button @click="show = false" class="ml-auto text-green-500 hover:text-green-700">
                            <span class="material-symbols-outlined text-sm">close</span>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center" x-data="{ show: true }" x-show="show">
                        <span class="material-symbols-outlined mr-2 text-red-500">error</span>
                        {{ session('error') }}
                        <button @click="show = false" class="ml-auto text-red-500 hover:text-red-700">
                            <span class="material-symbols-outlined text-sm">close</span>
                        </button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                        <div class="flex items-center mb-2">
                            <span class="material-symbols-outlined mr-2 text-red-500">error</span>
                            <strong>Terjadi kesalahan:</strong>
                        </div>
                        <ul class="list-disc list-inside text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Overlay for mobile sidebar -->
    <div 
        x-cloak
        x-show="sidebarOpen" 
        @click="sidebarOpen = false"
        class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden"
        x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    ></div>

    @stack('scripts')
</body>
</html>
