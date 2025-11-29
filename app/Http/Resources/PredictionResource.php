<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

class PredictionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'team_id' => $this['team_id'],
            'team_name' => $this['team_name'],
            'current_points' => $this['current_points'],
            'prediction_percentage' => $this['prediction_percentage'],
        ];
    }
}