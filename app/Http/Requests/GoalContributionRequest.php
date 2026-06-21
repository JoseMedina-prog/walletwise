<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GoalContributionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'amount'            => ['required', 'numeric', 'min:0.01', 'max:9999999999.99'],
            'contribution_date' => ['required', 'date'],
            'note'              => ['nullable', 'string', 'max:255'],
        ];
    }
}