<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CourierAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole(['administrador', 'vendedor', 'almacenero']) ?? false;
    }

    public function rules(): array
    {
        return [
            'repartidor_id' => [
                'nullable',
                Rule::exists('users', 'id')->where(
                    fn ($query) => $query->where('role', UserRole::Repartidor->value)->where('active', true)
                ),
            ],
        ];
    }
}
