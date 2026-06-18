<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserOtp;
use App\Mail\OtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AuthController extends Controller
{
    /**
     * Register a new user with OTP validation.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'otp' => 'required|string|size:6',
        ], [
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

        // Complete registration inside a transaction
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

        // Create Sanctum Token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User successfully registered and verified',
            'data' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    /**
     * Send OTP for manual registration.
     */
    public function sendRegisterOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255|unique:users',
        ], [
            'email.unique' => 'Email ini sudah terdaftar. Silakan gunakan email lain atau masuk.',
        ]);

        $email = $request->email;

        // Generate 6 digit random OTP
        $otp = sprintf("%06d", mt_rand(0, 999999));

        // Save OTP to DB
        UserOtp::updateOrCreate(
            ['email' => $email, 'type' => 'register'],
            [
                'otp' => $otp,
                'expires_at' => Carbon::now()->addMinutes(15),
            ]
        );

        // Send OTP email
        try {
            Mail::to($email)->send(new OtpMail($otp, 'register'));
        } catch (\Exception $e) {
            Log::error("Failed to send OTP to {$email}: " . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengirimkan email OTP. Silakan periksa konfigurasi mail server Anda.',
                'error' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Kode OTP berhasil dikirim ke email Anda.',
        ]);
    }

    /**
     * Send OTP for Forgot Password.
     */
    public function sendForgotPasswordOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255|exists:users,email',
        ], [
            'email.exists' => 'Email ini tidak terdaftar di sistem kami.',
        ]);

        $email = $request->email;

        // Generate 6 digit random OTP
        $otp = sprintf("%06d", mt_rand(0, 999999));

        // Save OTP to DB
        UserOtp::updateOrCreate(
            ['email' => $email, 'type' => 'forgot_password'],
            [
                'otp' => $otp,
                'expires_at' => Carbon::now()->addMinutes(15),
            ]
        );

        // Send OTP email
        try {
            Mail::to($email)->send(new OtpMail($otp, 'forgot_password'));
        } catch (\Exception $e) {
            Log::error("Failed to send Forgot Password OTP to {$email}: " . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengirimkan email OTP. Silakan periksa konfigurasi mail server Anda.',
                'error' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Kode OTP untuk atur ulang kata sandi berhasil dikirim ke email Anda.',
        ]);
    }

    /**
     * Reset password using OTP.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255|exists:users,email',
            'otp' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'email.exists' => 'Email ini tidak terdaftar di sistem kami.',
            'otp.required' => 'Kode OTP wajib diisi.',
            'otp.size' => 'Kode OTP harus terdiri dari 6 digit.',
            'password.min' => 'Kata sandi baru minimal harus 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        // Verify OTP
        $otpRecord = UserOtp::where('email', $request->email)
            ->where('type', 'forgot_password')
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

        // Reset password inside a transaction
        DB::transaction(function () use ($request, $otpRecord) {
            $user = User::where('email', $request->email)->firstOrFail();
            $user->update([
                'password' => Hash::make($request->password),
            ]);

            // Revoke all tokens to secure the account
            $user->tokens()->delete();

            // Delete the OTP record
            $otpRecord->delete();
        });

        return response()->json([
            'message' => 'Kata sandi berhasil diatur ulang. Silakan masuk menggunakan kata sandi baru Anda.',
        ]);
    }

    /**
     * Authenticate user and generate token.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Kredensial yang diberikan salah.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'data' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Get the authenticated User profile.
     */
    public function user(Request $request)
    {
        return response()->json([
            'data' => $request->user()
        ]);
    }

    /**
     * Revoke current user's token.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Token successfully revoked'
        ]);
    }

    /**
     * Mobile API: Authenticate user using Google ID Token (from Android).
     */
    public function googleTokenLogin(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        try {
            $client = new \Google_Client();
            
            // Allow token verification against either Android Client IDs or Web Client ID
            $clientIds = array_filter([
                env('GOOGLE_ANDROID_CLIENT_ID'),
                env('GOOGLE_ANDROID_RELEASE_CLIENT_ID'),
                env('GOOGLE_CLIENT_ID')
            ]);
            
            if (count($clientIds) === 0) {
                return response()->json(['message' => 'Server misconfiguration: No Google Client IDs set.'], 500);
            }

            // Verify the ID Token
            $payload = $client->verifyIdToken($request->token);

            if ($payload) {
                // Verify that the token audience matches our registered client IDs to prevent Token Substitution attacks
                if (!isset($payload['aud']) || !in_array($payload['aud'], $clientIds)) {
                    return response()->json([
                        'message' => 'Unauthorized Google token audience.'
                    ], 401);
                }

                $user = User::firstOrCreate(
                    ['email' => $payload['email']],
                    [
                        'name' => $payload['name'] ?? 'Google User',
                        'google_id' => $payload['sub'],
                        'password' => null, 
                        'email_verified_at' => Carbon::now(),
                    ]
                );

                if (! $user->google_id || ! $user->email_verified_at) {
                    $user->update([
                        'google_id' => $user->google_id ?? $payload['sub'],
                        'email_verified_at' => $user->email_verified_at ?? Carbon::now(),
                    ]);
                }

                $token = $user->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'message' => 'Login via Google successful',
                    'data' => $user,
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                ]);
            } else {
                return response()->json([
                    'message' => 'Invalid Google Token (Verification failed)'
                ], 401);
            }

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error verifying Google Token',
                'error' => $e->getMessage()
            ], 401);
        }
    }

    /**
     * Web Dashboard: Redirect to Google Sign-In.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Web Dashboard: Handle Google Callback.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::firstOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName(),
                    'google_id' => $googleUser->getId(),
                    'password' => null, 
                    'email_verified_at' => Carbon::now(),
                    'profile_image' => $googleUser->getAvatar(),
                ]
            );

            // Sync google credentials if they are missing
            if (empty($user->google_id) || empty($user->email_verified_at)) {
                $user->update([
                    'google_id' => $user->google_id ?: $googleUser->getId(),
                    'email_verified_at' => $user->email_verified_at ?: Carbon::now(),
                ]);
            }

            // Always sync the google profile image if it is currently empty or is a google avatar URL
            if (empty($user->profile_image) || str_starts_with($user->profile_image, 'http')) {
                $user->update([
                    'profile_image' => $googleUser->getAvatar(),
                ]);
            }

            Auth::login($user, remember: true);

            return redirect()->intended('/dashboard');

        } catch (\Exception $e) {
            Log::error('Google OAuth Login failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect('/login')->with('error', 'Login via Google failed. ' . $e->getMessage());
        }
    }

    /**
     * Update authenticated user profile (Name, Password, etc.)
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'name.required' => 'Nama lengkap harus diisi.',
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah digunakan oleh user lain.',
            'password.min' => 'Kata sandi minimal harus 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $user->name = $request->name;
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        if ($request->filled('password')) {
            $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        }
        $user->save();

        return response()->json([
            'message' => 'Profil berhasil diperbarui.',
            'data' => $user
        ]);
    }

    /**
     * Upload profile avatar image
     */
    public function uploadAvatar(Request $request)
    {
        $user = $request->user();
        
        $request->validate([
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'profile_image.required' => 'File gambar profile wajib diunggah.',
            'profile_image.image' => 'File harus berupa gambar.',
            'profile_image.mimes' => 'Format gambar harus jpeg, png, jpg, gif, atau webp.',
            'profile_image.max' => 'Ukuran gambar maksimal adalah 2MB.',
        ]);

        if ($request->hasFile('profile_image')) {
            // Delete old profile image if it exists and is stored locally in /storage/avatars
            if ($user->profile_image) {
                $oldPath = str_replace('/storage/', '', parse_url($user->profile_image, PHP_URL_PATH));
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            // Save the new file
            $path = $request->file('profile_image')->store('avatars', 'public');
            $url = Storage::url($path);

            $user->update([
                'profile_image' => $url,
            ]);

            return response()->json([
                'message' => 'Foto profil berhasil diperbarui.',
                'profile_image' => $url,
            ]);
        }

        return response()->json(['message' => 'Gagal mengunggah gambar.'], 400);
    }
}
