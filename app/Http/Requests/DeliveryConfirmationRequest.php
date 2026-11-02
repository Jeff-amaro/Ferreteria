<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

class DeliveryConfirmationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === UserRole::Repartidor;
    }

    public function rules(): array
    {
        return [
            'codigo_confirmacion' => ['required', 'string', 'max:12'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
