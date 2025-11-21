<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Dynamic Title -->
    <title>{{ $profile->app_name ?? 'Dashboard CRM' }} - @yield('title', 'Dashboard')</title>
    
    <!-- Dynamic Favicon -->
    @if($profile && $profile->favicon_path)
        @if(str_starts_with($profile->favicon_path, 'data:image'))
            <!-- Jika favicon berupa base64 -->
            <link rel="icon" type="image/x-icon" href="{{ $profile->favicon_path }}">
            <link rel="shortcut icon" type="image/x-icon" href="{{ $profile->favicon_path }}">
            <link rel="apple-touch-icon" href="{{ $profile->favicon_path }}">
        @else
            <!-- Jika favicon berupa path file -->
            <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $profile->favicon_path) }}">
            <link rel="shortcut icon" type="image/x-icon" href="{{ asset('storage/' . $profile->favicon_path) }}">
            <link rel="apple-touch-icon" href="{{ asset('storage/' . $profile->favicon_path) }}">
        @endif
    @else
        <!-- Default favicon jika tidak ada -->
        <link rel="icon" type="image/x-icon" href="{{ asset('img/logo.png') }}">
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('img/logo.png') }}">
    @endif
    
    <!-- Meta Description -->
    @if($profile && $profile->description)
        <meta name="description" content="{{ Str::limit($profile->description, 160) }}">
    @endif
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])

    {{-- Di layout utama --}}
    @stack('scripts')

    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .pulse-animation {
            animation: pulse 2s infinite;
        }
        .fade-in {
            animation: fadeIn 0.8s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .container-expanded {
            max-width: 1400px;
        }
    </style>
</head>
<body class="bg-gray-50">
    
    <x-dashboard.nav :profile="$profile"/>

    <div>
        @yield('content')
    </div>

    <x-dashboard.toast />

    <script src="{{ asset('js/geo-chart.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    @if(session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Akses Ditolak',
            text: "{{ session('error') }}",
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'OK',
            timer: 3000,
            timerProgressBar: true
        });
    </script>
    @endif
    
    <!-- Script untuk update favicon dinamis jika berubah -->
    <script>
        // Function untuk update favicon secara real-time
        function updateFavicon(faviconUrl) {
            const existingFavicons = document.querySelectorAll('link[rel="icon"], link[rel="shortcut icon"]');
            existingFavicons.forEach(link => link.remove());
            
            const link = document.createElement('link');
            link.rel = 'icon';
            link.type = 'image/x-icon';
            link.href = faviconUrl;
            document.head.appendChild(link);
            
            const shortcut = document.createElement('link');
            shortcut.rel = 'shortcut icon';
            shortcut.type = 'image/x-icon';
            shortcut.href = faviconUrl;
            document.head.appendChild(shortcut);
        }
        
        // Polling untuk check update profile setiap 30 detik (optional)
        // Uncomment jika ingin auto-refresh favicon tanpa reload halaman
        /*
        setInterval(async () => {
            try {
                const response = await fetch('/api/profile/latest');
                const data = await response.json();
                
                if (data.favicon_path) {
                    const faviconUrl = data.favicon_path.startsWith('data:image') 
                        ? data.favicon_path 
                        : '/storage/' + data.favicon_path;
                    updateFavicon(faviconUrl);
                }
            } catch (error) {
                console.log('Failed to check profile updates');
            }
        }, 30000);
        */
    </script>
</body>
</html>