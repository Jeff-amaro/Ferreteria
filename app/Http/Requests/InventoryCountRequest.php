<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InventoryCountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole(['administrador', 'almacenero']) ?? false;
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*' => ['required', 'integer', 'min:0', 'max:999999'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Registra al menos un producto en el conteo.',
            'items.*.integer' => 'La cantidad física debe ser un número entero.',
            'items.*.min' => 'La cantidad física no puede ser negativa.',
        ];
    }
}
