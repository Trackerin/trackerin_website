<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class WebAuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle authentication attempt.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi harus diisi.',
        ]);

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'Kredensial yang diberikan tidak cocok dengan data kami.',
        ])->onlyInput('email');
    }

    /**
     * Show the registration form.
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Handle user registration.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'otp' => ['required', 'string', 'size:6'],
        ], [
            'name.required' => 'Nama lengkap harus diisi.',
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.required' => 'Kata sandi harus diisi.',
            'password.min' => 'Kata sandi minimal harus 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'otp.required' => 'Kode OTP wajib diisi.',
            'otp.size' => 'Kode OTP harus terdiri dari 6 digit.',
        ]);

        // Verify OTP
        $otpRecord = UserOtp::where('email', $request->email)
            ->where('type', 'register')
            ->first();

        if (!$otpRecord) {
            throw ValidationException::withMessages([
                'otp' => ['Kode OTP tidak ditemukan. Silakan kirim ulang OTP.'],
            ]);
        }

        if ($otpRecord->otp !== $request->otp) {
            throw ValidationException::withMessages([
                'otp' => ['Kode OTP yang Anda masukkan salah.'],
            ]);
        }

        if ($otpRecord->isExpired()) {
            throw ValidationException::withMessages([
                'otp' => ['Kode OTP sudah kedaluwarsa. Silakan kirim ulang OTP.'],
            ]);
        }

        $user = DB::transaction(function () use ($request, $otpRecord) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'email_verified_at' => Carbon::now(),
            ]);

            // Delete the OTP record after successful registration
            $otpRecord->delete();

            return $user;
        });

        Auth::login($user);

        if ($request->expectsJson()) {
            return response()->json([
                'redirect' => route('dashboard'),
                'message' => 'Registrasi dan verifikasi berhasil.'
            ]);
        }

        return redirect('/dashboard');
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
