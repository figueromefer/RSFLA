<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\RentRollEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RentRollTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_and_staff_can_create_rent_roll_entries(): void
    {
        foreach ([User::ROLE_ADMIN, User::ROLE_STAFF] as $role) {
            $property = $this->property(["slug" => "property-{$role}"]);
            $user = User::factory()->create(["role" => $role]);

            $this->actingAs($user)->post(route('properties.rent-roll.store', $property), [
                'tenant_name' => "{$role} Tenant",
                'suite' => '200',
                'square_footage' => 14424,
                'lease_commencement_date' => '2022-01-01',
                'lease_expiration_date' => '2027-12-31',
                'lease_term' => '72 months',
                'start_rent' => '$24.00',
                'rent_increases' => '3% annually',
                'free_rent' => '2 months',
            ])->assertRedirect(route('properties.edit', $property));

            $this->assertDatabaseHas('rent_roll_entries', [
                'property_id' => $property->id,
                'tenant_name' => "{$role} Tenant",
                'lease_term' => '72 months',
                'start_rent' => '$24.00',
                'rent_increases' => '3% annually',
                'free_rent' => '2 months',
            ]);
        }
    }

    public function test_admin_and_staff_can_edit_rent_roll_entries(): void
    {
        foreach ([User::ROLE_ADMIN, User::ROLE_STAFF] as $role) {
            $property = $this->property(["slug" => "edit-{$role}"]);
            $entry = $property->rentRollEntries()->create(['tenant_name' => 'Old Tenant']);
            $user = User::factory()->create(["role" => $role]);

            $this->actingAs($user)->put(route('properties.rent-roll.update', [$property, $entry]), [
                'tenant_name' => "Updated {$role} Tenant",
                'square_footage' => 5056,
                'lease_commencement_date' => '2024-02-01',
                'lease_expiration_date' => '2029-01-31',
                'lease_term' => '60 months',
                'start_rent' => '$30.00',
                'rent_increases' => '2.5%',
                'free_rent' => 'None',
            ])->assertRedirect(route('properties.edit', $property));

            $this->assertDatabaseHas('rent_roll_entries', ['id' => $entry->id, 'lease_term' => '60 months', 'start_rent' => '$30.00']);
        }
    }

    public function test_admin_and_staff_can_delete_rent_roll_entries(): void
    {
        foreach ([User::ROLE_ADMIN, User::ROLE_STAFF] as $role) {
            $property = $this->property(["slug" => "delete-{$role}"]);
            $entry = $property->rentRollEntries()->create(['tenant_name' => 'Delete Tenant']);
            $user = User::factory()->create(["role" => $role]);

            $this->actingAs($user)->delete(route('properties.rent-roll.destroy', [$property, $entry]))
                ->assertRedirect(route('properties.edit', $property));
            $this->assertDatabaseMissing('rent_roll_entries', ['id' => $entry->id]);
        }
    }


    public function test_client_cannot_access_rent_roll_administration(): void
    {
        $client = User::factory()->create(['role' => User::ROLE_CLIENT]);
        $property = $this->property();
        $property->clients()->attach($client->id);

        $this->actingAs($client)->get(route('properties.rent-roll.create', $property))->assertForbidden();
        $this->actingAs($client)->post(route('properties.rent-roll.store', $property), ['tenant_name' => 'Blocked Tenant'])->assertForbidden();
        $this->assertDatabaseMissing('rent_roll_entries', ['tenant_name' => 'Blocked Tenant']);
    }

    public function test_report_displays_only_the_property_rent_roll_with_requested_formatting(): void
    {
        $client = User::factory()->create(['role' => User::ROLE_CLIENT]);
        $property = $this->property();
        $otherProperty = $this->property(['name' => 'Other Property', 'slug' => 'other-property']);
        $property->clients()->attach($client->id);
        $property->rentRollEntries()->create([
            'tenant_name' => 'Acme Corp',
            'suite' => '200',
            'square_footage' => 14424,
            'lease_commencement_date' => '2022-01-01',
            'lease_expiration_date' => '2027-12-31',
            'lease_term' => '72 months',
            'start_rent' => '$24.00',
            'rent_increases' => '3% annually',
            'free_rent' => '2 months',
        ]);
        $otherProperty->rentRollEntries()->create(['tenant_name' => 'Other Property Tenant']);

        $this->actingAs($client)->get(route('client.properties.show', $property))
            ->assertOk()
            ->assertSee('Rent Roll')
            ->assertSee('Acme Corp')
            ->assertSee('14,424')
            ->assertSee('01-01-2022')
            ->assertSee('12-31-2027')
            ->assertSee('72 months')
            ->assertDontSee('Other Property Tenant');
    }

    public function test_report_sorts_occupied_entries_before_vacant_suites(): void
    {
        $client = User::factory()->create(['role' => User::ROLE_CLIENT]);
        $property = $this->property();
        $property->clients()->attach($client->id);
        $property->rentRollEntries()->create(['suite' => '100', 'square_footage' => 1897, 'is_vacant' => true]);
        $property->rentRollEntries()->create(['tenant_name' => 'Occupied Company', 'suite' => '900', 'sort_order' => 10]);

        $response = $this->actingAs($client)->get(route('client.properties.show', $property));

        $response->assertOk()->assertSeeInOrder(['Occupied Company', 'Vacant'])->assertSee('1,897');
    }

    public function test_report_shows_restrained_empty_state(): void
    {
        $client = User::factory()->create(['role' => User::ROLE_CLIENT]);
        $property = $this->property();
        $property->clients()->attach($client->id);

        $this->actingAs($client)->get(route('client.properties.show', $property))
            ->assertOk()->assertSee('No rent roll data available.');
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
