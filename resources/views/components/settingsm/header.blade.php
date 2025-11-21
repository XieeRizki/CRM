<!-- Settings Navigation dengan tema Dashboard -->
<nav class="gradient-bg shadow-lg fixed top-0 left-0 right-0 z-50">
    <div class="container-expanded mx-auto px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Hamburger Menu Button untuk Sidebar -->
                <button id="sidebarToggle" class="text-white hover:text-indigo-300 mr-4 transition-colors">
                    <i class="fas fa-bars text-xl"></i>
                </button>
               @if($profile && $profile->logo_path)
                <div class="flex-shrink-0 flex items-center">
                    <img class="h-9 w-auto" 
                        src="{{ asset('storage/' . $profile->logo_path) }}" 
                        alt="{{ $profile->company_name ?? 'Company Logo' }}"
                        onerror="this.style.display='none'">
                </div>
            @else
                <!-- Fallback jika tidak ada logo -->
                <div class="flex-shrink-0 flex items-center">
                    <div class="h-9 w-9 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-building text-white text-lg"></i>
                    </div>
                </div>
            @endif
                <!-- Settings Navigation Links -->
                <div class="hidden md:flex items-center space-x-6 ml-8">
                    <a href="{{ route('user') }}" class="text-white hover:text-indigo-300 px-3 py-2 rounded-md text-sm font-medium transition duration-200 flex items-center space-x-2 {{ request()->routeIs('user') ? 'bg-white/20' : '' }}">
                        <i class="fas fa-users text-sm"></i>
                        <span>User</span>
                    </a>
                    <a href="{{ route('role') }}" class="text-white hover:text-indigo-300 px-3 py-2 rounded-md text-sm font-medium transition duration-200 flex items-center space-x-2 {{ request()->routeIs('role') ? 'bg-white/20' : '' }}">
                        <i class="fas fa-user-shield text-sm"></i>
                        <span>Role</span>
                    </a>
                    <a href="{{ route('menu') }}" class="text-white hover:text-indigo-300 px-3 py-2 rounded-md text-sm font-medium transition duration-200 flex items-center space-x-2 {{ request()->routeIs('menu') ? 'bg-white/20' : '' }}">
                        <i class="fas fa-list text-sm"></i>
                        <span>Menu</span>
                    </a>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <!-- Notifications -->
                <div class="relative">
                    <button class="text-white text-lg hover:text-indigo-300 transition-colors" onclick="toggleNotifications()">
                        <i class="fas fa-bell"></i>
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center">
                            3
                        </span>
                    </button>
                    
                    <!-- Dropdown Notifications -->
                    <div id="notificationDropdown" class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg z-50 hidden">
                        <div class="py-2">
                            <div class="px-4 py-2 border-b border-gray-200">
                                <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
                            </div>
                            <div class="max-h-64 overflow-y-auto">
                                <div class="px-4 py-3 hover:bg-gray-50 bg-blue-50">
                                    <p class="text-sm text-gray-900">New user created</p>
                                    <p class="text-xs text-gray-500 mt-1">2 minutes ago</p>
                                </div>
                                <div class="px-4 py-3 hover:bg-gray-50 bg-blue-50">
                                    <p class="text-sm text-gray-900">Role permissions updated</p>
                                    <p class="text-xs text-gray-500 mt-1">1 hour ago</p>
                                </div>
                                <div class="px-4 py-3 hover:bg-gray-50">
                                    <p class="text-sm text-gray-900">Menu configuration saved</p>
                                    <p class="text-xs text-gray-500 mt-1">3 hours ago</p>
                                </div>
                            </div>
                            <div class="px-4 py-2 border-t border-gray-200">
                                <a href="#" class="text-sm text-indigo-600 hover:text-indigo-800">
                                    View all notifications
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Info and Logout -->
                <div class="flex items-center space-x-3">
                    <div class="hidden sm:flex items-center space-x-2">
                        <img class="h-8 w-8 rounded-full border-2 border-white" 
                            src="{{ asset('img/logo.png') }}" 
                            alt="Profile">
                        <div>
                            <!-- Nama User -->
                            <span class="text-white font-medium block">
                                {{ Auth::user()->username }}
                            </span>
                            <!-- Role User (kecil) -->
                            <span class="text-blue-200 text-xs block">
                                {{ Auth::user()->role->role_name }}
                            </span>
                        </div>
                    </div>
            </div>
        </div>
        
        <!-- Mobile Navigation Links -->
        <div class="md:hidden pb-3">
            <div class="flex space-x-4">
                <a href="{{ route('user') }}" class="text-white hover:text-yellow-300 px-3 py-2 rounded-md text-sm font-medium transition duration-200 flex items-center space-x-2 {{ request()->routeIs('user') ? 'bg-white/20' : '' }}">
                    <i class="fas fa-users text-sm"></i>
                    <span>User</span>
                </a>
                <a href="{{ route('role') }}" class="text-white hover:text-yellow-300 px-3 py-2 rounded-md text-sm font-medium transition duration-200 flex items-center space-x-2 {{ request()->routeIs('role') ? 'bg-white/20' : '' }}">
                    <i class="fas fa-user-shield text-sm"></i>
                    <span>Role</span>
                </a>
                <a href="{{ route('menu') }}" class="text-white hover:text-yellow-300 px-3 py-2 rounded-md text-sm font-medium transition duration-200 flex items-center space-x-2 {{ request()->routeIs('menu') ? 'bg-white/20' : '' }}">
                    <i class="fas fa-list text-sm"></i>
                    <span>Menu</span>
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- Navbar Styles -->
<style>
    .gradient-bg {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .container-expanded {
        max-width: 1400px;
    }
    
    /* Fix untuk mencegah content tertutup navbar */
    body {
        padding-top: 64px; /* Height navbar (16 * 4 = 64px) */
    }
    
    /* Atau bisa gunakan class ini pada container utama */
    .main-content {
        padding-top: 80px; /* Sedikit lebih besar untuk spacing yang nyaman */
    }
</style>

<!-- Navbar JavaScript -->
<script>
// Toggle notifications dropdown
function toggleNotifications() {
    const dropdown = document.getElementById('notificationDropdown');
    dropdown.classList.toggle('hidden');
}

// Close notifications dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('notificationDropdown');
    const notificationButton = event.target.closest('.fa-bell')?.parentElement;
    
    if (!notificationButton && !dropdown.contains(event.target)) {
        dropdown.classList.add('hidden');
    }
});
</script>

