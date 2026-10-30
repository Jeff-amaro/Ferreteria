<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $supplier = $this->route('supplier');

        return [
            'ruc' => ['required', 'digits:11', Rule::unique('suppliers', 'ruc')->ignore($supplier?->id)],
            'business_name' => ['required', 'string', 'max:150'],
            'contact_name' => ['nullable', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'address' => ['nullable', 'string', 'max:180'],
            'lead_time_days' => ['required', 'integer', 'min:1', 'max:90'],
            'active' => ['boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'active' => $this->boolean('active'),
            'ruc' => preg_replace('/\D/', '', (string) $this->input('ruc')),
        ]);
    }
}
