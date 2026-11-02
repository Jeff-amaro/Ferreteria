<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

class DeliveryIncidentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === UserRole::Repartidor;
    }

    public function rules(): array
    {
        return [
            'incidencia_nota' => ['required', 'string', 'min:5', 'max:1000'],
        ];
    }
}
