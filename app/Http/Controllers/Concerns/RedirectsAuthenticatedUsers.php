<?php

namespace App\Http\Controllers\Concerns;

use App\Models\User;

trait RedirectsAuthenticatedUsers
{
    protected function redirectPathFor(User $user): string
    {
        if (! $user->isClient()) {
            return route('dashboard');
        }

        if ($user->properties()->count() === 1) {
            return route('client.properties.show', $user->properties()->first());
        }

        return route('client.properties');
    }
}
