<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\AuthorizesAdminStaff;
use Illuminate\Foundation\Http\FormRequest;

class RentRollEntryRequest extends FormRequest
{
    use AuthorizesAdminStaff;

    public function rules(): array
    {
        return [
            'tenant_name' => ['nullable', 'required_unless:is_vacant,1', 'string', 'max:255'],
            'suite' => ['nullable', 'string', 'max:255'],
            'square_footage' => ['nullable', 'integer', 'min:0'],
            'lease_commencement_date' => ['nullable', 'date'],
            'lease_expiration_date' => ['nullable', 'date'],
            'lease_term' => ['nullable', 'string', 'max:255'],
            'start_rent' => ['nullable', 'string', 'max:255'],
            'rent_increases' => ['nullable', 'string', 'max:255'],
            'free_rent' => ['nullable', 'string', 'max:255'],
            'is_vacant' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_vacant' => $this->boolean('is_vacant')]);
    }
}
