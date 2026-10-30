<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WholesaleEvaluationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('administrador') ?? false;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['aprobada', 'rechazada'])],
            'approved_discount' => ['nullable', 'numeric', 'min:0', 'max:30', 'required_if:status,aprobada'],
            'notes' => ['nullable', 'string', 'max:300'],
        ];
    }
}
