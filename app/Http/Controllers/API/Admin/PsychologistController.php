<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PsychologistResource;
use App\Models\Professional;
use App\Models\Psychologist;
use App\Services\PsychologistVerificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PsychologistController extends Controller
{
    public function __construct(protected PsychologistVerificationService $verification) {}

    public function index(Request $request): JsonResponse
    {
        $query = Psychologist::query()->psychologists()->with('user');

        // Old: if ($request->has('verified')) {
        //     $query->where('is_verified', $request->boolean('verified'));
        // }
        if ($request->filled('verification_status')
            && in_array($request->string('verification_status')->toString(), Professional::VERIFICATION_STATUSES, true)
        ) {
            $query->where('verification_status', $request->string('verification_status')->toString());
        }

        $psychologists = $query->latest()->paginate(30);

        return response()->json([
            'data' => PsychologistResource::collection($psychologists),
            'meta' => [
                'current_page' => $psychologists->currentPage(),
                'last_page' => $psychologists->lastPage(),
                'total' => $psychologists->total(),
            ],
        ]);
    }

    public function startReview(Request $request, Psychologist $psychologist): JsonResponse
    {
        $this->authorize('verify', $psychologist);
        $professional = $this->verification->startReview($psychologist);

        return response()->json(['data' => new PsychologistResource($this->freshPsychologist($professional))]);
    }

    public function verify(Request $request, Psychologist $psychologist): JsonResponse
    {
        $this->authorize('verify', $psychologist);
        $professional = $this->verification->verify($psychologist);

        return response()->json(['data' => new PsychologistResource($this->freshPsychologist($professional))]);
    }

    public function reject(Request $request, Psychologist $psychologist): JsonResponse
    {
        $this->authorize('verify', $psychologist);
        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:3', 'max:2000'],
        ]);
        $professional = $this->verification->reject($psychologist, $validated['reason']);

        return response()->json([
            'message' => __('backend.psychologists.rejected_ok'),
            'data' => new PsychologistResource($this->freshPsychologist($professional)),
        ]);
    }

    public function suspend(Request $request, Psychologist $psychologist): JsonResponse
    {
        $this->authorize('suspend', $psychologist);
        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:3', 'max:2000'],
        ]);
        $professional = $this->verification->suspend($psychologist, $validated['reason']);

        return response()->json([
            'message' => __('backend.psychologists.suspended_ok'),
            'data' => new PsychologistResource($this->freshPsychologist($professional)),
        ]);
    }

    public function restore(Request $request, Psychologist $psychologist): JsonResponse
    {
        $this->authorize('suspend', $psychologist);
        $professional = $this->verification->restore($psychologist);

        return response()->json([
            'message' => __('backend.psychologists.restored_ok'),
            'data' => new PsychologistResource($this->freshPsychologist($professional)),
        ]);
    }

    protected function freshPsychologist(Professional $professional): Psychologist
    {
        return Psychologist::query()->with('user')->whereKey($professional->id)->firstOrFail();
    }
}
