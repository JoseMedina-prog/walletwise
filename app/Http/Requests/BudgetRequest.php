<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BudgetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $userId = auth()->id();

        return [
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')
                    ->where(fn ($q) => $q->where('user_id', $userId)->where('type', 'expense')),
            ],
            'monthly_amount'  => ['required', 'numeric', 'min:0.01', 'max:9999999999.99'],
            'alert_threshold' => ['required', 'integer', 'min:1', 'max:100'],
            'is_active'       => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'La categoría es obligatoria.',
            'category_id.exists'   => 'La categoría seleccionada no es válida o no es de gastos.',
            'monthly_amount.required' => 'El importe mensual es obligatorio.',
            'monthly_amount.numeric'  => 'El importe mensual debe ser un número.',
            'monthly_amount.min'      => 'El importe mensual debe ser mayor a 0.',
            'alert_threshold.required' => 'El umbral de alerta es obligatorio.',
            'alert_threshold.integer'  => 'El umbral debe ser un número entero.',
            'alert_threshold.min'      => 'El umbral mínimo es 1%.',
            'alert_threshold.max'      => 'El umbral máximo es 100%.',
        ];
    }
}