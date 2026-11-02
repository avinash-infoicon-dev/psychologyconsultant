<?php

namespace App\Http\Controllers\API\Patient;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\PatientResource;
use App\Services\ProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(protected ProfileService $profiles) {}

    public function show(Request $request): JsonResponse
    {
        return response()->json([
            'data' => new PatientResource($this->profiles->patientProfile($request->user())),
        ]);

        // return response()->json(['data' => new PatientResource($request->user()->patient)]);
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $patient = $this->profiles->updatePatientProfile(
            $request->user(),
            $request->validated(),
        );

        return response()->json([
            'data' => new PatientResource($patient),
            'message' => __('profile_api.updated'),
        ]);

        // $user = $request->user();
        // $data = $request->validated();
        //
        // $user->update(array_intersect_key($data, array_flip(['name', 'phone', 'locale'])));
        //
        // $user->patient->update(array_intersect_key($data, array_flip([
        //     'date_of_birth', 'gender', 'address', 'city', 'country',
        //     'emergency_contact_name', 'emergency_contact_phone',
        // ])));
        //
        // return response()->json(['data' => new PatientResource($user->patient->fresh())]);
    }
}
