<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Company Profile Form</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen font-['Inter']">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-6xl bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden">
            <!-- Header - Tanpa Ikon Gedung -->
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white p-6 md:p-8">
                <div>
                    <!-- <h1 class="text-2xl md:text-3xl font-bold mb-2">Company Profile</h1> -->
                    <p class="text-indigo-100 text-lg">Company Profile Form</p>
                    <p class="text-indigo-200 text-sm">Isi data perusahaan Anda dengan lengkap</p>
                </div>
            </div>

            <!-- Progress Indicator -->
            <div class="px-6 md:px-8 pt-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Progress Pengisian</span>
                    <span id="progressPercentage" class="text-sm font-bold text-indigo-600">0%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div id="progressBar" class="bg-indigo-600 h-2.5 rounded-full transition-all duration-500" style="width: 0%"></div>
                </div>
                <p id="progressText" class="text-xs text-gray-500 mt-1">Mulai isi form di bawah ini</p>
            </div>

            <!-- Form Content -->
            <form id="companyForm" class="p-6 md:p-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Left Column - Informasi Dasar Perusahaan -->
                    <div class="space-y-6">
                        <!-- Informasi Perusahaan -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-info-circle text-indigo-500 mr-2"></i>
                                Informasi Dasar Perusahaan
                            </h3>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Nama Perusahaan <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <i class="fas fa-building absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                    <input type="text" id="companyName" name="company_name" required
                                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all bg-white"
                                        placeholder="Masukkan nama perusahaan">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    No. Telepon <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <i class="fas fa-phone absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                    <input type="tel" id="companyPhone" name="phone_number" required
                                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all bg-white"
                                        placeholder="+62 812-3456-7890">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Email Perusahaan <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <i class="fas fa-envelope absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                    <input type="email" id="companyEmail" name="support_email" required
                                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all bg-white"
                                        placeholder="company@example.com">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Website Perusahaan <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <i class="fas fa-globe absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                    <input type="url" id="companyWebsite" name="company_website" required
                                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all bg-white"
                                        placeholder="https://example.com">
                                </div>
                            </div>
                        </div>

                        <!-- Deskripsi Perusahaan -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                <i class="fas fa-file-alt text-indigo-500 mr-2"></i>
                                Deskripsi Perusahaan
                            </label>
                            <div class="relative">
                                <i class="fas fa-file-alt absolute left-3 top-4 text-gray-400"></i>
                                <textarea id="companyDescription" name="description" rows="4"
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all bg-white resize-none"
                                    placeholder="Deskripsikan perusahaan Anda..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Branding & Aplikasi -->
                    <div class="space-y-6">
                        <!-- Logo Upload Section -->
                        <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-xl p-6 border-2 border-dashed border-indigo-200">
                            <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                                <i class="fas fa-image text-indigo-500 mr-2"></i>
                                Logo Perusahaan
                            </h3>
                            <div class="flex flex-col items-center text-center">
                                <div class="relative group mb-4">
                                    <div class="w-32 h-32 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-3xl font-bold overflow-hidden cursor-pointer shadow-lg border-4 border-white" id="logoPreview">
                                        <i class="fas fa-image opacity-70"></i>
                                    </div>
                                    <label for="companyLogo" class="absolute inset-0 flex flex-col items-center justify-center bg-black bg-opacity-70 rounded-xl opacity-0 group-hover:opacity-100 transition-all duration-300 cursor-pointer">
                                        <i class="fas fa-image text-white text-xl mb-2"></i>
                                        <span class="text-white text-sm font-medium">Upload Logo</span>
                                    </label>
                                    <input type="file" id="companyLogo" accept="image/*" class="hidden">
                                </div>
                                <p class="text-sm text-gray-600 mb-2">Format: JPG, PNG, GIF. Maksimal: 2MB.</p>
                                <div class="flex items-center text-indigo-600 text-sm bg-indigo-100 px-3 py-1 rounded-full">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    <span>Rekomendasi: 200x200 piksel</span>
                                </div>
                            </div>
                        </div>

                        <!-- Favicon Upload -->
                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-6 border-2 border-dashed border-green-200">
                            <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                                <i class="fas fa-icons text-green-500 mr-2"></i>
                                Favicon Perusahaan
                            </h3>
                            <div class="flex items-center gap-4">
                                <div class="flex-shrink-0">
                                    <div class="relative group">
                                        <div class="w-20 h-20 rounded-lg bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center text-white text-xl font-bold overflow-hidden cursor-pointer shadow border-2 border-white" id="faviconPreview">
                                            <i class="fas fa-image"></i>
                                        </div>
                                        <label for="companyFavicon" class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-70 rounded-lg opacity-0 group-hover:opacity-100 transition-all duration-300 cursor-pointer">
                                            <i class="fas fa-plus text-white"></i>
                                        </label>
                                        <input type="file" id="companyFavicon" accept="image/*" class="hidden">
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-gray-600 mb-3">Favicon perusahaan (ikon website)</p>
                                    <button type="button" onclick="document.getElementById('companyFavicon').click()" 
                                        class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-all duration-300 flex items-center gap-2 shadow-sm">
                                        <i class="fas fa-upload"></i>
                                        Pilih File
                                    </button>
                                    <div id="faviconFileName" class="text-sm text-gray-500 mt-2">Belum ada file yang dipilih</div>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Aplikasi -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2 flex items-center">
                                <i class="fas fa-mobile-alt text-indigo-500 mr-2"></i>
                                Informasi Aplikasi (Opsional)
                            </h3>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Nama Aplikasi
                                </label>
                                <div class="relative">
                                    <i class="fas fa-mobile-alt absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                    <input type="text" id="appName" name="app_name"
                                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all bg-white"
                                        placeholder="Nama aplikasi (opsional)">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Versi
                                </label>
                                <div class="relative">
                                    <i class="fas fa-code-branch absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                    <input type="text" id="version" name="version"
                                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all bg-white"
                                        placeholder="v1.0.0 (opsional)">
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row gap-3 pt-4">
                            <button type="button" onclick="hapusForm()"
                                class="flex-1 px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg text-base font-semibold hover:from-red-700 hover:to-red-800 transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                                <i class="fas fa-trash-alt"></i>
                                Hapus
                            </button>
                            <button type="submit"
                                class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg text-base font-semibold hover:from-blue-700 hover:to-blue-800 transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                                <i class="fas fa-save"></i>
                                Simpan
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="fixed top-5 right-5 px-6 py-4 rounded-xl shadow-lg text-white font-medium z-50 hidden transform translate-x-full transition-transform duration-300">
        <div class="flex items-center">
            <i id="toastIcon" class="fas fa-check-circle mr-3 text-lg"></i>
            <span id="toastMessage">Pesan toast</span>
        </div>
    </div>

    <style>
        input:focus, select:focus, textarea:focus {
            outline: none;
        }

        #logoPreview img, #faviconPreview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.3s ease-out;
        }

        /* Style untuk field yang sudah terisi */
        .field-filled {
            border-color: #10b981 !important;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }
    </style>

    <script>
        let currentLogo = null;
        let currentFavicon = null;

        // Setup CSRF token untuk semua AJAX request
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Fungsi untuk menghitung progress pengisian form
        function calculateProgress() {
            const requiredFields = [
                'companyName', 'companyPhone', 'companyEmail', 'companyWebsite'
            ];
            
            let filledCount = 0;
            requiredFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field && field.value.trim() !== '') {
                    filledCount++;
                }
            });
            
            // Tambah poin untuk logo dan favicon jika sudah diupload
            if (currentLogo) filledCount += 0.5;
            if (currentFavicon) filledCount += 0.5;
            
            // Tambah poin untuk deskripsi jika sudah diisi
            const description = document.getElementById('companyDescription');
            if (description && description.value.trim() !== '') {
                filledCount += 0.5;
            }
            
            // Total maksimal adalah 5.5 (4 field wajib + 0.5 logo + 0.5 favicon + 0.5 deskripsi)
            const progress = Math.min(100, Math.round((filledCount / 5.5) * 100));
            
            // Update progress bar
            const progressBar = document.getElementById('progressBar');
            const progressPercentage = document.getElementById('progressPercentage');
            const progressText = document.getElementById('progressText');
            
            progressBar.style.width = `${progress}%`;
            progressPercentage.textContent = `${progress}%`;
            
            // Update progress text berdasarkan persentase
            if (progress === 0) {
                progressText.textContent = 'Mulai isi form di bawah ini';
            } else if (progress < 50) {
                progressText.textContent = 'Lanjutkan mengisi form';
            } else if (progress < 100) {
                progressText.textContent = 'Hampir selesai!';
            } else {
                progressText.textContent = 'Semua data telah terisi!';
            }
            
            return progress;
        }

        // Fungsi untuk menandai field yang sudah terisi
        function markFilledFields() {
            const allFields = document.querySelectorAll('input, textarea');
            allFields.forEach(field => {
                if (field.value.trim() !== '') {
                    field.classList.add('field-filled');
                } else {
                    field.classList.remove('field-filled');
                }
            });
        }

        // Fungsi untuk menyimpan data ke localStorage
        function saveToLocalStorage() {
            const formData = {
                company_name: document.getElementById('companyName').value,
                phone_number: document.getElementById('companyPhone').value,
                support_email: document.getElementById('companyEmail').value,
                company_website: document.getElementById('companyWebsite').value,
                app_name: document.getElementById('appName').value || null,
                version: document.getElementById('version').value || null,
                description: document.getElementById('companyDescription').value || null,
                logo_path: currentLogo,
                favicon_path: currentFavicon
            };
            
            localStorage.setItem('companyProfileData', JSON.stringify(formData));
            
            // Update progress dan tampilan field
            calculateProgress();
            markFilledFields();
        }

        // Fungsi untuk memuat data dari localStorage
        function loadFromLocalStorage() {
            const savedData = localStorage.getItem('companyProfileData');
            if (savedData) {
                const formData = JSON.parse(savedData);
                
                document.getElementById('companyName').value = formData.company_name || '';
                document.getElementById('companyPhone').value = formData.phone_number || '';
                document.getElementById('companyEmail').value = formData.support_email || '';
                document.getElementById('companyWebsite').value = formData.company_website || '';
                document.getElementById('appName').value = formData.app_name || '';
                document.getElementById('version').value = formData.version || '';
                document.getElementById('companyDescription').value = formData.description || '';
                
                // Load logo jika ada
                if (formData.logo_path) {
                    currentLogo = formData.logo_path;
                    const preview = document.getElementById('logoPreview');
                    preview.innerHTML = `<img src="${formData.logo_path}" alt="Company Logo" class="rounded-xl">`;
                }
                
                // Load favicon jika ada
                if (formData.favicon_path) {
                    currentFavicon = formData.favicon_path;
                    const preview = document.getElementById('faviconPreview');
                    const fileNameDisplay = document.getElementById('faviconFileName');
                    
                    preview.innerHTML = `<img src="${formData.favicon_path}" alt="Company Favicon" class="rounded-lg">`;
                    fileNameDisplay.textContent = 'File tersimpan';
                    fileNameDisplay.className = 'text-sm text-green-600 font-medium mt-2';
                }
                
                // Update progress dan tampilan field
                calculateProgress();
                markFilledFields();
            }
        }

        // Panggil fungsi load saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            loadFromLocalStorage();
            
            // Tambahkan event listener untuk semua input agar tersimpan otomatis
            const inputs = document.querySelectorAll('input, textarea');
            inputs.forEach(input => {
                input.addEventListener('input', saveToLocalStorage);
            });
            
            // Hitung progress awal
            calculateProgress();
        });

        // Company Logo Preview
        document.getElementById('companyLogo').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    showToast('Ukuran file terlalu besar. Maksimal 2MB.', 'error');
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    currentLogo = e.target.result;
                    const preview = document.getElementById('logoPreview');
                    preview.innerHTML = `<img src="${e.target.result}" alt="Company Logo" class="rounded-xl">`;
                    showToast('Logo berhasil diunggah', 'success');
                    saveToLocalStorage(); // Simpan ke localStorage
                };
                reader.readAsDataURL(file);
            }
        });

        // Company Favicon Preview
        document.getElementById('companyFavicon').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 500 * 1024) {
                    showToast('Ukuran file favicon terlalu besar. Maksimal 500KB.', 'error');
                    return;
                }
                
                const img = new Image();
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    img.onload = function() {
                        if (img.width !== img.height) {
                            showToast('Favicon harus berupa gambar persegi (width = height).', 'error');
                            return;
                        }
                        
                        currentFavicon = e.target.result;
                        const preview = document.getElementById('faviconPreview');
                        const fileNameDisplay = document.getElementById('faviconFileName');
                        
                        preview.innerHTML = `<img src="${e.target.result}" alt="Company Favicon" class="rounded-lg">`;
                        fileNameDisplay.textContent = file.name;
                        fileNameDisplay.className = 'text-sm text-green-600 font-medium mt-2';
                        saveToLocalStorage(); // Simpan ke localStorage
                    };
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        // Fungsi untuk menghapus form
        function hapusForm() {
            if (confirm('Apakah Anda yakin ingin menghapus semua data yang telah diisi?')) {
                document.getElementById('companyForm').reset();
                currentLogo = null;
                currentFavicon = null;
                
                // Kembalikan preview ke keadaan awal dengan ikon gambar
                document.getElementById('logoPreview').innerHTML = '<i class="fas fa-image opacity-70"></i>';
                document.getElementById('faviconPreview').innerHTML = '<i class="fas fa-image"></i>';
                document.getElementById('faviconFileName').textContent = 'Belum ada file yang dipilih';
                document.getElementById('faviconFileName').className = 'text-sm text-gray-500 mt-2';
                
                // Hapus data dari localStorage
                localStorage.removeItem('companyProfileData');
                
                // Reset progress dan tampilan field
                calculateProgress();
                markFilledFields();
                
                showToast('Data form telah dihapus', 'info');
            }
        }

        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            const toastIcon = document.getElementById('toastIcon');
            const toastMessage = document.getElementById('toastMessage');
            
            // Set toast style based on type
            if (type === 'success') {
                toast.className = toast.className.replace(/bg-\w+-\d+/, 'bg-green-500');
                toastIcon.className = 'fas fa-check-circle mr-3 text-lg';
            } else if (type === 'error') {
                toast.className = toast.className.replace(/bg-\w+-\d+/, 'bg-red-500');
                toastIcon.className = 'fas fa-exclamation-circle mr-3 text-lg';
            } else {
                toast.className = toast.className.replace(/bg-\w+-\d+/, 'bg-blue-500');
                toastIcon.className = 'fas fa-info-circle mr-3 text-lg';
            }
            
            // Ensure base classes are present
            toast.className = 'fixed top-5 right-5 px-6 py-4 rounded-xl shadow-lg text-white font-medium z-50 transform translate-x-full transition-transform duration-300 ' + 
                             (type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500');
            
            toastMessage.textContent = message;
            toast.classList.remove('hidden');
            
            // Animate in
            setTimeout(() => {
                toast.classList.remove('translate-x-full');
            }, 10);
            
            // Hide after 3 seconds
            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => {
                    toast.classList.add('hidden');
                }, 300);
            }, 3000);
        }

        // Validasi form sebelum pengiriman
        function validateForm() {
            const requiredFields = [
                { id: 'companyName', name: 'Nama Perusahaan' },
                { id: 'companyPhone', name: 'No. Telepon' },
                { id: 'companyEmail', name: 'Email Perusahaan' },
                { id: 'companyWebsite', name: 'Website Perusahaan' }
            ];
            
            let isValid = true;
            let errorMessages = [];
            
            requiredFields.forEach(field => {
                const element = document.getElementById(field.id);
                if (!element.value.trim()) {
                    isValid = false;
                    errorMessages.push(`${field.name} harus diisi`);
                    element.classList.add('border-red-500');
                } else {
                    element.classList.remove('border-red-500');
                }
            });
            
            // Validasi email
            const emailField = document.getElementById('companyEmail');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (emailField.value && !emailRegex.test(emailField.value)) {
                isValid = false;
                errorMessages.push('Format email tidak valid');
                emailField.classList.add('border-red-500');
            }
            
            // Validasi website
            const websiteField = document.getElementById('companyWebsite');
            const urlRegex = /^(https?:\/\/)?([\da-z.-]+)\.([a-z.]{2,6})([/\w .-]*)*\/?$/;
            if (websiteField.value && !urlRegex.test(websiteField.value)) {
                isValid = false;
                errorMessages.push('Format website tidak valid');
                websiteField.classList.add('border-red-500');
            }
            
            if (!isValid) {
                showToast('❌ ' + errorMessages.join(', '), 'error');
            }
            
            return isValid;
        }

        // Handle form submission dengan AJAX
        document.getElementById('companyForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            // Validasi form sebelum pengiriman
            if (!validateForm()) {
                return;
            }

            const formData = {
                company_name: document.getElementById('companyName').value,
                phone_number: document.getElementById('companyPhone').value,
                support_email: document.getElementById('companyEmail').value,
                company_website: document.getElementById('companyWebsite').value,
                app_name: document.getElementById('appName').value || null,
                version: document.getElementById('version').value || null,
                description: document.getElementById('companyDescription').value || null,
                logo_path: currentLogo,
                favicon_path: currentFavicon
            };

            try {
                const response = await fetch('/profiles', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const result = await response.json();

                if (result.success) {
                    showToast('✅ Data perusahaan berhasil disimpan!', 'success');
                    
                    // Data tetap tersimpan di localStorage, tidak perlu reset form
                    // Tampilkan konfirmasi untuk reset form jika diperlukan
                    setTimeout(() => {
                        if (confirm('Data berhasil disimpan! Apakah Anda ingin mengisi form baru?')) {
                            hapusForm();
                        }
                    }, 1000);
                } else {
                    let errorMessage = 'Gagal menyimpan data';
                    if (result.errors) {
                        errorMessage += ': ' + Object.values(result.errors).flat().join(', ');
                    } else if (result.message) {
                        errorMessage += ': ' + result.message;
                    }
                    showToast('❌ ' + errorMessage, 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('❌ Terjadi kesalahan saat mengirim data. Silakan coba lagi.', 'error');
            }
        });
    </script>
</body>
</html>