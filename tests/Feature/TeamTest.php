<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_cannot_access_team_module(): void
    {
        $client = User::factory()->create([
            'role' => User::ROLE_CLIENT,
        ]);

        $this->actingAs($client)
            ->get('/team')
            ->assertForbidden();
    }

    public function test_admin_and_staff_can_access_team_module(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);
        $staff = User::factory()->create([
            'role' => User::ROLE_STAFF,
        ]);

        $this->actingAs($admin)->get(route('team.index'))->assertOk();
        $this->actingAs($staff)->get(route('team.index'))->assertOk();
    }

    public function test_creating_team_member_works(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $this->actingAs($admin)
            ->post(route('team.store'), [
                'name' => 'Taylor Advisor',
                'dre' => 'DRE 1234567',
                'phone' => '555-1111',
                'email' => 'taylor@example.com',
                'bio_url' => 'https://example.com/taylor',
                'photo' => 'https://example.com/taylor.jpg',
                'is_active' => '1',
            ])
            ->assertRedirect(route('team.index'));

        $this->assertDatabaseHas('team_members', [
            'name' => 'Taylor Advisor',
            'dre' => 'DRE 1234567',
            'email' => 'taylor@example.com',
            'is_active' => true,
        ]);
    }

    public function test_editing_team_member_works(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);
        $teamMember = $this->teamMember();

        $this->actingAs($admin)
            ->put(route('team.update', $teamMember), [
                'name' => 'Updated Advisor',
                'dre' => 'DRE 7654321',
                'phone' => '555-2222',
                'email' => 'updated@example.com',
                'bio_url' => 'https://example.com/updated',
                'photo' => 'https://example.com/updated.jpg',
                'is_active' => '0',
            ])
            ->assertRedirect(route('team.edit', $teamMember));

        $this->assertDatabaseHas('team_members', [
            'id' => $teamMember->id,
            'name' => 'Updated Advisor',
            'is_active' => false,
        ]);
    }

    public function test_deleting_team_member_works(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);
        $teamMember = $this->teamMember();

        $this->actingAs($admin)
            ->delete(route('team.destroy', $teamMember))
            ->assertRedirect(route('team.index'));

        $this->assertDatabaseMissing('team_members', [
            'id' => $teamMember->id,
        ]);
    }

    public function test_client_report_shows_assigned_active_team_members(): void
    {
        $client = User::factory()->create([
            'role' => User::ROLE_CLIENT,
        ]);
        $property = $this->property();
        $property->clients()->attach($client->id);
        $activeMember = $this->teamMember([
            'name' => 'Active Advisor',
            'is_active' => true,
        ]);
        $inactiveMember = $this->teamMember([
            'name' => 'Inactive Advisor',
            'is_active' => false,
        ]);
        $property->teamMembers()->sync([$activeMember->id, $inactiveMember->id]);

        $response = $this->actingAs($client)
            ->get(route('client.properties.show', $property));

        $response->assertOk();
        $response->assertSee('Active Advisor');
        $response->assertDontSee('Inactive Advisor');
    }

    private function teamMember(array $overrides = []): TeamMember
    {
        return TeamMember::create(array_merge([
            'name' => 'Taylor Advisor',
            'dre' => 'DRE 1234567',
            'phone' => '555-1111',
            'email' => 'taylor@example.com',
            'title' => 'Team Member',
            'is_active' => true,
        ], $overrides));
    }

    private function property(): Property
    {
        return Property::create([
            'name' => 'Utah Campus',
            'slug' => 'utah-campus',
            'city' => 'Salt Lake City',
            'state' => 'UT',
            'is_active' => true,
        ]);
    }
}
