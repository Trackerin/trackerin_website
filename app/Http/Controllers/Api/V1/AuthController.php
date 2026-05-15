<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Create Sanctum Token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User successfully registered',
            'data' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
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
            
            // Allow token verification against either Android Client ID or Web Client ID
            $clientIds = array_filter([
                env('GOOGLE_ANDROID_CLIENT_ID'),
                env('GOOGLE_CLIENT_ID')
            ]);
            
            if (count($clientIds) === 0) {
                return response()->json(['message' => 'Server misconfiguration: No Google Client IDs set.'], 500);
            }

            // Verify the ID Token
            $payload = $client->verifyIdToken($request->token);

            if ($payload) {
                $user = User::firstOrCreate(
                    ['email' => $payload['email']],
                    [
                        'name' => $payload['name'] ?? 'Google User',
                        'google_id' => $payload['sub'],
                        'password' => null, 
                    ]
                );

                if (! $user->google_id) {
                    $user->update(['google_id' => $payload['sub']]);
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
                ]
            );

            if (! $user->google_id) {
                $user->update(['google_id' => $googleUser->getId()]);
            }

            Auth::login($user);

            return redirect()->intended('/dashboard');

        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Login via Google failed. ' . $e->getMessage());
        }
    }
}
