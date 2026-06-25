<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\AuthorizesAdminStaff;
use App\Models\MarketingActivity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MarketingActivityRequest extends FormRequest
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
            'type' => ['required', 'string', Rule::in(MarketingActivity::TYPES)],
            'title' => ['required', 'string', 'max:180'],
            'description' => ['nullable', 'string', 'max:5000'],
            'activity_date' => ['required', 'date'],
            'metric_label' => ['nullable', 'string', 'max:120'],
            'metric_value' => ['nullable', 'string', 'max:120'],
            'url' => ['nullable', 'url', 'max:2048'],
            'visible_to_client' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Return validated data mapped for persistence.
     *
     * @return array<string, mixed>
     */
    public function marketingActivityData(): array
    {
        $data = $this->validated();
        $data['visible_to_client'] = $this->boolean('visible_to_client');

        return $data;
    }
}
