<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class GetWeekRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'week' => ['required', 'integer', 'min:1'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->route('week')) {
            $this->merge(['week' => (int) $this->route('week')]);
        }
    }

    public function getWeek(): int
    {
        return (int) $this->route('week');
    }
}

