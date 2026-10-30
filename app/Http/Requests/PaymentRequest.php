<?php

namespace App\Http\Requests;

use App\Models\Payment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole(['administrador', 'vendedor']) ?? false;
    }

    public function rules(): array
    {
        return [
            'method' => ['required', Rule::in(array_keys(Payment::METHODS))],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'reference' => ['nullable', 'string', 'max:100', 'required_unless:method,efectivo'],
        ];
    }

    public function messages(): array
    {
        return [
            'reference.required_unless' => 'Ingresa el número de operación o referencia del pago.',
        ];
    }
}
