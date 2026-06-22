<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Akun - Trackerin</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/trackerin_logo.svg') }}">
    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        .letter-tracking {
            letter-spacing: -0.06em !important;
        }
    </style>
</head>

<body class="bg-white-bg text-dark-text min-h-screen font-sans selection:bg-main-blue selection:text-white-pure antialiased overflow-x-hidden flex items-center justify-center relative">
    
    <!-- Aurora Background Glow -->
    <div class="aurora-hero-bg opacity-40"></div>

    <!-- Main Container -->
    <div class="relative z-10 w-full max-w-md px-4 py-12">
        <!-- Back to Home -->
        <a href="/" class="inline-flex items-center space-x-2 text-sm font-semibold text-grey-text hover:text-dark-text transition-colors duration-300 mb-8 group">
            <svg class="w-4 h-4 transform transition-transform duration-300 group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            <span>Kembali ke Dashboard</span>
        </a>

        <!-- Premium Auth Card -->
        <div class="gradient-border-card p-6 sm:p-8 md:p-10 shadow-lg bg-white-pure/80 backdrop-blur-xl border border-dark-text/5">
            <!-- Brand Logo & Header -->
            <div class="text-left mb-8">
                <img src="{{ asset('images/trackerin_logo.svg') }}" alt="Trackerin Logo" class="h-7 w-auto mb-6">
                <h1 class="text-2xl font-bold tracking-tight text-dark-text letter-tracking">
                    Siap capai <span class="font-serif italic font-normal text-main-blue">target belajarmu</span>?
                </h1>
                <p class="text-grey-text text-sm font-medium mt-2">
                    Yuk, gabung dan buat akun Trackerin secara gratis sekarang!
                </p>
            </div>

            @if($errors->any())
                <div class="mb-5 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-500 text-sm font-semibold">
                    <ul class="list-disc pl-5 space-y-1 text-left">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form -->
            <form id="register-form" action="{{ route('register') }}" method="POST" class="space-y-5">
                @csrf

                <!-- Step 1: Registration Fields -->
                <div id="register-fields" class="space-y-5 transition-all duration-300">
                    <!-- Name Input -->
                    <div class="flex flex-col space-y-1.5">
                        <label for="name" class="text-xs font-bold uppercase tracking-wider text-grey-text">Nama Lengkap</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="Nama lengkap Anda"
                            class="w-full bg-white-bg border border-dark-text/10 text-dark-text focus:border-main-blue/80 focus:ring-1 focus:ring-main-blue/80 rounded-xl px-4 py-3 placeholder:text-grey-text transition-all duration-300 outline-none text-sm font-medium">
                        <span id="error-name" class="text-xs font-semibold text-red-500 mt-1 hidden text-left"></span>
                    </div>

                    <!-- Email Input -->
                    <div class="flex flex-col space-y-1.5">
                        <label for="email" class="text-xs font-bold uppercase tracking-wider text-grey-text">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="name@example.com"
                            class="w-full bg-white-bg border border-dark-text/10 text-dark-text focus:border-main-blue/80 focus:ring-1 focus:ring-main-blue/80 rounded-xl px-4 py-3 placeholder:text-grey-text transition-all duration-300 outline-none text-sm font-medium">
                        <span id="error-email" class="text-xs font-semibold text-red-500 mt-1 hidden text-left"></span>
                    </div>

                    <!-- Password Input -->
                    <div class="flex flex-col space-y-1.5">
                        <label for="password" class="text-xs font-bold uppercase tracking-wider text-grey-text">Kata Sandi</label>
                        <input type="password" id="password" name="password" required placeholder="Minimal 8 karakter"
                            class="w-full bg-white-bg border border-dark-text/10 text-dark-text focus:border-main-blue/80 focus:ring-1 focus:ring-main-blue/80 rounded-xl px-4 py-3 placeholder:text-grey-text transition-all duration-300 outline-none text-sm font-medium">
                        <span id="error-password" class="text-xs font-semibold text-red-500 mt-1 hidden text-left"></span>
                    </div>

                    <!-- Password Confirmation Input -->
                    <div class="flex flex-col space-y-1.5">
                        <label for="password_confirmation" class="text-xs font-bold uppercase tracking-wider text-grey-text">Konfirmasi Kata Sandi</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Ulangi kata sandi Anda"
                            class="w-full bg-white-bg border border-dark-text/10 text-dark-text focus:border-main-blue/80 focus:ring-1 focus:ring-main-blue/80 rounded-xl px-4 py-3 placeholder:text-grey-text transition-all duration-300 outline-none text-sm font-medium">
                        <span id="error-password_confirmation" class="text-xs font-semibold text-red-500 mt-1 hidden text-left"></span>
                    </div>

                    <!-- General Register Error -->
                    <div id="general-register-error" class="text-xs font-semibold text-red-500 mt-1 text-center hidden"></div>

                    <!-- Submit Button -->
                    <button type="submit" id="btn-register-submit" class="w-full py-4 px-6 rounded-full bg-dark-text hover:bg-main-blue active:scale-[0.98] transition-all duration-300 text-sm font-bold text-white-pure flex items-center justify-center space-x-2 cursor-pointer">
                        <span>Daftar Sekarang</span>
                        <svg id="register-spinner" class="w-4 h-4 animate-spin text-white-pure hidden" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>

                    <!-- Separator -->
                    <div class="relative flex items-center justify-center my-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-dark-text/5"></div>
                        </div>
                        <span class="relative z-10 px-4 bg-white-pure/80 text-[10px] font-bold uppercase tracking-widest text-grey-text">atau</span>
                    </div>

                    <!-- Google OAuth Login -->
                    <a href="{{ route('google.login') }}" class="w-full py-3.5 px-6 rounded-full border border-dark-text/10 hover:border-dark-text/30 bg-white-pure hover:bg-white-bg text-dark-text font-bold text-sm transition-all duration-300 flex items-center justify-center space-x-3 active:scale-[0.98]">
                        <svg viewBox="0 0 24 24" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" fill="#EA4335"/>
                        </svg>
                        <span>Daftar dengan Google</span>
                    </a>
                </div>

                <!-- Step 2: OTP Verification Form (Hidden initially) -->
                <div id="otp-fields" class="hidden space-y-5 transition-all duration-300">
                    <div class="text-left mb-2">
                        <h2 class="text-xl font-bold text-dark-text letter-tracking">Verifikasi Email Anda</h2>
                        <p class="text-grey-text text-xs font-medium mt-1">
                            Kami telah mengirimkan kode OTP 6-digit ke <span id="display-email" class="font-bold text-main-blue"></span>. Silakan masukkan kode tersebut di bawah ini.
                        </p>
                    </div>

                    <!-- OTP inputs container -->
                    <div class="flex flex-col space-y-2">
                        <label class="text-xs font-bold uppercase tracking-wider text-grey-text text-left">Kode OTP</label>
                        <div class="flex justify-between gap-2" id="otp-inputs">
                            <input type="text" maxlength="1" pattern="[0-9]*" inputmode="numeric" class="w-11 h-11 text-center text-xl font-bold bg-white-bg border border-dark-text/10 text-dark-text focus:border-main-blue/80 focus:ring-1 focus:ring-main-blue/80 rounded-xl transition-all duration-300 outline-none" />
                            <input type="text" maxlength="1" pattern="[0-9]*" inputmode="numeric" class="w-11 h-11 text-center text-xl font-bold bg-white-bg border border-dark-text/10 text-dark-text focus:border-main-blue/80 focus:ring-1 focus:ring-main-blue/80 rounded-xl transition-all duration-300 outline-none" />
                            <input type="text" maxlength="1" pattern="[0-9]*" inputmode="numeric" class="w-11 h-11 text-center text-xl font-bold bg-white-bg border border-dark-text/10 text-dark-text focus:border-main-blue/80 focus:ring-1 focus:ring-main-blue/80 rounded-xl transition-all duration-300 outline-none" />
                            <input type="text" maxlength="1" pattern="[0-9]*" inputmode="numeric" class="w-11 h-11 text-center text-xl font-bold bg-white-bg border border-dark-text/10 text-dark-text focus:border-main-blue/80 focus:ring-1 focus:ring-main-blue/80 rounded-xl transition-all duration-300 outline-none" />
                            <input type="text" maxlength="1" pattern="[0-9]*" inputmode="numeric" class="w-11 h-11 text-center text-xl font-bold bg-white-bg border border-dark-text/10 text-dark-text focus:border-main-blue/80 focus:ring-1 focus:ring-main-blue/80 rounded-xl transition-all duration-300 outline-none" />
                            <input type="text" maxlength="1" pattern="[0-9]*" inputmode="numeric" class="w-11 h-11 text-center text-xl font-bold bg-white-bg border border-dark-text/10 text-dark-text focus:border-main-blue/80 focus:ring-1 focus:ring-main-blue/80 rounded-xl transition-all duration-300 outline-none" />
                        </div>
                        <!-- Hidden input to store full 6-digit OTP -->
                        <input type="hidden" id="otp" name="otp" />
                        <span id="error-otp" class="text-xs font-semibold text-red-500 mt-1 text-left hidden"></span>
                    </div>

                    <!-- Resend & Timer -->
                    <div class="flex items-center justify-between text-xs font-semibold py-2">
                        <button type="button" id="btn-back-to-register" class="text-grey-text hover:text-dark-text transition-colors duration-300 flex items-center space-x-1 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                            </svg>
                            <span>Ubah Data</span>
                        </button>
                        <span id="otp-timer" class="text-grey-text">
                            Kirim ulang dalam <span id="timer-count" class="font-bold text-main-blue">60</span>s
                        </span>
                        <button type="button" id="btn-resend-otp" class="text-main-blue hover:text-hover-blue font-bold hidden transition-colors duration-300 cursor-pointer">
                            Kirim Ulang OTP
                        </button>
                    </div>

                    <!-- Submit Button -->
                    <button type="button" id="btn-verify-otp" class="w-full py-4 px-6 rounded-full bg-dark-text hover:bg-main-blue active:scale-[0.98] transition-all duration-300 text-sm font-bold text-white-pure flex items-center justify-center space-x-2 cursor-pointer">
                        <span>Verifikasi & Daftar</span>
                        <svg id="verify-spinner" class="w-4 h-4 animate-spin text-white-pure hidden" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
            </form>

            <!-- Login Link -->
            <div class="mt-8 pt-6 border-t border-dark-text/5 text-center text-xs font-medium text-grey-text">
                Sudah punya akun? 
                <a href="{{ route('login') }}" class="text-main-blue font-bold hover:underline transition-all">Masuk di sini</a>
            </div>
        </div>
    </div>

    <!-- OTP Execution Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('register-form');
            const registerFields = document.getElementById('register-fields');
            const otpFields = document.getElementById('otp-fields');
            const displayEmail = document.getElementById('display-email');
            const otpInputs = document.querySelectorAll('#otp-inputs input');
            const otpHidden = document.getElementById('otp');
            const btnRegisterSubmit = document.getElementById('btn-register-submit');
            const registerSpinner = document.getElementById('register-spinner');
            const btnVerifyOtp = document.getElementById('btn-verify-otp');
            const verifySpinner = document.getElementById('verify-spinner');
            const btnBackToRegister = document.getElementById('btn-back-to-register');
            const btnResendOtp = document.getElementById('btn-resend-otp');
            const otpTimer = document.getElementById('otp-timer');
            const timerCount = document.getElementById('timer-count');
            
            // Errors
            const errorFields = {
                name: document.getElementById('error-name'),
                email: document.getElementById('error-email'),
                password: document.getElementById('error-password'),
                password_confirmation: document.getElementById('error-password_confirmation'),
                otp: document.getElementById('error-otp'),
                general: document.getElementById('general-register-error')
            };

            let resendCooldown = 60;
            let timerInterval = null;

            function clearErrors() {
                Object.values(errorFields).forEach(el => {
                    if (el) {
                        el.textContent = '';
                        el.classList.add('hidden');
                    }
                });
            }

            function startTimer() {
                resendCooldown = 60;
                otpTimer.classList.remove('hidden');
                btnResendOtp.classList.add('hidden');
                timerCount.textContent = resendCooldown;

                if (timerInterval) clearInterval(timerInterval);
                
                timerInterval = setInterval(() => {
                    resendCooldown--;
                    timerCount.textContent = resendCooldown;
                    if (resendCooldown <= 0) {
                        clearInterval(timerInterval);
                        otpTimer.classList.add('hidden');
                        btnResendOtp.classList.remove('hidden');
                    }
                }, 1000);
            }

            // Step 1: Send OTP & Transition
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                clearErrors();
                
                const name = document.getElementById('name').value;
                const email = document.getElementById('email').value;
                const password = document.getElementById('password').value;
                const password_confirmation = document.getElementById('password_confirmation').value;

                if (password !== password_confirmation) {
                    errorFields.password_confirmation.textContent = 'Konfirmasi kata sandi tidak cocok.';
                    errorFields.password_confirmation.classList.remove('hidden');
                    return;
                }

                btnRegisterSubmit.disabled = true;
                registerSpinner.classList.remove('hidden');

                try {
                    const response = await fetch('/api/v1/register/send-otp', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ email })
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        if (response.status === 422 && data.errors) {
                            if (data.errors.email) {
                                errorFields.email.textContent = data.errors.email[0];
                                errorFields.email.classList.remove('hidden');
                            }
                        } else {
                            errorFields.general.textContent = data.message || 'Gagal mengirim OTP. Silakan coba lagi.';
                            errorFields.general.classList.remove('hidden');
                        }
                    } else {
                        // Success - transition to OTP
                        displayEmail.textContent = email;
                        
                        // Check if GSAP is loaded for animations
                        if (window.gsap) {
                            gsap.to(registerFields, {
                                opacity: 0,
                                y: -20,
                                duration: 0.3,
                                onComplete: () => {
                                    registerFields.classList.add('hidden');
                                    otpFields.classList.remove('hidden');
                                    gsap.fromTo(otpFields, { opacity: 0, y: 20 }, { opacity: 1, y: 0, duration: 0.3 });
                                    otpInputs[0].focus();
                                }
                            });
                        } else {
                            registerFields.classList.add('hidden');
                            otpFields.classList.remove('hidden');
                            otpInputs[0].focus();
                        }
                        
                        startTimer();
                    }
                } catch (error) {
                    console.error(error);
                    errorFields.general.textContent = 'Terjadi kesalahan koneksi. Silakan coba lagi.';
                    errorFields.general.classList.remove('hidden');
                } finally {
                    btnRegisterSubmit.disabled = false;
                    registerSpinner.classList.add('hidden');
                }
            });

            // Resend OTP
            btnResendOtp.addEventListener('click', async () => {
                const email = document.getElementById('email').value;
                btnResendOtp.disabled = true;
                
                try {
                    const response = await fetch('/api/v1/register/send-otp', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ email })
                    });
                    const data = await response.json();

                    if (response.ok) {
                        startTimer();
                    } else {
                        alert(data.message || 'Gagal mengirim ulang OTP.');
                    }
                } catch (error) {
                    console.error(error);
                    alert('Terjadi kesalahan koneksi.');
                } finally {
                    btnResendOtp.disabled = false;
                }
            });

            // Go Back to Register Form
            btnBackToRegister.addEventListener('click', () => {
                clearErrors();
                if (window.gsap) {
                    gsap.to(otpFields, {
                        opacity: 0,
                        y: 20,
                        duration: 0.3,
                        onComplete: () => {
                            otpFields.classList.add('hidden');
                            registerFields.classList.remove('hidden');
                            gsap.fromTo(registerFields, { opacity: 0, y: -20 }, { opacity: 1, y: 0, duration: 0.3 });
                        }
                    });
                } else {
                    otpFields.classList.add('hidden');
                    registerFields.classList.remove('hidden');
                }
            });

            // OTP inputs behavior
            otpInputs.forEach((input, index) => {
                input.addEventListener('input', (e) => {
                    const value = e.target.value;
                    e.target.value = value.replace(/[^0-9]/g, '');

                    if (e.target.value.length === 1 && index < otpInputs.length - 1) {
                        otpInputs[index + 1].focus();
                    }
                    updateHiddenOtp();
                });

                input.addEventListener('keydown', (e) => {
                    if (e.key === 'Backspace' && e.target.value.length === 0 && index > 0) {
                        otpInputs[index - 1].focus();
                    }
                });

                input.addEventListener('paste', (e) => {
                    e.preventDefault();
                    const pasteData = (e.clipboardData || window.clipboardData).getData('text').trim();
                    if (/^\d{6}$/.test(pasteData)) {
                        for (let i = 0; i < 6; i++) {
                            otpInputs[i].value = pasteData[i];
                        }
                        updateHiddenOtp();
                        btnVerifyOtp.focus();
                        btnVerifyOtp.click();
                    }
                });

                input.addEventListener('keyup', (e) => {
                    if (e.target.value.length === 1 && index === otpInputs.length - 1) {
                        btnVerifyOtp.click();
                    }
                });
            });

            function updateHiddenOtp() {
                let code = '';
                otpInputs.forEach(input => {
                    code += input.value;
                });
                otpHidden.value = code;
            }

            // Step 2: Verify OTP and register (AJAX)
            btnVerifyOtp.addEventListener('click', async () => {
                clearErrors();
                updateHiddenOtp();

                if (otpHidden.value.length !== 6) {
                    errorFields.otp.textContent = 'Kode OTP harus terdiri dari 6 digit.';
                    errorFields.otp.classList.remove('hidden');
                    return;
                }

                btnVerifyOtp.disabled = true;
                verifySpinner.classList.remove('hidden');

                const name = document.getElementById('name').value;
                const email = document.getElementById('email').value;
                const password = document.getElementById('password').value;
                const password_confirmation = document.getElementById('password_confirmation').value;
                const otp = otpHidden.value;

                try {
                    const response = await fetch('/register', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                        },
                        body: JSON.stringify({
                            name,
                            email,
                            password,
                            password_confirmation,
                            otp
                        })
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        if (response.status === 422 && data.errors) {
                            if (data.errors.otp) {
                                errorFields.otp.textContent = data.errors.otp[0];
                                errorFields.otp.classList.remove('hidden');
                            }
                            if (data.errors.name || data.errors.email || data.errors.password) {
                                if (data.errors.name) {
                                    errorFields.name.textContent = data.errors.name[0];
                                    errorFields.name.classList.remove('hidden');
                                }
                                if (data.errors.email) {
                                    errorFields.email.textContent = data.errors.email[0];
                                    errorFields.email.classList.remove('hidden');
                                }
                                if (data.errors.password) {
                                    errorFields.password.textContent = data.errors.password[0];
                                    errorFields.password.classList.remove('hidden');
                                }
                                btnBackToRegister.click();
                            }
                        } else {
                            errorFields.otp.textContent = data.message || 'Terjadi kesalahan saat verifikasi.';
                            errorFields.otp.classList.remove('hidden');
                        }
                    } else {
                        window.location.href = data.redirect || '/dashboard';
                    }
                } catch (error) {
                    console.error(error);
                    errorFields.otp.textContent = 'Terjadi kesalahan koneksi. Silakan coba lagi.';
                    errorFields.otp.classList.remove('hidden');
                } finally {
                    btnVerifyOtp.disabled = false;
                    verifySpinner.classList.add('hidden');
                }
            });
        });
    </script>
</body>

</html>
