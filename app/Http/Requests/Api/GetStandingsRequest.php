<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class GetStandingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
}