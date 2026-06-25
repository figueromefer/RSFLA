<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\AuthorizesAdminStaff;
use App\Models\Prospect;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PipelineProspectRequest extends FormRequest
{
    use AuthorizesAdminStaff;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'property_id' => ['required', 'integer', 'exists:properties,id'],
            'status' => ['required', 'string', Rule::in(Prospect::STATUSES)],
            'suite' => ['nullable', 'string', 'max:120'],
            'tenant' => ['required', 'string', 'max:160'],
            'use' => ['nullable', 'string', 'max:160'],
            'timing' => ['nullable', 'string', 'max:160'],
            'rsf' => ['nullable', 'integer', 'min:0', 'max:10000000'],
            'broker' => ['nullable', 'string', 'max:160'],
            'contact_name' => ['nullable', 'string', 'max:160'],
            'email' => ['nullable', 'email', 'max:160'],
            'phone' => ['nullable', 'string', 'max:80'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'visible_to_client' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
        ];
    }

    /**
     * Return validated data mapped to database column names.
     *
     * @return array<string, mixed>
     */
    public function prospectData(): array
    {
        $data = $this->validated();

        $data['use_type'] = $data['use'] ?? null;
        unset($data['use']);

        $data['visible_to_client'] = $this->boolean('visible_to_client');
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['first_name'] = $data['contact_name'] ?: $data['tenant'];
        $data['last_name'] = null;
        $data['company'] = $data['tenant'];
        $data['is_active'] = $data['status'] !== Prospect::STATUS_INACTIVE;
        $data['last_contacted_at'] = now();

        return $data;
    }
}
