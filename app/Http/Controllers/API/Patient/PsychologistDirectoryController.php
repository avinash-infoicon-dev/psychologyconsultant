<?php

namespace App\Http\Controllers\API\Patient;

use App\Http\Controllers\Controller;
use App\Http\Requests\PsychologistDirectory\ListPsychologistsRequest;
use App\Http\Resources\PsychologistDirectoryResource;
use App\Models\Professional;
use App\Services\PsychologistDirectoryService;
use Illuminate\Http\JsonResponse;

class PsychologistDirectoryController extends Controller
{
    public function __construct(protected PsychologistDirectoryService $directory) {}

    /** Only verified psychologists are visible to patients — unverified ones are still admin-review-pending. */
    public function index(ListPsychologistsRequest $request): JsonResponse
    {
        $psychologists = $this->directory->paginate($request->validated());

        return response()->json($this->directory->paginatedPayload($psychologists));

        // $query = Psychologist::query()->where('is_verified', true)->with('user');
        //
        // if ($request->filled('specialization')) {
        //     $query->where('specialization', 'like', '%'.$request->string('specialization').'%');
        // }
        //
        // if ($request->boolean('accepting_only')) {
        //     $query->where('is_accepting_patients', true);
        // }
        //
        // $psychologists = $query->paginate(20);
        //
        // return response()->json([
        //     'data' => PsychologistDirectoryResource::collection($psychologists),
        //     'meta' => [
        //         'current_page' => $psychologists->currentPage(),
        //         'last_page' => $psychologists->lastPage(),
        //         'total' => $psychologists->total(),
        //     ],
        // ]);
    }

    public function show(Professional $professional): JsonResponse
    {
        // Directory patients may only view verified psychologists (404 if pending/unverified).
        // $this->authorize('view', $psychologist);

        return response()->json([
            'data' => new PsychologistDirectoryResource(
                // Legacy argument was $psychologist.
                $this->directory->findForDirectory($professional),
            ),
        ]);

        // return response()->json(['data' => new PsychologistDirectoryResource($psychologist->load('user'))]);
    }
}
