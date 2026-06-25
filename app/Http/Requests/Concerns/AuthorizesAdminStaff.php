<?php

namespace App\Http\Requests\Concerns;

trait AuthorizesAdminStaff
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin', 'staff') === true;
    }
}
