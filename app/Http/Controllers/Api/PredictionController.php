<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\PredictionResource;
use App\Services\LeagueService;
use App\Services\PredictionService;
use Illuminate\Http\JsonResponse;

class PredictionController extends Controller {
    public function __construct(
        private PredictionService $predictionService,
        private LeagueService $leagueService
    ) {}

    public function index(): JsonResponse
    {
        $league = $this->leagueService->getCurrentLeague();
        if (!$this->predictionService->shouldShowPredictions($league)) {
            return ApiResponse::badRequest('Predictions are not available yet.');
        }
        $predictions = $this->predictionService->calculatePredictions($league);
        return ApiResponse::success(PredictionResource::collection($predictions));
    }
}