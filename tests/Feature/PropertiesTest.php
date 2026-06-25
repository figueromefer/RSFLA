<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\Prospect;
use App\Models\ProspectActivity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertiesTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_cannot_access_properties_module(): void
    {
        $client = User::factory()->create([
            'role' => User::ROLE_CLIENT,
        ]);

        $this->actingAs($client)
            ->get('/properties')
            ->assertForbidden();
    }

    public function test_staff_and_admin_can_access_properties_module(): void
    {
        $staff = User::factory()->create([
            'role' => User::ROLE_STAFF,
        ]);
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $this->actingAs($staff)->get('/properties')->assertOk();
        $this->actingAs($admin)->get('/properties')->assertOk();
    }

    public function test_creating_property_works(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $this->actingAs($admin)
            ->post(route('properties.store'), [
                'name' => 'Campus Plaza',
                'slug' => 'campus-plaza',
                'address' => '100 Main St',
                'city' => 'Salt Lake City',
                'state' => 'UT',
                'hero_image' => 'https://example.com/campus.jpg',
                'report_title' => 'Campus Plaza Report',
                'is_active' => '1',
            ])
            ->assertRedirect(route('properties.index'));

        $this->assertDatabaseHas('properties', [
            'name' => 'Campus Plaza',
            'slug' => 'campus-plaza',
            'street_address' => '100 Main St',
            'is_active' => true,
        ]);
    }

    public function test_slug_is_generated_when_blank(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $this->actingAs($admin)
            ->post(route('properties.store'), [
                'name' => 'South Valley Retail',
                'slug' => '',
                'address' => '200 State St',
                'city' => 'Sandy',
                'state' => 'UT',
                'is_active' => '1',
            ])
            ->assertRedirect(route('properties.index'));

        $this->assertDatabaseHas('properties', [
            'name' => 'South Valley Retail',
            'slug' => 'south-valley-retail',
        ]);
    }

    public function test_client_cannot_view_inactive_property_report(): void
    {
        $client = User::factory()->create([
            'role' => User::ROLE_CLIENT,
        ]);
        $property = Property::create([
            'name' => 'Inactive Property',
            'slug' => 'inactive-property',
            'city' => 'Salt Lake City',
            'state' => 'UT',
            'is_active' => false,
            'status' => Property::STATUS_INACTIVE,
        ]);
        $property->clients()->attach($client->id);

        $this->actingAs($client)
            ->get(route('client.properties.show', $property))
            ->assertForbidden();
    }

    public function test_properties_index_shows_basic_metrics(): void
    {
        $staff = User::factory()->create([
            'role' => User::ROLE_STAFF,
        ]);
        $property = Property::create([
            'name' => 'Utah Campus',
            'slug' => 'utah-campus',
            'city' => 'Salt Lake City',
            'state' => 'UT',
            'is_active' => true,
        ]);
        Prospect::create([
            'property_id' => $property->id,
            'first_name' => 'Visible Tenant',
            'tenant' => 'Visible Tenant',
            'status' => Prospect::STATUS_LEAD,
            'visible_to_client' => true,
            'is_active' => true,
        ]);
        Prospect::create([
            'property_id' => $property->id,
            'first_name' => 'Hidden Tenant',
            'tenant' => 'Hidden Tenant',
            'status' => Prospect::STATUS_LEAD,
            'visible_to_client' => false,
            'is_active' => true,
        ]);

        $response = $this->actingAs($staff)->get(route('properties.index'));

        $response->assertOk();
        $response->assertSee('Utah Campus');
        $response->assertSee('Total Prospects');
        $response->assertSee('Visible Prospects');
        $response->assertSee('2');
        $response->assertSee('1');
    }

    public function test_admin_and_staff_can_view_property_detail(): void
    {
        $staff = User::factory()->create([
            'role' => User::ROLE_STAFF,
        ]);
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);
        $property = Property::create([
            'name' => 'Utah Campus',
            'slug' => 'utah-campus',
            'city' => 'Salt Lake City',
            'state' => 'UT',
            'is_active' => true,
        ]);

        $this->actingAs($staff)
            ->get(route('properties.show', $property))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('properties.show', $property))
            ->assertOk();
    }

    public function test_client_cannot_view_internal_property_detail(): void
    {
        $client = User::factory()->create([
            'role' => User::ROLE_CLIENT,
        ]);
        $property = Property::create([
            'name' => 'Utah Campus',
            'slug' => 'utah-campus',
            'city' => 'Salt Lake City',
            'state' => 'UT',
            'is_active' => true,
        ]);
        $property->clients()->attach($client->id);

        $this->actingAs($client)
            ->get(route('properties.show', $property))
            ->assertForbidden();
    }

    public function test_property_detail_shows_only_that_property_prospects_and_activity(): void
    {
        $staff = User::factory()->create([
            'role' => User::ROLE_STAFF,
        ]);
        $property = Property::create([
            'name' => 'Utah Campus',
            'slug' => 'utah-campus',
            'city' => 'Salt Lake City',
            'state' => 'UT',
            'is_active' => true,
        ]);
        $otherProperty = Property::create([
            'name' => 'Other Property',
            'slug' => 'other-property',
            'city' => 'Provo',
            'state' => 'UT',
            'is_active' => true,
        ]);
        $prospect = Prospect::create([
            'property_id' => $property->id,
            'first_name' => 'Visible Tenant',
            'tenant' => 'Visible Tenant',
            'status' => Prospect::STATUS_LEAD,
            'visible_to_client' => true,
            'is_active' => true,
        ]);
        $otherProspect = Prospect::create([
            'property_id' => $otherProperty->id,
            'first_name' => 'Other Tenant',
            'tenant' => 'Other Tenant',
            'status' => Prospect::STATUS_LEAD,
            'visible_to_client' => true,
            'is_active' => true,
        ]);
        ProspectActivity::create([
            'property_id' => $property->id,
            'prospect_id' => $prospect->id,
            'type' => ProspectActivity::TYPE_CREATED,
            'subject' => 'Visible activity',
            'occurred_at' => now(),
        ]);
        ProspectActivity::create([
            'property_id' => $otherProperty->id,
            'prospect_id' => $otherProspect->id,
            'type' => ProspectActivity::TYPE_CREATED,
            'subject' => 'Other activity',
            'occurred_at' => now(),
        ]);

        $response = $this->actingAs($staff)
            ->get(route('properties.show', $property));

        $response->assertOk();
        $response->assertSee('Visible Tenant');
        $response->assertSee('Visible activity');
        $response->assertDontSee('Other Tenant');
        $response->assertDontSee('Other activity');
    }
}
