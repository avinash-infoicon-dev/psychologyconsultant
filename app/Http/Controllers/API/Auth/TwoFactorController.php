<?php

namespace App\Http\Controllers\API\Auth;

use App\Exceptions\TwoFactorException;
use App\Http\Controllers\Controller;
use App\Http\Requests\TwoFactorChallengeRequest;
use App\Http\Requests\TwoFactorVerifyRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AuthService;
use App\Services\TwoFactorService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;

class TwoFactorController extends Controller
{
    public function __construct(
        protected AuthService $authService,
        protected TwoFactorService $twoFactor,
    ) {}

    /** Resend a fresh code — used if the first email never arrived or expired. */
    public function challenge(TwoFactorChallengeRequest $request): JsonResponse
    {
        $user = User::where('email', $request->string('email'))->firstOrFail();

        $this->twoFactor->sendCode($user);

        return response()->json(['message' => __('auth_api.two_factor_code_resent')]);
    }

    public function verify(TwoFactorVerifyRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->completeTwoFactorLogin(
                $request->string('email'),
                $request->string('code'),
                $request->input('device_name'),
            );
        } catch (TwoFactorException $e) {
            return response()->json(['message' => $e->getMessage()], 429);
        } catch (AuthenticationException $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        }

        if ($result['status'] === 'invalid_code') {
            return response()->json(['message' => __('auth_api.two_factor_invalid_code')], 422);
        }

        return response()->json([
            'token' => $result['token'],
            'token_type' => 'Bearer',
            'expires_in' => $this->authService->tokenExpiresIn(),
            'user' => new UserResource($result['user']),
        ]);
    }
}
