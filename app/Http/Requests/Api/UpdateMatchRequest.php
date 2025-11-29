<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'home_score' => ['required', 'integer', 'min:0', 'max:20'],
            'away_score' => ['required', 'integer', 'min:0', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'home_score.required' => 'Home score is required.',
            'home_score.integer' => 'Home score must be an integer.',
            'home_score.min' => 'Home score cannot be negative.',
            'home_score.max' => 'Home score cannot exceed 20.',
            'away_score.required' => 'Away score is required.',
            'away_score.integer' => 'Away score must be an integer.',
            'away_score.min' => 'Away score cannot be negative.',
            'away_score.max' => 'Away score cannot exceed 20.',
        ];
    }

    public function getHomeScore(): int
    {
        return (int) $this->input('home_score');
    }

    public function getAwayScore(): int
    {
        return (int) $this->input('away_score');
    }
}