<!-- Sidebar Overlay -->
<div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 sidebar-overlay opacity-0 pointer-events-none transition-all duration-300"></div>

<!-- Sidebar -->
<div id="sidebar" class="fixed left-0 top-0 h-full w-80 bg-white shadow-2xl z-40 sidebar closed overflow-y-auto">
    <div class="p-6">
        <!-- Sidebar Header -->
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center space-x-3">
                <img class="h-8 w-8 rounded-full" 
                    src="{{ asset('img/logo.png') }}" 
                    alt="Profile">
                <div>
                    <h3 class="font-semibold text-graysuperadmin User</h3>
                    <p class="text-sm text-graysuperadministrator</p>
                </div>
            </div>
            <button id="sidebarClose" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>


        @php
        use Illuminate\Support\Facades\Auth;
        $currentRoute = request()->route()->getName();
        $user = Auth::user();
        $user->load('role.menus');

        // Kalau superadmin, ambil semua menu dengan children-nya
        $parentMenus = $user->role->role_name === 'superadmin'
            ? \App\Models\Menu::whereNull('parent_id')->with('children')->orderBy('order', 'asc')->get()
            : $user->role->menus->where('pivot.can_view', true)->whereNull('parent_id')->load('children')->sortBy('order');
        @endphp

<nav class="space-y-2">
    @foreach($parentMenus as $menu)
        @if(auth()->user()->canAccess($menu->menu_id, 'view'))
            @php
                // Cek apakah menu ini punya child (pakai relationship)
                $hasChildren = $menu->children->count() > 0;
                
                // Cek apakah salah satu child menu sedang aktif
                $isParentActive = false;
                if($hasChildren) {
                    foreach($menu->children as $child) {
                        if($currentRoute == $child->route) {
                            $isParentActive = true;
                            break;
                        }
                    }
                }
            @endphp
            
            @if($hasChildren)
                <!-- Parent Menu dengan Children -->
                <div class="relative" x-data="{ open: {{ $isParentActive ? 'true' : 'false' }} }">
                    <button @click="open = !open" 
                            class="flex items-center justify-between w-full space-x-3 {{ $isParentActive ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:text-indigo-600 hover:bg-indigo-50' }} rounded-lg px-3 py-2 transition-all duration-200">
                        <div class="flex items-center space-x-3">
                            <i class="{{ $menu->icon ?? 'fas fa-circle' }} w-5"></i>
                            <span class="font-medium">{{ $menu->nama_menu }}</span>
                        </div>
                        <i class="fas fa-chevron-down transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                    </button>
                    
                    <!-- Submenu -->
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-2"
                         class="ml-6 mt-2 space-y-1">
                        @foreach($menu->children as $child)
                            @if(auth()->user()->canAccess($child->menu_id, 'view'))
                                <a href="{{ (!empty($child->route) && Route::has($child->route)) ? route($child->route) : '#' }}"  
                                   class="flex items-center space-x-3 {{ $currentRoute == $child->route ? 'text-indigo-600 bg-indigo-50 border-l-2 border-indigo-600' : 'text-gray-600 hover:text-indigo-600 hover:bg-indigo-50' }} rounded-lg px-3 py-2 transition-all duration-200">
                                    <i class="{{ $child->icon ?? 'fas fa-circle text-xs' }} w-4"></i>
                                    <span class="font-medium">{{ $child->nama_menu }}</span>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @else
                <!-- Single Menu tanpa Children -->
                <a href="{{ (!empty($menu->route) && Route::has($menu->route)) ? route($menu->route) : '#' }}" 
                   class="flex items-center space-x-3 {{ $currentRoute == $menu->route ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:text-indigo-600 hover:bg-indigo-50' }} rounded-lg px-3 py-2 transition-all duration-200">
                    <i class="{{ $menu->icon ?? 'fas fa-circle' }} w-5"></i>
                    <span class="font-medium">{{ $menu->nama_menu }}</span>
                </a>
            @endif
        @endif
    @endforeach
