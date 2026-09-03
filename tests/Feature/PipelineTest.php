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
            'opportunity_date' => '2026-07-15',
            'suite' => 'Suite 100',
            'tenant' => 'Test Tenant',
            'use' => 'Retail',
            'timing' => 'Q3',
            'rsf' => 2500,
            'broker' => 'Test Broker',
            'public_notes' => 'Client-facing update.',
            'visible_to_client' => '1',
        ]);

        $prospect = Prospect::first();

        $response->assertRedirect(route('pipeline.index'));
        $this->assertDatabaseHas('prospect_activities', [
            'prospect_id' => $prospect->id,
            'type' => ProspectActivity::TYPE_CREATED,
            'status_to' => Prospect::STATUS_LEAD,
        ]);
        $prospect->refresh();
        $this->assertSame('2026-07-15', $prospect->opportunity_date->format('Y-m-d'));
        $this->assertSame('Client-facing update.', $prospect->public_notes);
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
        $this->assertSame(0, $response->viewData('metrics')['leases']);
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
            'company' => 'Preserved Private Company',
            'contact_name' => 'Preserved Contact',
            'email' => 'preserved@example.com',
            'phone' => '555-0100',
            'notes' => 'Preserved private note.',
            'sort_order' => 7,
            'is_active' => true,
        ]);

        $this->actingAs($admin)->put(route('pipeline.update', $prospect), [
            'property_id' => $property->id,
            'status' => Prospect::STATUS_PROPOSAL_SENT,
            'opportunity_date' => '2026-08-01',
            'suite' => 'Suite 100',
            'tenant' => 'Test Tenant',
            'use' => 'Retail',
            'timing' => 'Q3',
            'rsf' => 2500,
            'broker' => 'Test Broker',
            'public_notes' => 'Updated public note.',
            'visible_to_client' => '1',
        ])->assertRedirect(route('pipeline.edit', $prospect));

        $this->assertDatabaseHas('prospect_activities', [
            'prospect_id' => $prospect->id,
            'type' => ProspectActivity::TYPE_STATUS_CHANGE,
            'status_from' => Prospect::STATUS_LEAD,
            'status_to' => Prospect::STATUS_PROPOSAL_SENT,
        ]);
        $prospect->refresh();
        $this->assertSame('2026-08-01', $prospect->opportunity_date->format('Y-m-d'));
        $this->assertSame('Preserved Private Company', $prospect->company);
        $this->assertSame('Preserved Contact', $prospect->contact_name);
        $this->assertSame('Preserved private note.', $prospect->notes);
        $this->assertSame(7, $prospect->sort_order);
        $this->assertSame('Updated public note.', $prospect->public_notes);
    }

    public function test_leasing_activity_hides_private_contact_information(): void
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
        Prospect::create([
            'property_id' => $property->id,
            'first_name' => 'Internal Tenant',
            'tenant' => 'Internal Tenant',
            'status' => Prospect::STATUS_LEAD,
            'opportunity_date' => '2026-07-15',
            'company' => 'Internal Company LLC',
            'contact_name' => 'Private Contact',
            'email' => 'private-contact@example.com',
            'phone' => '555-0199',
            'public_notes' => 'Public pipeline note.',
            'notes' => 'Private pipeline note.',
            'visible_to_client' => true,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('pipeline.index'))
            ->assertOk()
            ->assertSee('07-15-2026')
            ->assertSee('Public pipeline note.')
            ->assertDontSee('Internal Company LLC')
            ->assertDontSee('Private Contact')
            ->assertDontSee('private-contact@example.com')
            ->assertDontSee('555-0199')
            ->assertDontSee('Private pipeline note.');
    }

    public function test_normal_leasing_activity_form_only_contains_report_fields(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->actingAs($admin)->get(route('pipeline.create'))
            ->assertOk()
            ->assertSee('Leasing Activity')
            ->assertSee('name="public_notes"', false)
            ->assertSee('Show in client report')
            ->assertDontSee('name="company"', false)
            ->assertDontSee('name="contact_name"', false)
            ->assertDontSee('name="email"', false)
            ->assertDontSee('name="phone"', false)
            ->assertDontSee('name="notes"', false)
            ->assertDontSee('name="sort_order"', false);
    }
}
