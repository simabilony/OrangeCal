<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\GoogleLoginRequest;
use App\Http\Requests\Auth\GoogleLoginOnlyRequest;
use App\Http\Requests\Auth\AppleLoginRequest;
use App\Http\Requests\Auth\MobileLoginRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    public function googleLogin(GoogleLoginRequest $request): JsonResponse
    {
        $data = $request->validated();
        $result = $this->authService->authenticateGoogle([
//            'google_id' => $data['google_id'],
            'email' => $data['email'],
            'name' => $data['name'],
            'password' => $data['password'],
        ]);

        if (isset($data['fcm_token'])) {
            $result['user']->update(['fcm_token' => $data['fcm_token']]);
        }

        return response()->json([
            'user' => new UserResource($result['user']->load('profile')),
            'token' => $result['token'],
        ]);
    }

    public function appleLogin(AppleLoginRequest $request): JsonResponse
    {
        $data = $request->validated();
        $result = $this->authService->authenticateApple([
            'apple_id' => $data['apple_id'],
            'email' => $data['email'] ?? null,
            'name' => $data['name'] ?? null,
        ]);

        if (isset($data['fcm_token'])) {
            $result['user']->update(['fcm_token' => $data['fcm_token']]);
        }

        return response()->json([
            'user' => new UserResource($result['user']->load('profile')),
            'token' => $result['token'],
        ]);
    }

    public function mobileLogin(MobileLoginRequest $request): JsonResponse
    {
        $data = $request->validated();
        $result = $this->authService->authenticateMobile([
            'mobile_id' => $data['mobile_id'],
            'name' => $data['name'] ?? null,
        ]);

        if (isset($data['fcm_token'])) {
            $result['user']->update(['fcm_token' => $data['fcm_token']]);
        }

        return response()->json([
            'user' => new UserResource($result['user']->load('profile')),
            'token' => $result['token'],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function refreshToken(Request $request): JsonResponse
    {
        $result = $this->authService->refreshToken($request->user());

        return response()->json(['token' => $result['token']]);
    }

    /**
     * Google Login (Login Only)
     *
     * Authenticate an existing user with Google credentials (email and password).
     * This endpoint is for login only and will not create a new user.
     *
     * @operationId googleLoginOnly
     * @tags Authentication
     * @bodyContent \App\Http\Requests\Auth\GoogleLoginOnlyRequest
     * @response 200 {
     *   "user": {
     *     "id": 1,
     *     "name": "John Doe",
     *     "email": "john@example.com"
     *   },
     *   "token": "1|xxxxxxxxxxxxx"
     * }
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "email": ["The provided credentials do not match our records."]
     *   }
     * }
     */
    public function googleLoginOnly(GoogleLoginOnlyRequest $request): JsonResponse
    {
        $data = $request->validated();
        $result = $this->authService->loginGoogle([
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        if (isset($data['fcm_token'])) {
            $result['user']->update(['fcm_token' => $data['fcm_token']]);
        }

        return response()->json([
            'user' => new UserResource($result['user']->load('profile')),
            'token' => $result['token'],
        ]);
    }
}

