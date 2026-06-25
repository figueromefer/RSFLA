<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $actor = $this->user();
        $target = $this->route('user');

        if (! $actor?->hasRole(User::ROLE_ADMIN, User::ROLE_STAFF)) {
            return false;
        }

        if ($actor->isStaff()) {
            if ($this->input('role') === User::ROLE_ADMIN) {
                return false;
            }

            if ($target?->isAdmin()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $target = $this->route('user');
        $passwordRules = $target
            ? ['nullable', 'confirmed', Password::defaults()]
            : ['required', 'confirmed', Password::defaults()];

        return [
            'name' => ['required', 'string', 'max:160'],
            'email' => ['required', 'email', 'max:160', Rule::unique('users', 'email')->ignore($target?->id)],
            'role' => ['required', 'string', Rule::in(User::ROLES)],
            'password' => $passwordRules,
            'is_active' => ['nullable', 'boolean'],
            'property_ids' => ['nullable', 'array'],
            'property_ids.*' => ['integer', 'exists:properties,id'],
        ];
    }

    /**
     * Return validated data mapped for persistence.
     *
     * @return array<string, mixed>
     */
    public function userData(): array
    {
        $data = $this->validated();
        unset($data['property_ids'], $data['password_confirmation']);

        $data['is_active'] = $this->boolean('is_active');

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        return $data;
    }

    /**
     * @return array<int>
     */
    public function propertyIds(): array
    {
        if ($this->validated('role') !== User::ROLE_CLIENT) {
            return [];
        }

        return array_map('intval', $this->validated('property_ids', []));
    }
}
