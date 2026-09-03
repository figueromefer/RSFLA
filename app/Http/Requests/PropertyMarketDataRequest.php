<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\AuthorizesAdminStaff;
use Illuminate\Foundation\Http\FormRequest;

class PropertyMarketDataRequest extends FormRequest
{
    use AuthorizesAdminStaff;

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'report_date' => ['nullable', 'date'],
            'image' => [
                $this->isMethod('post') ? 'required' : 'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:10240',
            ],
        ];
    }
}
