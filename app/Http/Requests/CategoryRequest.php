<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $userId = auth()->id();

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('categories')->where(fn ($q) => $q->where('user_id', $userId)),
            ],
            'type' => ['required', Rule::in(['income', 'expense'])],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.unique' => 'Ya tienes una categoría con ese nombre.',
            'type.required' => 'El tipo es obligatorio.',
            'type.in' => 'El tipo debe ser ingreso o gasto.',
        ];
    }
}
