<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeagueResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'current_week' => $this->current_week,
            'total_weeks' => $this->getTotalWeeks(),
            'status' => $this->status->value,
            'is_completed' => $this->isCompleted(),
            'standings' => StandingResource::collection($this->whenLoaded('standings')),
            'matches' => MatchResource::collection($this->whenLoaded('matches')),
        ];
    }
}

