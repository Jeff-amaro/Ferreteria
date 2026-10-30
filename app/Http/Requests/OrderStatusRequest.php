<?php

namespace App\Http\Requests;

use App\Models\SalesOrder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole(['administrador', 'vendedor', 'almacenero']) ?? false;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(array_keys(SalesOrder::STATUSES))],
            'comment' => ['nullable', 'string', 'max:300'],
        ];
    }
}
