<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RedirectsAuthenticatedUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    use RedirectsAuthenticatedUsers;

    public function __invoke(Request $request): RedirectResponse
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        return redirect($this->redirectPathFor($request->user()));
    }
}
