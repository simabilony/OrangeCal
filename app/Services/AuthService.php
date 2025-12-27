<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;

class AuthService
{
    /**
     * Authenticate or create user via Google.
     */
    public function authenticateGoogle(array $data): array
    {
       // $user = User::where('google_id', $data['google_id'])->first();

            $user = User::where('email', $data['email'])->first();

                $user = User::create([
//                    'google_id' => $data['google_id'],
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make(Str::random(32)),
                    'is_active' => true,
                ]);


        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user' => $user->load('profile'),
            'token' => $token,
        ];
    }

    /**
     * Authenticate or create user via Apple.
     */
    public function authenticateApple(array $data): array
    {
        $user = User::where('apple_id', $data['apple_id'])->first();

        if (!$user) {
            $user = User::where('email', $data['email'])->first();

            if ($user) {
                $user->update(['apple_id' => $data['apple_id']]);
            } else {
                $user = User::create([
                    'apple_id' => $data['apple_id'],
                    'name' => $data['name'] ?? 'Apple User',
                    'email' => $data['email'],
                    'password' => Hash::make(Str::random(32)),
                    'is_active' => true,
                ]);
            }
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user' => $user->load('profile'),
            'token' => $token,
        ];
    }

    /**
     * Authenticate or create user via mobile OTP.
     */
    public function authenticateMobile(array $data): array
    {
        $user = User::where('mobile_id', $data['mobile_id'])->first();

        if (!$user) {
            $user = User::create([
                'mobile_id' => $data['mobile_id'],
                'name' => $data['name'] ?? 'Mobile User',
                'password' => Hash::make(Str::random(32)),
                'is_active' => true,
            ]);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user' => $user->load('profile'),
            'token' => $token,
        ];
    }

    /**
     * Logout user.
     */
    public function logout(User $user, ?string $tokenId = null): void
    {
        if ($tokenId) {
            PersonalAccessToken::findToken($tokenId)?->delete();
        } else {
            $user->tokens()->delete();
        }
    }

    /**
     * Refresh token.
     */
    public function refreshToken(User $user): array
    {
        $user->tokens()->delete();
        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'token' => $token,
        ];
    }
}






