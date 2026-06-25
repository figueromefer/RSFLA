<?php

namespace Tests\Feature;

use App\Models\MarketingActivity;
use App\Models\Property;
use App\Models\Prospect;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportsTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_cannot_access_reports_module(): void
    {
        $client = User::factory()->create([
            'role' => User::ROLE_CLIENT,
        ]);

        $this->actingAs($client)
            ->get(route('reports.index'))
            ->assertForbidden();
    }

    public function test_admin_and_staff_can_access_reports_module(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);
        $staff = User::factory()->create([
            'role' => User::ROLE_STAFF,
        ]);

        $this->actingAs($admin)->get(route('reports.index'))->assertOk();
        $this->actingAs($staff)->get(route('reports.index'))->assertOk();
    }

    public function test_reports_index_shows_properties(): void
    {
        $staff = User::factory()->create([
            'role' => User::ROLE_STAFF,
        ]);
        $property = $this->property();
        Prospect::create([
            'property_id' => $property->id,
            'first_name' => 'Visible Tenant',
            'tenant' => 'Visible Tenant',
            'status' => Prospect::STATUS_LEAD,
            'visible_to_client' => true,
            'is_active' => true,
        ]);
        MarketingActivity::create([
            'property_id' => $property->id,
            'type' => MarketingActivity::TYPE_BROADCAST_EMAIL,
            'title' => 'Visible broadcast',
            'activity_date' => '2026-06-25',
            'visible_to_client' => true,
        ]);

        $response = $this->actingAs($staff)->get(route('reports.index'));

        $response->assertOk();
        $response->assertSee('Utah Campus');
        $response->assertSee('Visible Prospects');
        $response->assertSee('Marketing');
        $response->assertSee('View Report');
    }

    public function test_internal_report_show_responds_for_admin_and_staff(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);
        $staff = User::factory()->create([
            'role' => User::ROLE_STAFF,
        ]);
        $property = $this->property();

        $this->actingAs($admin)
            ->get(route('reports.show', $property))
            ->assertOk()
            ->assertSee('Print / Export');

        $this->actingAs($staff)
            ->get(route('reports.show', $property))
            ->assertOk();
    }

    public function test_client_report_remains_protected_by_property_assignment(): void
    {
        $client = User::factory()->create([
            'role' => User::ROLE_CLIENT,
        ]);
        $assignedProperty = $this->property();
        $unassignedProperty = $this->property([
            'name' => 'Unassigned Property',
            'slug' => 'unassigned-property',
        ]);
        $assignedProperty->clients()->attach($client->id);

        $this->actingAs($client)
            ->get(route('client.properties.show', $assignedProperty))
            ->assertOk();

        $this->actingAs($client)
            ->get(route('client.properties.show', $unassignedProperty))
            ->assertForbidden();
    }

    public function test_print_button_appears_in_client_report(): void
    {
        $client = User::factory()->create([
            'role' => User::ROLE_CLIENT,
        ]);
        $property = $this->property();
        $property->clients()->attach($client->id);

        $this->actingAs($client)
            ->get(route('client.properties.show', $property))
            ->assertOk()
            ->assertSee('Print / Export');
    }

    public function test_client_report_shows_executive_summary(): void
    {
        $client = User::factory()->create([
            'role' => User::ROLE_CLIENT,
        ]);
        $property = $this->property();
        $property->clients()->attach($client->id);

        $this->actingAs($client)
            ->get(route('client.properties.show', $property))
            ->assertOk()
            ->assertSee('Executive Summary')
            ->assertSee('Current leasing position');
    }

    public function test_client_report_groups_pipeline_by_status(): void
    {
        $client = User::factory()->create([
            'role' => User::ROLE_CLIENT,
        ]);
        $property = $this->property();
        $property->clients()->attach($client->id);

        $this->prospect($property, [
            'tenant' => 'Lease Tenant',
            'status' => Prospect::STATUS_LEASE_SIGNED,
        ]);
        $this->prospect($property, [
            'tenant' => 'Proposal Tenant',
            'status' => Prospect::STATUS_PROPOSAL_SENT,
        ]);
        $this->prospect($property, [
            'tenant' => 'Tour Tenant',
            'status' => Prospect::STATUS_TOUR_SCHEDULED,
        ]);
        $this->prospect($property, [
            'tenant' => 'Active Tenant',
            'status' => Prospect::STATUS_PROSPECT,
        ]);
        $this->prospect($property, [
            'tenant' => 'Lead Tenant',
            'status' => Prospect::STATUS_LEAD,
        ]);
        $this->prospect($property, [
            'tenant' => 'Inactive Tenant',
            'status' => Prospect::STATUS_INACTIVE,
        ]);

        $this->actingAs($client)
            ->get(route('client.properties.show', $property))
            ->assertOk()
            ->assertSee('Pipeline Detail')
            ->assertSeeInOrder([
                'Lease',
                'Lease Tenant',
                'Proposals',
                'Proposal Tenant',
                'Tours',
                'Tour Tenant',
                'Active Prospects',
                'Active Tenant',
                'New Leads',
                'Lead Tenant',
                'Inactive',
                'Inactive Tenant',
            ]);
    }

    public function test_internal_prospects_do_not_appear_on_client_report(): void
    {
        $client = User::factory()->create([
            'role' => User::ROLE_CLIENT,
        ]);
        $property = $this->property();
        $property->clients()->attach($client->id);

        $this->prospect($property, [
            'tenant' => 'Visible Tenant',
            'visible_to_client' => true,
        ]);
        $this->prospect($property, [
            'tenant' => 'Internal Tenant',
            'visible_to_client' => false,
        ]);

        $this->actingAs($client)
            ->get(route('client.properties.show', $property))
            ->assertOk()
            ->assertSee('Visible Tenant')
            ->assertDontSee('Internal Tenant');
    }

    public function test_internal_reports_route_uses_polished_report_layout(): void
    {
        $staff = User::factory()->create([
            'role' => User::ROLE_STAFF,
        ]);
        $property = $this->property();

        $this->actingAs($staff)
            ->get(route('reports.show', $property))
            ->assertOk()
            ->assertSee('Executive Summary')
            ->assertSee('Pipeline Detail')
            ->assertSee('Monthly Activity');
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

    private function prospect(Property $property, array $overrides = []): Prospect
    {
        return Prospect::create(array_merge([
            'property_id' => $property->id,
            'first_name' => 'Visible Tenant',
            'tenant' => 'Visible Tenant',
            'suite' => '100',
            'use_type' => 'Retail',
            'timing' => 'Q3',
            'rsf' => 2500,
            'broker' => 'Broker Team',
            'status' => Prospect::STATUS_LEAD,
            'visible_to_client' => true,
            'is_active' => true,
        ], $overrides));
    }
}
