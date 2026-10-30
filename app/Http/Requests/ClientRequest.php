<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole(['administrador', 'vendedor']) ?? false;
    }

    public function rules(): array
    {
        $client = $this->route('client');
        $isRuc = $this->input('document_type') === 'RUC';

        return [
            'document_type' => ['required', Rule::in(['DNI', 'RUC'])],
            'document_number' => [
                'required',
                $isRuc ? 'digits:11' : 'digits:8',
                Rule::unique('clients', 'document_number')->ignore($client?->id),
            ],
            'name' => ['required', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:180'],
            'type' => ['required', Rule::in(['minorista', 'mayorista'])],
            'active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'document_number.digits' => 'El documento debe tener 8 dígitos para DNI o 11 dígitos para RUC.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'active' => $this->boolean('active'),
            'document_number' => preg_replace('/\D/', '', (string) $this->input('document_number')),
        ]);
    }
}
