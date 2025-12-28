<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class AuthService
{
    /**
     * Authenticate or create user via Google (Register/Login).
     */
    public function authenticateGoogle(array $data): array
    {
        // Check if user exists
        $user = User::where('email', $data['email'])->first();

        if ($user) {
            // User exists - verify password for login
            if (isset($data['password']) && !Hash::check($data['password'], $user->password)) {
                throw ValidationException::withMessages([
                    'password' => ['The provided credentials do not match our records.'],
                ]);
            }

            // Update name if provided
            if (isset($data['name'])) {
                $user->update(['name' => $data['name']]);
            }
        } else {
            // User doesn't exist - create new user (register)
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password'] ?? Str::random(32)),
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
     * Login user with email and password (Google login only - no registration).
     */
    public function loginGoogle(array $data): array
    {
        $user = User::where('email', $data['email'])->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials do not match our records.'],
            ]);
        }

        if (!Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['The provided credentials do not match our records.'],
            ]);
        }

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Your account has been deactivated.'],
            ]);
        }

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






