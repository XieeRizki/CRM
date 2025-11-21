<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $profile->company_name ?? 'INOTAL' }} - About Us</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @if($profile && $profile->favicon_path)
    <link rel="icon" href="{{ asset('storage/' . $profile->favicon_path) }}" type="image/x-icon">
    @endif
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <!-- Modal untuk preview logo full size -->
    <div id="logoModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden transition-opacity duration-300">
        <div class="bg-white rounded-2xl p-6 max-w-2xl mx-4 relative transform transition-all duration-300 scale-95 opacity-0" id="modalContent">
            <button id="closeModal" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 transition-colors">
                <i class="fas fa-times text-2xl"></i>
            </button>
            <div class="text-center">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Logo {{ $profile->company_name ?? 'INOTAL' }}</h3>
                <div class="bg-gray-100 rounded-xl p-4 mb-4">
                    <img id="modalLogo" src="" alt="Company Logo Full Size" class="max-w-full max-h-96 mx-auto rounded-lg">
                </div>
                <button id="downloadLogo" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2 mx-auto">
                    <i class="fas fa-download"></i>
                    Download Logo
                </button>
            </div>
        </div>
    </div>

    <!-- Navigation Bar -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        @if($profile && $profile->logo_path)
                            <img src="{{ asset('storage/' . $profile->logo_path) }}" alt="Logo" class="h-8 w-auto mr-2 cursor-pointer hover:opacity-80 transition-opacity" id="navLogo">
                        @else
                            <i class="fas fa-building text-indigo-600 text-2xl mr-2"></i>
                        @endif
                        <span class="text-xl font-bold text-gray-800">{{ $profile->app_name ?? 'INOTAL' }}</span>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="#" class="text-gray-600 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Home</a>
                    <a href="#" class="bg-indigo-100 text-indigo-700 px-3 py-2 rounded-md text-sm font-medium">About Us</a>
                    <a href="#" class="text-gray-600 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Services</a>
                    <a href="#" class="text-gray-600 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Contact</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="min-h-screen py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl shadow-2xl overflow-hidden mb-12">
                <div class="p-8 md:p-12">
                    <div class="flex flex-col md:flex-row items-center justify-between">
                        <div class="mb-6 md:mb-0 md:mr-8">
                            <h1 class="text-3xl md:text-4xl font-bold mb-4">Tentang {{ $profile->company_name ?? 'INOTAL' }}</h1>
                            <p class="text-indigo-100 text-lg max-w-2xl">
                                {{ $profile->description ? Str::limit($profile->description, 150) : 'Perusahaan teknologi terdepan yang berfokus pada solusi inovatif untuk kebutuhan bisnis modern.' }}
                            </p>
                        </div>
                        <div class="w-32 h-32 bg-white bg-opacity-20 rounded-xl flex items-center justify-center overflow-hidden cursor-pointer hover:bg-opacity-30 transition-all duration-300" id="headerLogo">
                            @if($profile && $profile->logo_path)
                                <img src="{{ asset('storage/' . $profile->logo_path) }}" alt="Company Logo" class="w-full h-full object-cover">
                            @else
                                <i class="fas fa-building text-white text-4xl"></i>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Company Information Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
                <!-- Company Logo & Basic Info -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
                        <div class="flex flex-col items-center text-center mb-6">
                            <div class="w-40 h-40 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-4xl font-bold overflow-hidden shadow-lg border-4 border-white mb-4 cursor-pointer hover:scale-105 transition-transform duration-300 relative group" id="mainLogo">
                                @if($profile && $profile->logo_path)
                                    <img src="{{ asset('storage/' . $profile->logo_path) }}" alt="Company Logo" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 flex items-center justify-center rounded-xl">
                                        <i class="fas fa-expand text-white text-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></i>
                                    </div>
                                @else
                                    <i class="fas fa-building"></i>
                                @endif
                            </div>
                            <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ $profile->company_name ?? 'INOTAL' }}</h2>
                            <p class="text-gray-600">{{ $profile->app_name ?? 'Teknologi & IT' }}</p>
                            @if($profile && $profile->version)
                                <span class="inline-block mt-2 px-3 py-1 bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-full">
                                    {{ $profile->version }}
                                </span>
                            @endif
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center mr-3">
                                    <i class="fas fa-phone text-indigo-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Telepon</p>
                                    <p class="font-medium text-gray-800">{{ $profile->phone_number ?? '+62 812-3456-7890' }}</p>
                                </div>
                            </div>

                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center mr-3">
                                    <i class="fas fa-envelope text-indigo-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Email</p>
                                    <p class="font-medium text-gray-800 break-all">{{ $profile->support_email ?? 'company@example.com' }}</p>
                                </div>
                            </div>

                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center mr-3">
                                    <i class="fas fa-globe text-indigo-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Website</p>
                                    <a href="{{ $profile->company_website ?? 'https://example.com' }}" target="_blank" class="font-medium text-indigo-600 hover:text-indigo-800 break-all">
                                        {{ $profile->company_website ?? 'https://example.com' }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Company Description & Details -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">Profil Perusahaan</h2>
                        
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-700 mb-3">Deskripsi Perusahaan</h3>
                            <p class="text-gray-600 leading-relaxed">
                                {{ $profile->description ?? 'INOTAL adalah perusahaan teknologi yang berfokus pada pengembangan solusi inovatif untuk membantu bisnis mencapai potensi maksimal mereka. Dengan tim yang berpengalaman dan dedikasi tinggi terhadap kualitas, kami menyediakan layanan yang dapat diandalkan dan solusi yang sesuai dengan kebutuhan klien.' }}
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-5 border border-blue-100">
                                <div class="flex items-center mb-3">
                                    <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center mr-3">
                                        <i class="fas fa-rocket text-blue-600"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-800">Misi Kami</h3>
                                </div>
                                <p class="text-gray-600 text-sm">
                                    Memberikan solusi teknologi terbaik yang meningkatkan efisiensi dan produktivitas bisnis klien kami.
                                </p>
                            </div>

                            <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-5 border border-purple-100">
                                <div class="flex items-center mb-3">
                                    <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center mr-3">
                                        <i class="fas fa-eye text-purple-600"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-800">Visi Kami</h3>
                                </div>
                                <p class="text-gray-600 text-sm">
                                    Menjadi mitra teknologi terpercaya bagi perusahaan di seluruh Indonesia.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(!$profile)
            <!-- Warning jika tidak ada data -->
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-8 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-400 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            <strong>Data perusahaan belum tersedia.</strong> Silakan lengkapi data perusahaan di halaman 
                            <a href="{{ route('profile') }}" class="font-semibold underline">Profile Management</a>.
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Team Section -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200 mb-12">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Tim Kami</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="text-center">
                        <div class="w-24 h-24 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 mx-auto mb-4 flex items-center justify-center text-white text-xl font-bold">
                            JD
                        </div>
                        <h3 class="font-semibold text-gray-800">John Doe</h3>
                        <p class="text-sm text-gray-600">CEO & Founder</p>
                    </div>

                    <div class="text-center">
                        <div class="w-24 h-24 rounded-full bg-gradient-to-br from-blue-400 to-cyan-500 mx-auto mb-4 flex items-center justify-center text-white text-xl font-bold">
                            JS
                        </div>
                        <h3 class="font-semibold text-gray-800">Jane Smith</h3>
                        <p class="text-sm text-gray-600">CTO</p>
                    </div>

                    <div class="text-center">
                        <div class="w-24 h-24 rounded-full bg-gradient-to-br from-green-400 to-emerald-500 mx-auto mb-4 flex items-center justify-center text-white text-xl font-bold">
                            RW
                        </div>
                        <h3 class="font-semibold text-gray-800">Robert Wilson</h3>
                        <p class="text-sm text-gray-600">Head of Development</p>
                    </div>

                    <div class="text-center">
                        <div class="w-24 h-24 rounded-full bg-gradient-to-br from-pink-400 to-rose-500 mx-auto mb-4 flex items-center justify-center text-white text-xl font-bold">
                            SJ
                        </div>
                        <h3 class="font-semibold text-gray-800">Sarah Johnson</h3>
                        <p class="text-sm text-gray-600">Marketing Director</p>
                    </div>
                </div>
            </div>

            <!-- Values Section -->
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl shadow-2xl p-8 mb-12">
                <h2 class="text-2xl font-bold mb-8 text-center">Nilai-Nilai Perusahaan</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="w-16 h-16 rounded-full bg-white bg-opacity-20 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-lightbulb text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Inovasi</h3>
                        <p class="text-indigo-100">
                            Kami selalu mencari cara baru dan lebih baik untuk menyelesaikan masalah.
                        </p>
                    </div>

                    <div class="text-center">
                        <div class="w-16 h-16 rounded-full bg-white bg-opacity-20 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-handshake text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Integritas</h3>
                        <p class="text-indigo-100">
                            Kami menjunjung tinggi kejujuran dan transparansi dalam semua aspek bisnis.
                        </p>
                    </div>

                    <div class="text-center">
                        <div class="w-16 h-16 rounded-full bg-white bg-opacity-20 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-users text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Kolaborasi</h3>
                        <p class="text-indigo-100">
                            Kami percaya pada kekuatan kerja sama tim untuk mencapai hasil terbaik.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Call to Action -->
            <div class="text-center">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Tertarik Bekerja Sama dengan Kami?</h2>
                <p class="text-gray-600 mb-6 max-w-2xl mx-auto">
                    Hubungi kami hari ini untuk mendiskusikan bagaimana {{ $profile->company_name ?? 'INOTAL' }} dapat membantu bisnis Anda tumbuh dan berkembang.
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="mailto:{{ $profile->support_email ?? 'info@inotal.com' }}" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg text-base font-semibold hover:from-indigo-700 hover:to-purple-700 transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                        <i class="fas fa-envelope"></i>
                        Hubungi Kami
                    </a>
                    <button class="px-6 py-3 border border-gray-300 rounded-lg text-base font-semibold text-gray-700 hover:bg-gray-50 transition-all duration-300 flex items-center justify-center gap-2">
                        <i class="fas fa-file-alt"></i>
                        Lihat Portofolio
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">{{ $profile->company_name ?? 'INOTAL' }}</h3>
                    <p class="text-gray-400">
                        {{ $profile->description ? Str::limit($profile->description, 100) : 'Perusahaan teknologi terdepan yang menyediakan solusi inovatif untuk bisnis modern.' }}
                    </p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Tautan Cepat</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Beranda</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Tentang Kami</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Layanan</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Portofolio</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Kontak</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li class="flex items-center">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            <span>Jl. Teknologi No. 123, Jakarta</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone mr-2"></i>
                            <span>{{ $profile->phone_number ?? '+62 812-3456-7890' }}</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-2"></i>
                            <span>{{ $profile->support_email ?? 'info@inotal.com' }}</span>
                        </li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Ikuti Kami</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center hover:bg-indigo-600 transition-colors">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center hover:bg-indigo-600 transition-colors">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center hover:bg-indigo-600 transition-colors">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center hover:bg-indigo-600 transition-colors">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} {{ $profile->company_name ?? 'INOTAL' }}. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Fungsi untuk menampilkan modal preview logo
        function showLogoModal(logoUrl) {
            const modal = document.getElementById('logoModal');
            const modalContent = document.getElementById('modalContent');
            const modalLogo = document.getElementById('modalLogo');
            
            // Set gambar logo di modal
            modalLogo.src = logoUrl;
            
            // Tampilkan modal dengan animasi
            modal.classList.remove('hidden');
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        // Fungsi untuk menyembunyikan modal
        function hideLogoModal() {
            const modal = document.getElementById('logoModal');
            const modalContent = document.getElementById('modalContent');
            
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        // Event listeners untuk logo yang bisa diklik
        document.addEventListener('DOMContentLoaded', function() {
            const logoUrl = "{{ $profile && $profile->logo_path ? asset('storage/' . $profile->logo_path) : '' }}";
            
            if (logoUrl) {
                // Logo di navigation
                const navLogo = document.getElementById('navLogo');
                if (navLogo) {
                    navLogo.addEventListener('click', function(e) {
                        e.preventDefault();
                        showLogoModal(logoUrl);
                    });
                }

                // Logo di header
                const headerLogo = document.getElementById('headerLogo');
                if (headerLogo) {
                    headerLogo.addEventListener('click', function() {
                        showLogoModal(logoUrl);
                    });
                }

                // Logo utama di card
                const mainLogo = document.getElementById('mainLogo');
                if (mainLogo) {
                    mainLogo.addEventListener('click', function() {
                        showLogoModal(logoUrl);
                    });
                }
            }

            // Event listener untuk tombol close modal
            const closeModal = document.getElementById('closeModal');
            if (closeModal) {
                closeModal.addEventListener('click', hideLogoModal);
            }

            // Event listener untuk klik di luar modal
            const modal = document.getElementById('logoModal');
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        hideLogoModal();
                    }
                });
            }

            // Event listener untuk tombol download
            const downloadBtn = document.getElementById('downloadLogo');
            if (downloadBtn) {
                downloadBtn.addEventListener('click', function() {
                    const link = document.createElement('a');
                    link.href = logoUrl;
                    link.download = 'logo-{{ $profile->company_name ?? "INOTAL" }}.png';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                });
            }

            // Event listener untuk tombol escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    hideLogoModal();
                }
            });
        });
    </script>

    <style>
        /* Animasi untuk modal */
        #modalContent {
            transition: all 0.3s ease-out;
        }

        /* Efek hover untuk logo yang bisa diklik */
        #navLogo, #headerLogo, #mainLogo {
            transition: all 0.3s ease;
        }

        #navLogo:hover, #headerLogo:hover {
            transform: scale(1.05);
        }

        /* Custom scrollbar untuk modal */
        #logoModal::-webkit-scrollbar {
            width: 8px;
        }

        #logoModal::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        #logoModal::-webkit-scrollbar-thumb {
            background: #c5c5c5;
            border-radius: 10px;
        }

        #logoModal::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
</body>
</html>