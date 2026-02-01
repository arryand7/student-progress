<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $appSettings = $appSettings ?? [];
        $appName = data_get($appSettings, 'general.app_name', config('app.name') ?: 'Elite Class Progress Report');
        $appTagline = data_get($appSettings, 'general.app_tagline', 'MA Unggul SABIRA');
        $appDescription = data_get($appSettings, 'general.app_description');
        $appLogo = data_get($appSettings, 'general.app_logo');
    @endphp
    <title>@yield('title', 'Login') - {{ $appTagline }}</title>
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
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Material Symbols -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-primary-700 to-primary-900 min-h-screen flex items-center justify-center p-4">
    @yield('content')
</body>
</html>
