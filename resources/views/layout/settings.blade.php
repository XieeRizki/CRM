<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Dynamic Title -->
    <title>{{ $profile->app_name ?? 'User Privilege Management' }} - @yield('title', 'Settings')</title>
    
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
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3B82F6',
                        secondary: '#64748B'
                    }
                }
            }
        }
    </script>

    @stack('scripts')
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <x-settingsm.header :profile="$profile"/>
    
    <div>
        <!-- Stats Cards -->
        
    </div>
        
    <main>
        @yield('content')
    </main>

    <script>
        window.rolePermissions = @json($rolePermissions ?? []);
    </script>
   
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
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
    
    <!-- Script untuk update favicon dinamis -->
    <script>
        // Function untuk update favicon secara real-time
        function updateFavicon(faviconUrl) {
            // Remove existing favicons
            const existingFavicons = document.querySelectorAll('link[rel="icon"], link[rel="shortcut icon"], link[rel="apple-touch-icon"]');
            existingFavicons.forEach(link => link.remove());
            
            // Add new favicon
            const link = document.createElement('link');
            link.rel = 'icon';
            link.type = 'image/x-icon';
            link.href = faviconUrl;
            document.head.appendChild(link);
            
            // Add shortcut icon
            const shortcut = document.createElement('link');
            shortcut.rel = 'shortcut icon';
            shortcut.type = 'image/x-icon';
            shortcut.href = faviconUrl;
            document.head.appendChild(shortcut);
            
            // Add apple touch icon
            const apple = document.createElement('link');
            apple.rel = 'apple-touch-icon';
            apple.href = faviconUrl;
            document.head.appendChild(apple);
        }
        
        // Function untuk update page title dinamis
        function updatePageTitle(title) {
            document.title = title;
        }
        
        // Optional: Polling untuk check update profile setiap 30 detik
        // Uncomment jika ingin auto-refresh favicon tanpa reload halaman
        /*
        setInterval(async () => {
            try {
                const response = await fetch('/api/profile/latest');
                const data = await response.json();
                
                if (data.success && data.data) {
                    // Update favicon jika ada
                    if (data.data.favicon_path) {
                        const faviconUrl = data.data.favicon_path.startsWith('data:image') 
                            ? data.data.favicon_path 
                            : '/storage/' + data.data.favicon_path;
                        updateFavicon(faviconUrl);
                    }
                    
                    // Update title jika ada
                    if (data.data.app_name) {
                        const currentPage = document.title.split(' - ')[1] || 'Settings';
                        updatePageTitle(data.data.app_name + ' - ' + currentPage);
                    }
                }
            } catch (error) {
                console.log('Failed to check profile updates');
            }
        }, 30000); // Check setiap 30 detik
        */
    </script>
</body>
</html>