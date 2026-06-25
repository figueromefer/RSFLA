<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\AuthorizesAdminStaff;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PropertyRequest extends FormRequest
{
    use AuthorizesAdminStaff;

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if (! $this->filled('slug') && $this->filled('name')) {
            $this->merge([
                'slug' => Str::slug($this->string('name')->toString()),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $property = $this->route('property');

        return [
            'name' => ['required', 'string', 'max:160'],
            'slug' => [
                'required',
                'string',
                'max:180',
                'alpha_dash:ascii',
                Rule::unique('properties', 'slug')->ignore($property?->id),
            ],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:120'],
            'state' => ['required', 'string', 'size:2'],
            'hero_image' => ['nullable', 'url', 'max:2048'],
            'report_title' => ['nullable', 'string', 'max:180'],
            'is_active' => ['nullable', 'boolean'],
            'team_member_ids' => ['nullable', 'array'],
            'team_member_ids.*' => ['integer', 'exists:team_members,id'],
        ];
    }

    /**
     * Return validated data mapped to database column names.
     *
     * @return array<string, mixed>
     */
    public function propertyData(): array
    {
        $data = $this->validated();
        $data['street_address'] = $data['address'] ?? null;
        unset($data['address']);

        $data['state'] = strtoupper($data['state']);
        $data['is_active'] = $this->boolean('is_active');
        $data['status'] = $data['is_active'] ? 'active' : 'inactive';
        unset($data['team_member_ids']);

        return $data;
    }

    /**
     * @return array<int>
     */
    public function teamMemberIds(): array
    {
        return array_map('intval', $this->validated('team_member_ids', []));
    }
}
