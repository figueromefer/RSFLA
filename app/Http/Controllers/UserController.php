<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only(['role', 'is_active', 'search']);

        $users = User::query()
            ->with('properties')
            ->when($filters['role'] ?? null, fn ($query, $role) => $query->where('role', $role))
            ->when(($filters['is_active'] ?? '') !== '', fn ($query) => $query->where('is_active', (bool) $filters['is_active']))
            ->when($filters['search'] ?? null, function ($query, $search): void {
                $query->where(function ($query) use ($search): void {
                    $query
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('role')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('users.index', [
            'users' => $users,
            'roles' => User::ROLES,
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        return view('users.create', [
            'user' => new User([
                'role' => User::ROLE_CLIENT,
                'is_active' => true,
            ]),
            'roles' => $this->availableRoles(),
            'properties' => Property::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $user = User::create($request->userData());
        $this->syncClientProperties($user, $request->propertyIds());

        return redirect()
            ->route('users.index')
            ->with('status', 'User created.');
    }

    public function edit(User $user): View
    {
        $this->authorizeStaffAccess($user);

        return view('users.edit', [
            'user' => $user->load('properties'),
            'roles' => $this->availableRoles($user),
            'properties' => Property::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $this->authorizeStaffAccess($user);

        $user->update($request->userData());
        $this->syncClientProperties($user, $request->propertyIds());

        return redirect()
            ->route('users.edit', $user)
            ->with('status', 'User updated.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->authorizeStaffAccess($user);

        abort_if($request->user()->is($user), 403);

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('status', 'User deleted.');
    }

    private function syncClientProperties(User $user, array $propertyIds): void
    {
        if (! $user->isClient()) {
            $user->properties()->sync([]);

            return;
        }

        $user->properties()->syncWithPivotValues($propertyIds, [
            'role' => 'owner',
            'receives_reports' => true,
        ]);
    }

    /**
     * @return array<int, string>
     */
    private function availableRoles(?User $target = null): array
    {
        if (auth()->user()->isAdmin()) {
            return User::ROLES;
        }

        if ($target?->isAdmin()) {
            return [];
        }

        return [User::ROLE_STAFF, User::ROLE_CLIENT];
    }

    private function authorizeStaffAccess(User $target): void
    {
        abort_if(auth()->user()->isStaff() && $target->isAdmin(), 403);
    }
}
