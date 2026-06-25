<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\Prospect;
use App\Models\ProspectActivity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PipelineTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_cannot_access_pipeline(): void
    {
        $client = User::factory()->create([
            'role' => User::ROLE_CLIENT,
        ]);

        $this->actingAs($client)
            ->get('/pipeline')
            ->assertForbidden();
    }

    public function test_staff_and_admin_can_access_pipeline(): void
    {
        $staff = User::factory()->create([
            'role' => User::ROLE_STAFF,
        ]);

        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $this->actingAs($staff)
            ->get('/pipeline')
            ->assertOk();

        $this->actingAs($admin)
            ->get('/pipeline')
            ->assertOk();
    }

    public function test_creating_prospect_generates_activity(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);
        $property = Property::create([
            'name' => 'Utah Campus',
            'slug' => 'utah-campus',
            'city' => 'Salt Lake City',
            'state' => 'UT',
        ]);

        $response = $this->actingAs($admin)->post(route('pipeline.store'), [
            'property_id' => $property->id,
            'status' => Prospect::STATUS_LEAD,
            'suite' => 'Suite 100',
            'tenant' => 'Test Tenant',
            'use' => 'Retail',
            'timing' => 'Q3',
            'rsf' => 2500,
            'broker' => 'Test Broker',
            'contact_name' => 'Taylor Contact',
            'email' => 'tenant@example.com',
            'phone' => '555-0100',
            'notes' => 'New opportunity.',
            'visible_to_client' => '1',
            'sort_order' => 1,
        ]);

        $prospect = Prospect::first();

        $response->assertRedirect(route('pipeline.index'));
        $this->assertDatabaseHas('prospect_activities', [
            'prospect_id' => $prospect->id,
            'type' => ProspectActivity::TYPE_CREATED,
            'status_to' => Prospect::STATUS_LEAD,
        ]);
    }

    public function test_hidden_prospect_is_not_counted_in_client_report(): void
    {
        $client = User::factory()->create([
            'role' => User::ROLE_CLIENT,
        ]);
        $property = Property::create([
            'name' => 'Utah Campus',
            'slug' => 'utah-campus',
            'city' => 'Salt Lake City',
            'state' => 'UT',
            'unit_count' => 100,
            'is_active' => true,
        ]);
        $property->clients()->attach($client->id);

        $visibleProspect = Prospect::create([
            'property_id' => $property->id,
            'first_name' => 'Visible Tenant',
            'tenant' => 'Visible Tenant',
            'status' => Prospect::STATUS_LEAD,
            'visible_to_client' => true,
            'is_active' => true,
        ]);
        $hiddenProspect = Prospect::create([
            'property_id' => $property->id,
            'first_name' => 'Hidden Tenant',
            'tenant' => 'Hidden Tenant',
            'status' => Prospect::STATUS_LEASE_SIGNED,
            'visible_to_client' => false,
            'is_active' => true,
        ]);
        ProspectActivity::create([
            'prospect_id' => $visibleProspect->id,
            'property_id' => $property->id,
            'type' => ProspectActivity::TYPE_CREATED,
            'subject' => 'Visible tenant added',
            'occurred_at' => now(),
        ]);
        ProspectActivity::create([
            'prospect_id' => $hiddenProspect->id,
            'property_id' => $property->id,
            'type' => ProspectActivity::TYPE_CREATED,
            'subject' => 'Hidden tenant added',
            'occurred_at' => now(),
        ]);

        $response = $this->actingAs($client)
            ->get(route('client.properties.show', $property));

        $response->assertOk();
        $response->assertSee('Visible Tenant');
        $response->assertDontSee('Hidden Tenant');
        $response->assertDontSee('Hidden tenant added');
        $response->assertSee('0 signed leases');
    }

    public function test_client_cannot_access_another_clients_property_report(): void
    {
        $client = User::factory()->create([
            'role' => User::ROLE_CLIENT,
        ]);
        $property = Property::create([
            'name' => 'Utah Campus',
            'slug' => 'utah-campus',
            'city' => 'Salt Lake City',
            'state' => 'UT',
        ]);

        $this->actingAs($client)
            ->get(route('client.properties.show', $property))
            ->assertForbidden();
    }

    public function test_changing_status_generates_status_activity(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);
        $property = Property::create([
            'name' => 'Utah Campus',
            'slug' => 'utah-campus',
            'city' => 'Salt Lake City',
            'state' => 'UT',
        ]);
        $prospect = Prospect::create([
            'property_id' => $property->id,
            'first_name' => 'Test Tenant',
            'tenant' => 'Test Tenant',
            'status' => Prospect::STATUS_LEAD,
            'is_active' => true,
        ]);

        $this->actingAs($admin)->put(route('pipeline.update', $prospect), [
            'property_id' => $property->id,
            'status' => Prospect::STATUS_PROPOSAL_SENT,
            'suite' => 'Suite 100',
            'tenant' => 'Test Tenant',
            'use' => 'Retail',
            'timing' => 'Q3',
            'rsf' => 2500,
            'broker' => 'Test Broker',
            'contact_name' => 'Taylor Contact',
            'email' => 'tenant@example.com',
            'phone' => '555-0100',
            'notes' => 'Moved forward.',
            'visible_to_client' => '1',
            'sort_order' => 1,
        ])->assertRedirect(route('pipeline.edit', $prospect));

        $this->assertDatabaseHas('prospect_activities', [
            'prospect_id' => $prospect->id,
            'type' => ProspectActivity::TYPE_STATUS_CHANGE,
            'status_from' => Prospect::STATUS_LEAD,
            'status_to' => Prospect::STATUS_PROPOSAL_SENT,
        ]);
    }
}
