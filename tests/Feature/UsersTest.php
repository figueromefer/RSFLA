<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UsersTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_cannot_access_users_module(): void
    {
        $client = User::factory()->create([
            'role' => User::ROLE_CLIENT,
        ]);

        $this->actingAs($client)
            ->get('/users')
            ->assertForbidden();
    }

    public function test_staff_and_admin_can_access_users_module(): void
    {
        $staff = User::factory()->create([
            'role' => User::ROLE_STAFF,
        ]);
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $this->actingAs($staff)->get(route('users.index'))->assertOk();
        $this->actingAs($admin)->get(route('users.index'))->assertOk();
    }

    public function test_admin_can_create_client_with_properties(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);
        $property = $this->property();

        $this->actingAs($admin)
            ->post(route('users.store'), [
                'name' => 'Client Owner',
                'email' => 'owner@example.com',
                'role' => User::ROLE_CLIENT,
                'password' => 'password',
                'password_confirmation' => 'password',
                'is_active' => '1',
                'property_ids' => [$property->id],
            ])
            ->assertRedirect(route('users.index'));

        $client = User::where('email', 'owner@example.com')->first();

        $this->assertNotNull($client);
        $this->assertTrue($client->properties()->whereKey($property->id)->exists());
    }

    public function test_created_client_can_login_and_only_see_assigned_properties(): void
    {
        $property = $this->property();
        $otherProperty = $this->property([
            'name' => 'Other Property',
            'slug' => 'other-property',
        ]);
        $client = User::factory()->create([
            'name' => 'Client Owner',
            'email' => 'owner@example.com',
            'role' => User::ROLE_CLIENT,
            'password' => Hash::make('password'),
        ]);
        $client->properties()->attach($property->id, [
            'role' => 'owner',
            'receives_reports' => true,
        ]);

        $this->post(route('login.store'), [
            'email' => 'owner@example.com',
            'password' => 'password',
        ])->assertRedirect(route('client.properties.show', $property));

        $this->actingAs($client)
            ->get(route('client.properties.show', $property))
            ->assertOk();

        $this->actingAs($client)
            ->get(route('client.properties.show', $otherProperty))
            ->assertForbidden();
    }

    public function test_inactive_user_cannot_login(): void
    {
        User::factory()->create([
            'email' => 'inactive@example.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_CLIENT,
            'is_active' => false,
        ]);

        $this->from(route('login'))
            ->post(route('login.store'), [
                'email' => 'inactive@example.com',
                'password' => 'password',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors([
                'email' => 'This account is inactive. Please contact RSFLA for access.',
            ]);
    }

    public function test_staff_cannot_create_edit_or_delete_admins(): void
    {
        $staff = User::factory()->create([
            'role' => User::ROLE_STAFF,
        ]);
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $this->actingAs($staff)
            ->post(route('users.store'), [
                'name' => 'New Admin',
                'email' => 'new-admin@example.com',
                'role' => User::ROLE_ADMIN,
                'password' => 'password',
                'password_confirmation' => 'password',
                'is_active' => '1',
            ])
            ->assertForbidden();

        $this->actingAs($staff)
            ->get(route('users.edit', $admin))
            ->assertForbidden();

        $this->actingAs($staff)
            ->put(route('users.update', $admin), [
                'name' => 'Changed Admin',
                'email' => $admin->email,
                'role' => User::ROLE_ADMIN,
                'is_active' => '1',
            ])
            ->assertForbidden();

        $this->actingAs($staff)
            ->delete(route('users.destroy', $admin))
            ->assertForbidden();
    }

    public function test_user_cannot_delete_self(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $this->actingAs($admin)
            ->delete(route('users.destroy', $admin))
            ->assertForbidden();
    }

    private function property(array $overrides = []): Property
    {
        return Property::create(array_merge([
            'name' => 'Utah Campus',
            'slug' => 'utah-campus',
            'city' => 'Salt Lake City',
            'state' => 'UT',
            'is_active' => true,
        ], $overrides));
    }
}
