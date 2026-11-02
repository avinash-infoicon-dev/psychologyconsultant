<?php

namespace App\Http\Controllers\API\Subscription;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlanResource;
use App\Models\Plan;
use Illuminate\Http\JsonResponse;

class PlanController extends Controller
{
    public function index(): JsonResponse
    {
        $plans = Plan::with('translations')->where('is_active', true)->orderBy('sort_order')->get();

        return response()->json(['data' => PlanResource::collection($plans)]);
    }
}