</nav>


        <!-- Sidebar Footer -->
        <div class="absolute bottom-0 left-0 right-0 p-6 bg-gray-50 border-t">
            <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full flex items-center justify-center space-x-2 px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors duration-200">
                <i class="fas fa-sign-out-alt"></i>
                <span class="font-medium">Logout</span>
            </button>
        </form>

        </div>
    </div>
</div>

<!-- Sidebar Styles -->
<style>
    .sidebar-overlay {
        transition: opacity 0.3s ease-in-out;
    }
    .sidebar {
        transition: transform 0.3s ease-in-out;
    }
    .sidebar.open {
        transform: translateX(0);
    }
    .sidebar.closed {
        transform: translateX(-100%);
    }
</style>

<!-- Sidebar JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get elements
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarClose = document.getElementById('sidebarClose');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    // Function to open sidebar
    function openSidebar() {
        sidebar.classList.remove('closed');
        sidebar.classList.add('open');
        sidebarOverlay.classList.remove('opacity-0', 'pointer-events-none');
        sidebarOverlay.classList.add('opacity-100');
        document.body.classList.add('overflow-hidden');
    }

    // Function to close sidebar
    function closeSidebar() {
        sidebar.classList.remove('open');
        sidebar.classList.add('closed');
        sidebarOverlay.classList.remove('opacity-100');
        sidebarOverlay.classList.add('opacity-0', 'pointer-events-none');
        document.body.classList.remove('overflow-hidden');
    }

    // Event listeners
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', openSidebar);
    }
    
    if (sidebarClose) {
        sidebarClose.addEventListener('click', closeSidebar);
    }
    
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', closeSidebar);
    }

    // Close sidebar with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeSidebar();
        }
    });

    // Handle responsive behavior
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1024) {
        }
    });
});
</script>