<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes slideInRight { from { transform: translateX(400px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        @keyframes slideOutRight { from { transform: translateX(0); opacity: 1; } to { transform: translateX(400px); opacity: 0; } }
        @keyframes shake { 0%,100% { transform: translateX(0); } 10%,30%,50%,70%,90% { transform: translateX(-10px);} 20%,40%,60%,80% { transform: translateX(10px);} }
        @keyframes progress { from { width:100%; } to { width:0%; } }
        .toast-enter{animation:slideInRight .4s ease-out forwards;}
        .toast-exit{animation:slideOutRight .3s ease-in forwards;}
        .shake{animation:shake .5s ease-in-out;}
        .progress-bar{animation:progress 4s linear forwards;}
        .spinner{border:2px solid rgba(255,255,255,.3);border-radius:50%;border-top:2px solid white;width:20px;height:20px;animation:spin .8s linear infinite;}
        @keyframes spin{0%{transform:rotate(0)}100%{transform:rotate(360deg)}}
        .fade-in{animation:fadeIn .6s ease-out;}
        @keyframes fadeIn{from{opacity:0;transform:translateY(20px);}to{opacity:1;transform:translateY(0)}}
        .image-slider{position:absolute;inset:0;opacity:0;transition:opacity .6s ease-in-out,transform .6s ease-in-out;}
        .image-slider.active{opacity:1;transform:scale(1);}
        .slide-dot{transition:all .3s ease;cursor:pointer;}
        .slide-dot:hover{background-color:rgba(255,255,255,.8)!important;transform:scale(1.2);}
        .slide-dot.active{background-color:white!important;width:2rem!important;}
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">

<div id="toastContainer" class="fixed top-6 right-6 z-[9999] flex flex-col gap-3" style="position: fixed; top: calc(1.5rem + env(safe-area-inset-top)); right: 1.5rem;"></div>

<div class="flex w-full max-w-6xl mx-auto min-h-[700px] bg-white rounded-3xl shadow-2xl overflow-hidden fade-in">
    <!-- Image Card with Slideshow -->
    <div class="hidden md:flex w-1/2 items-center justify-center bg-gradient-to-br from-indigo-50 to-blue-100">
        <div id="imageSlider" class="relative w-96 h-[520px] rounded-2xl shadow-2xl overflow-hidden bg-white/50 cursor-pointer" style="box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(0, 0, 0, 0.05);">
            
            <div id="electric-image" class="absolute inset-0 pointer-events-none z-20"></div>

            <div class="image-slider active" data-slide="0">
                <img class="w-full h-full object-cover" src="/img/login1.jpeg" alt="Image 1">
            </div>
            <div class="image-slider" data-slide="1">
                <img class="w-full h-full object-cover" src="/img/login2.jpg" alt="Image 2">
            </div>
            <div class="image-slider" data-slide="2">
                <img class="w-full h-full object-cover" src="/img/login4.jpg" alt="Image 3">
            </div>
            
            <div class="absolute inset-0 bg-gradient-to-b from-black/20 via-transparent to-black/90 z-[1]"></div>
            
            <div class="absolute bottom-0 left-0 p-8 text-white z-10">
                <h3 class="text-4xl font-bold tracking-tight" style="text-shadow: 0 4px 12px rgba(0, 0, 0, 0.8), 0 2px 4px rgba(0, 0, 0, 0.6);">Welcome !</h3>
                <p class="text-white mt-3 font-normal text-base" style="text-shadow: 0 2px 8px rgba(0, 0, 0, 0.9), 0 1px 3px rgba(0, 0, 0, 0.7);">Visualize your growth with our dashboard.</p>
            </div>
            
            <div class="absolute bottom-6 right-6 flex gap-2 z-10">
                <div class="slide-dot w-2 h-2 rounded-full bg-white active" data-index="0"></div>
                <div class="slide-dot w-2 h-2 rounded-full bg-white/40" data-index="1"></div>
                <div class="slide-dot w-2 h-2 rounded-full bg-white/40" data-index="2"></div>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="w-full md:w-1/2 flex items-center justify-center">
        <div class="relative w-full max-w-md">
            <div id="electric-login" class="absolute inset-0 pointer-events-none z-10 rounded-2xl"></div>

            <form method="POST" action="{{ route('login.process') }}" id="loginForm"
                class="relative w-full px-10 py-12 bg-white rounded-2xl shadow-lg flex flex-col items-center z-20">
                <h2 class="text-4xl text-gray-900 font-semibold">Sign in</h2>
                @csrf

                <div class="flex items-center gap-4 w-full my-5">
                    <div class="w-full h-px bg-gray-300/90"></div>
                    <p class="text-nowrap text-sm text-gray-400/90">Silahkan Login Terlebih Dahulu</p>
                    <div class="w-full h-px bg-gray-300/90"></div>
                </div>

                <div class="flex items-center w-full bg-transparent border border-gray-300/60 h-12 rounded-full overflow-hidden pl-6 gap-2 mb-2 transition-all duration-300 focus-within:border-indigo-400 focus-within:shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="#6B7280"
                        class="w-5 h-5">
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a8.25 8.25 0 1115 0v.75H4.5v-.75z" />
                    </svg>
                    <input type="text" name="username" id="username" placeholder="Username"
                        class="bg-transparent text-gray-700 placeholder-gray-400 outline-none text-sm w-full h-full"
                        value="{{ old('username') }}" required>
                </div>

                <div class="flex items-center w-full bg-transparent border border-gray-300/60 h-12 rounded-full overflow-hidden pl-6 pr-4 gap-2 mt-4 mb-2 transition-all duration-300 focus-within:border-indigo-400 focus-within:shadow-md">
                    <svg width="13" height="17" viewBox="0 0 13 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M13 8.5c0-.938-.729-1.7-1.625-1.7h-.812V4.25C10.563 1.907 8.74 0 6.5 0S2.438 1.907 2.438 4.25V6.8h-.813C.729 6.8 0 7.562 0 8.5v6.8c0 .938.729 1.7 1.625 1.7h9.75c.896 0 1.625-.762 1.625-1.7zM4.063 4.25c0-1.406 1.093-2.55 2.437-2.55s2.438 1.144 2.438 2.55V6.8H4.061z" fill="#6B7280"/>
                    </svg>
                    <input type="password" name="password" id="password" placeholder="Password"
                        class="bg-transparent text-gray-700 placeholder-gray-400 outline-none text-sm w-full h-full" required>
                    <button type="button" id="togglePassword" class="text-gray-400 hover:text-gray-600 transition-colors focus:outline-none">
                        <svg id="eyeOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <svg id="eyeClosed" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                        </svg>
                    </button>
                </div>

                <div class="w-full flex items-center justify-between mt-6 text-gray-500/80">
                    <div class="flex items-center gap-2">
                        <input class="h-5 w-5 rounded border-gray-300 focus:ring-indigo-400" type="checkbox" id="remember" name="remember">
                        <label class="text-sm" for="remember">Remember me</label>
                    </div>
                    <a class="text-sm underline hover:text-indigo-600 transition-colors" href="{{ route('password.request') }}">Forgot password?</a>
                </div>

                <button type="submit" id="loginBtn"
                    class="mt-8 w-full h-11 rounded-full text-white bg-indigo-500 hover:bg-indigo-600 transition font-semibold shadow-md hover:shadow-lg transform hover:scale-[1.02] active:scale-[0.98] flex items-center justify-center gap-2">
                    <span id="btnText">Login</span>
                    <div id="btnSpinner" class="spinner hidden"></div>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    // Image Slider
    let currentSlide = 0;
    let slideInterval;
    const slides = document.querySelectorAll('.image-slider');
    const dots = document.querySelectorAll('.slide-dot');
    const imageSlider = document.getElementById('imageSlider');
    const totalSlides = slides.length;

    function showSlide(index) {
        slides.forEach(slide => slide.classList.remove('active'));
        dots.forEach(dot => dot.classList.remove('active'));
        slides[index].classList.add('active');
        dots[index].classList.add('active');
        currentSlide = index;
    }

    function nextSlide() {
        currentSlide = (currentSlide + 1) % totalSlides;
        showSlide(currentSlide);
    }

    function startSlideshow() {
        slideInterval = setInterval(nextSlide, 5000);
    }

    function resetSlideshow() {
        clearInterval(slideInterval);
        startSlideshow();
    }

    dots.forEach((dot, index) => {
        dot.addEventListener('click', (e) => {
            e.stopPropagation();
            showSlide(index);
            resetSlideshow();
        });
    });

    imageSlider.addEventListener('click', (e) => {
        if (!e.target.classList.contains('slide-dot')) {
            nextSlide();
            resetSlideshow();
        }
    });

    startSlideshow();

    // Toast
    function showToast(message, type = 'success') {
        const toastContainer = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        const isError = type === 'error';

        const bgClass = isError ? 'bg-red-500' : 'bg-green-500';
        const icon = isError
            ? `<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
               </svg>`
            : `<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
               </svg>`;

        toast.className = `toast-enter min-w-[320px] max-w-[400px] rounded-xl shadow-2xl overflow-hidden ${bgClass}`;
        toast.innerHTML = `
            <div class="flex items-start gap-3 p-4 text-white">
                ${icon}
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-sm">${isError ? 'Login Gagal!' : 'Notifikasi'}</p>
                    <p class="text-sm text-white/95 mt-0.5">${message}</p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-white/80 hover:text-white transition flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="h-1 bg-white/30">
                <div class="progress-bar h-full bg-white/80"></div>
            </div>
        `;

        toastContainer.appendChild(toast);

        setTimeout(() => {
            toast.classList.remove('toast-enter');
            toast.classList.add('toast-exit');
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }

    const form = document.getElementById('loginForm');
    const loginBtn = document.getElementById('loginBtn');
    const btnText = document.getElementById('btnText');
    const btnSpinner = document.getElementById('btnSpinner');

    @if (session('login_success'))
        showToast('{{ session('login_success') }}', 'success');
    @endif

    @if (session('success'))
        showToast('{{ session('success') }}', 'success');
    @endif

    @if ($errors->has('loginError'))
        form.classList.add('shake');
        setTimeout(() => form.classList.remove('shake'), 500);
        showToast('{{ $errors->first('loginError') }}', 'error');
    @endif

    form.addEventListener('submit', function() {
        loginBtn.disabled = true;
        btnText.textContent = 'Logging in...';
        btnSpinner.classList.remove('hidden');
        loginBtn.classList.add('opacity-90', 'cursor-not-allowed');
    });

    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');

    [usernameInput, passwordInput].forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value && this.value.length < 3) {
                this.parentElement.classList.add('border-red-400');
            } else {
                this.parentElement.classList.remove('border-red-400');
            }
        });

        input.addEventListener('input', function() {
            this.parentElement.classList.remove('border-red-400');
        });
    });

    // Toggle Password Visibility
    const togglePassword = document.getElementById('togglePassword');
    const eyeOpen = document.getElementById('eyeOpen');
    const eyeClosed = document.getElementById('eyeClosed');

    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        eyeOpen.classList.toggle('hidden');
        eyeClosed.classList.toggle('hidden');
    });
</script>

</body>
</html>