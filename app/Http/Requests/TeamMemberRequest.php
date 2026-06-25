<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\AuthorizesAdminStaff;
use Illuminate\Foundation\Http\FormRequest;

class TeamMemberRequest extends FormRequest
{
    use AuthorizesAdminStaff;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:160'],
            'dre' => ['nullable', 'string', 'max:80'],
            'phone' => ['nullable', 'string', 'max:80'],
            'email' => ['nullable', 'email', 'max:160'],
            'bio_url' => ['nullable', 'url', 'max:2048'],
            'photo' => ['nullable', 'string', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Return validated data mapped for persistence.
     *
     * @return array<string, mixed>
     */
    public function teamMemberData(): array
    {
        $data = $this->validated();
        $data['is_active'] = $this->boolean('is_active');
        $data['title'] = 'Team Member';

        return $data;
    }
}
