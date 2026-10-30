<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeliveryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole(['administrador', 'vendedor', 'almacenero']) ?? false;
    }

    public function rules(): array
    {
        return [
            'received_by' => ['required', 'string', 'max:150'],
            'document_number' => ['required', 'string', 'max:20'],
            'signature' => ['nullable', 'image', 'max:2048'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
