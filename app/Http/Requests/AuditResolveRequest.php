<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AuditResolveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole(['administrador', 'almacenero']) ?? false;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['en_proceso', 'resuelta'])],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
