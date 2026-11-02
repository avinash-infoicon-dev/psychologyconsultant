<?php

namespace App\Http\Controllers\API\Psychologist;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\PsychologistResource;
use App\Models\Professional;
use App\Services\PsychologistVerificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(protected PsychologistVerificationService $verification) {}

    public function show(Request $request): JsonResponse
    {
        return response()->json(['data' => new PsychologistResource($request->user()->psychologist)]);
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();

        $user->update(array_intersect_key($data, array_flip(['name', 'phone', 'locale'])));

        // A psychologist can only toggle whether they're accepting NEW patients —
        // is_verified stays admin-controlled regardless of what's posted here.
        $user->psychologist->update(array_intersect_key($data, array_flip([
            'specialization', 'bio', 'years_experience', 'is_accepting_patients', 'languages',
        ])));

        return response()->json(['data' => new PsychologistResource($user->psychologist->fresh())]);
    }

    public function submitVerification(Request $request): JsonResponse
    {
        $professional = $request->user()->professional
            ?? Professional::query()->whereKey($request->user()->psychologist?->id)->firstOrFail();

        $professional = $this->verification->submit($professional);

        return response()->json([
            'message' => __('frontend.psych_profile.verification_submitted'),
            'data' => new PsychologistResource(
                \App\Models\Psychologist::query()->with('user')->whereKey($professional->id)->firstOrFail()
            ),
        ]);
    }
}
