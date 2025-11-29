<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\TeamResource;
use App\Services\TeamService;
use Illuminate\Http\JsonResponse;

class TeamController extends Controller {
    public function __construct(
        private TeamService $teamService
    ) {}

    public function index(): JsonResponse
    {
        $teams = $this->teamService->getAllTeams();
        return ApiResponse::success(TeamResource::collection($teams));
    }
}