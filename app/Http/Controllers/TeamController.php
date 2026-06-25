<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeamMemberRequest;
use App\Models\TeamMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeamController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'is_active']);

        $teamMembers = TeamMember::query()
            ->withCount('properties')
            ->when($filters['search'] ?? null, function ($query, $search): void {
                $query->where(function ($query) use ($search): void {
                    $query
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('dre', 'like', "%{$search}%");
                });
            })
            ->when(($filters['is_active'] ?? '') !== '', fn ($query) => $query->where('is_active', (bool) $filters['is_active']))
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('team.index', [
            'teamMembers' => $teamMembers,
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        return view('team.create', [
            'teamMember' => new TeamMember([
                'is_active' => true,
            ]),
        ]);
    }

    public function store(TeamMemberRequest $request): RedirectResponse
    {
        TeamMember::create($request->teamMemberData());

        return redirect()
            ->route('team.index')
            ->with('status', 'Team member created.');
    }

    public function edit(TeamMember $teamMember): View
    {
        return view('team.edit', [
            'teamMember' => $teamMember,
        ]);
    }

    public function update(TeamMemberRequest $request, TeamMember $teamMember): RedirectResponse
    {
        $teamMember->update($request->teamMemberData());

        return redirect()
            ->route('team.edit', $teamMember)
            ->with('status', 'Team member updated.');
    }

    public function destroy(TeamMember $teamMember): RedirectResponse
    {
        $teamMember->delete();

        return redirect()
            ->route('team.index')
            ->with('status', 'Team member deleted.');
    }
}
