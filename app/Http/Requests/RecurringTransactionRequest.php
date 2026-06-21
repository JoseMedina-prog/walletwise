<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RecurringTransactionRequest extends FormRequest
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
                Rule::exists('categories', 'id')->where(fn ($q) => $q->where('user_id', $userId)),
            ],
            'type'             => ['required', Rule::in(['income', 'expense'])],
            'amount'           => ['required', 'numeric', 'min:0.01', 'max:9999999999.99'],
            'description'      => ['nullable', 'string', 'max:255'],
            'frequency'        => ['required', Rule::in(['daily', 'weekly', 'monthly', 'yearly'])],
            'interval'         => ['required', 'integer', 'min:1', 'max:60'],
            'start_date'       => ['required', 'date'],
            'end_date'         => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_active'        => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'La categoría es obligatoria.',
            'category_id.exists'   => 'La categoría seleccionada no es válida.',
            'amount.required'      => 'El importe es obligatorio.',
            'amount.min'           => 'El importe debe ser mayor a 0.',
            'frequency.required'   => 'La frecuencia es obligatoria.',
            'frequency.in'         => 'La frecuencia no es válida.',
            'interval.required'    => 'El intervalo es obligatorio.',
            'interval.min'         => 'El intervalo mínimo es 1.',
            'interval.max'         => 'El intervalo máximo es 60.',
            'start_date.required'  => 'La fecha de inicio es obligatoria.',
            'end_date.after_or_equal' => 'La fecha de fin debe ser posterior a la de inicio.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            $type = $this->input('type');
            $catId = $this->input('category_id');
            if ($type && $catId) {
                $cat = \App\Models\Category::where('user_id', auth()->id())
                    ->where('id', $catId)->first();
                if ($cat && $cat->type !== $type) {
                    $v->errors()->add('category_id', 'La categoría no coincide con el tipo de transacción.');
                }
            }
        });
    }
}