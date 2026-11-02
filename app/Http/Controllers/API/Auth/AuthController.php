<?php

namespace App\Http\Controllers\API\Auth;

use App\Exceptions\TwoFactorException;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmailVerificationRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResendEmailVerificationRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(protected AuthService $authService) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->authService->register($request->validated());

        $message = $user->hasRbacRole('psychologist')
            ? __('auth_api.register_psychologist_pending')
            : __('auth_api.register_verify_email');

        return response()->json([
            'message' => $message,
            'requires_email_verification' => true,
            'user' => new UserResource($user),
        ], 201);
    }

    public function verifyEmail(EmailVerificationRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->verifyEmail(
                $request->string('email'),
                $request->string('code'),
            );
        } catch (TwoFactorException $e) {
            return response()->json(['message' => $e->getMessage()], 429);
        }

        if ($result['status'] === 'invalid_code') {
            return response()->json(['message' => __('auth_api.email_verify_invalid_code')], 422);
        }

        $user = $result['user']->loadMissing('psychologist', 'professional', 'roles');

        // Old: withhold token until admin verifies.
        // if ($user->isPsychologist() && ! $user->psychologist?->is_verified) {
        //     return response()->json([
        //         'message' => __('auth_api.email_verified_awaiting_admin'),
        //         'email_verified' => true,
        //         'requires_admin_verification' => true,
        //         'user' => new UserResource($user),
        //     ]);
        // }
        if ($user->isPsychologist() && $user->isPsychologistSuspended()) {
            return response()->json([
                'message' => __('auth_api.psychologist_suspended'),
                'email_verified' => true,
                'user' => new UserResource($user),
            ]);
        }

        return response()->json([
            'message' => __('auth_api.email_verified'),
            'email_verified' => true,
            'token' => $this->authService->issueToken($user),
            'token_type' => 'Bearer',
            'expires_in' => $this->authService->tokenExpiresIn(),
            'user' => new UserResource($user),
        ]);
    }

    public function resendEmailVerification(ResendEmailVerificationRequest $request): JsonResponse
    {
        $this->authService->resendEmailVerification($request->string('email'));

        return response()->json([
            'message' => __('auth_api.email_verify_resent'),
        ]);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->attemptLogin(
                $request->string('email'),
                $request->string('password'),
                $request->input('device_name'),
            );
        } catch (AuthenticationException $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        }

        if ($result['status'] === 'requires_2fa') {
            return response()->json([
                'message' => __('auth_api.two_factor_code_sent'),
                'requires_2fa' => true,
            ], 200);
        }

        return response()->json([
            'token' => $result['token'],
            'token_type' => 'Bearer',
            'expires_in' => $this->authService->tokenExpiresIn(),
            'user' => new UserResource($result['user']),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->bearerToken());

        return response()->json(['message' => __('auth_api.logged_out')]);
    }

    /** Issues a fresh JWT (does not revoke the old one). */
    public function refresh(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'token' => $this->authService->issueToken($user),
            'token_type' => 'Bearer',
            'expires_in' => $this->authService->tokenExpiresIn(),
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json(['user' => new UserResource($request->user())]);
    }
}
