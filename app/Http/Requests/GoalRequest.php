<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GoalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name'           => ['required', 'string', 'max:100'],
            'description'    => ['nullable', 'string', 'max:500'],
            'target_amount'  => ['required', 'numeric', 'min:0.01', 'max:9999999999.99'],
            'current_amount' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'start_date'     => ['nullable', 'date'],
            'target_date'    => ['nullable', 'date', 'after_or_equal:today'],
            'color'          => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'        => 'El nombre es obligatorio.',
            'target_amount.required' => 'El objetivo es obligatorio.',
            'target_amount.min'    => 'El objetivo debe ser mayor a 0.',
            'target_date.after_or_equal' => 'La fecha objetivo debe ser hoy o posterior.',
        ];
    }
}