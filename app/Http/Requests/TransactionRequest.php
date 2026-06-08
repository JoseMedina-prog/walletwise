<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $userId = auth()->id();
        $type = $this->input('type');

        return [
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where(fn ($q) => $q->where('user_id', $userId)),
            ],
            'type' => ['required', Rule::in(['income', 'expense'])],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999999.99'],
            'description' => ['nullable', 'string', 'max:255'],
            'transaction_date' => ['required', 'date', 'before_or_equal:today'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $type = $this->input('type');
            $categoryId = $this->input('category_id');

            if ($type && $categoryId) {
                $cat = Category::where('user_id', auth()->id())
                    ->where('id', $categoryId)
                    ->first();

                if ($cat && $cat->type !== $type) {
                    $validator->errors()->add(
                        'category_id',
                        'La categoría seleccionada no coincide con el tipo de transacción.'
                    );
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'La categoría es obligatoria.',
            'category_id.exists' => 'La categoría seleccionada no es válida.',
            'type.required' => 'El tipo es obligatorio.',
            'type.in' => 'El tipo debe ser ingreso o gasto.',
            'amount.required' => 'El importe es obligatorio.',
            'amount.numeric' => 'El importe debe ser un número.',
            'amount.min' => 'El importe debe ser mayor a 0.',
            'transaction_date.required' => 'La fecha es obligatoria.',
            'transaction_date.date' => 'La fecha no es válida.',
            'transaction_date.before_or_equal' => 'La fecha no puede ser futura.',
        ];
    }
}
